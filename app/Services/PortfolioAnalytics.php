<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Contract;
use App\Models\Project;
use App\Models\Resource;
use App\Models\Risk;
use App\Models\StatusReport;
use App\Models\Timesheet;
use App\Models\WbsItem;
use App\Support\Pmbok;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

/** Agregasi portofolio untuk dasbor: EVM, kurva S, kesehatan, risiko, sumber daya, pengadaan. */
class PortfolioAnalytics
{
    /** @var array<string,Collection> per konteks perusahaan demo */
    protected static array $projects = [];

    public static function flush(): void
    {
        static::$projects = [];
    }

    /** @return Collection<int,Project> */
    public static function projects(): Collection
    {
        return static::$projects[\App\Support\DemoCompany::current() ?? '*'] ??= Project::with(['wbsItems', 'portfolio', 'manager'])->orderBy('code')->get();
    }

    public static function active(): Collection
    {
        return static::projects()->whereIn('status', ['aktif', 'ditunda']);
    }

    public static function statusDate(): CarbonInterface
    {
        return static::projects()->map(fn ($p) => $p->statusDate())->max() ?? Carbon::today();
    }

    /** @return array<string,float|int|null> */
    public static function totals(): array
    {
        $act = static::active();
        $e = $act->map(fn ($p) => $p->evm());
        $pv = $e->sum('pv');
        $ev = $e->sum('ev');
        $ac = $e->sum('ac');

        return [
            'projects' => static::projects()->count(),
            'active' => $act->count(),
            'bac' => $e->sum('bac'),
            'eac' => $e->sum('eac'),
            'pv' => $pv, 'ev' => $ev, 'ac' => $ac,
            'spi' => $pv > 0 ? $ev / $pv : null,
            'cpi' => $ac > 0 ? $ev / $ac : null,
            'red' => $act->filter(fn ($p) => $p->health() === 'merah')->count(),
            'high_risks' => Risk::whereIn('status', ['terbuka', 'dipantau', 'terjadi'])->whereRaw('probability * impact >= 15')->count(),
            'open_risks' => Risk::whereIn('status', ['terbuka', 'dipantau', 'terjadi'])->count(),
            'pending_cr' => \App\Models\ChangeRequest::whereIn('status', ['diajukan', 'dianalisis'])->count(),
            'open_issues' => \App\Models\Issue::where('status', '!=', 'selesai')->count(),
        ];
    }

    /** Kurva S portofolio bulanan: PV dari baseline WBS, EV & AC dari laporan status bulanan. */
    public static function sCurve(?Project $only = null): array
    {
        $projects = $only ? collect([$only->loadMissing('wbsItems')]) : static::active();
        if ($projects->isEmpty()) {
            return ['labels' => [], 'pv' => [], 'ev' => [], 'ac' => []];
        }
        $wp = $projects->flatMap(fn ($p) => $p->workPackages());
        $start = Carbon::parse($wp->min('baseline_start') ?? now())->startOfMonth();
        $statusDate = $only ? $only->statusDate() : static::statusDate();
        $end = Carbon::parse(collect([$wp->max('baseline_finish'), $wp->max('finish_date'), $statusDate])->filter()->max() ?? now())->endOfMonth();
        if ($start->diffInMonths($end) > 72) {
            $start = $end->copy()->subMonths(72)->startOfMonth();
        }

        $reports = StatusReport::whereIn('project_id', $projects->pluck('id'))->get()
            ->groupBy(fn ($r) => $r->period_date->format('Y-m'));

        $labels = $pv = $ev = $ac = [];
        $lastEv = $lastAc = null;
        for ($m = $start->copy(); $m->lte($end); $m->addMonth()) {
            $eom = $m->copy()->endOfMonth();
            $labels[] = $m->translatedFormat('M y');
            $pv[] = round($wp->sum(fn (WbsItem $i) => $i->planned_cost * $i->plannedPercentAt($eom) / 100) / 1e6, 2);
            $key = $m->format('Y-m');
            if ($m->lte($statusDate) && isset($reports[$key])) {
                $lastEv = round($reports[$key]->sum('ev') / 1e6, 2);
                $lastAc = round($reports[$key]->sum('ac') / 1e6, 2);
            } elseif ($m->gt($statusDate)) {
                $lastEv = $lastAc = null;
            }
            $ev[] = $m->lte($statusDate) ? $lastEv : null;
            $ac[] = $m->lte($statusDate) ? $lastAc : null;
        }

        return compact('labels', 'pv', 'ev', 'ac');
    }

    public static function byPhase(): array
    {
        $c = static::projects()->countBy('phase');

        return collect(Pmbok::PHASES)->map(fn ($label, $k) => (int) ($c[$k] ?? 0))->all();
    }

    /** Jumlah proyek per unit bisnis per warna kesehatan. */
    public static function healthByUnit(): array
    {
        $out = [];
        foreach (Pmbok::BUSINESS_UNITS as $k => $label) {
            $ps = static::active()->where('business_unit', $k);
            if ($ps->isEmpty()) {
                continue;
            }
            $h = $ps->countBy(fn ($p) => $p->health());
            $out[$label] = ['hijau' => $h['hijau'] ?? 0, 'kuning' => $h['kuning'] ?? 0, 'merah' => $h['merah'] ?? 0];
        }

        return $out;
    }

    public static function riskByCategory(): array
    {
        return Risk::whereIn('status', ['terbuka', 'dipantau', 'terjadi'])->get()
            ->groupBy('category')
            ->map(fn ($g) => ['count' => $g->count(), 'high' => $g->filter(fn ($r) => $r->score >= 15)->count()])
            ->sortByDesc('count')
            ->mapWithKeys(fn ($v, $k) => [Pmbok::RISK_CATEGORIES[$k] ?? $k => $v])
            ->all();
    }

    public static function contractsByStatus(): array
    {
        return Contract::selectRaw('status, sum(value) as total, count(*) as n')->groupBy('status')->get()
            ->mapWithKeys(fn ($r) => [Pmbok::CONTRACT_STATUSES[$r->status] ?? $r->status => round($r->total / 1e6, 2)])
            ->all();
    }

    /** Jam lembar waktu 12 minggu terakhir, per unit bisnis. */
    public static function timesheetWeeks(int $weeks = 12): array
    {
        $end = static::statusDate()->copy()->startOfWeek();
        $start = $end->copy()->subWeeks($weeks - 1);
        $rows = Timesheet::with('project:id,business_unit')->whereBetween('week_start', [$start, $end->copy()->endOfWeek()])->get();
        $labels = [];
        $series = [];
        for ($w = $start->copy(); $w->lte($end); $w->addWeek()) {
            $labels[] = $w->format('d/m');
            $wk = $rows->filter(fn ($t) => $t->week_start->isSameDay($w));
            foreach (Pmbok::BUSINESS_UNITS as $k => $label) {
                $series[$label][] = round($wk->filter(fn ($t) => $t->project?->business_unit === $k)->sum('hours'), 1);
            }
        }

        return ['labels' => $labels, 'series' => array_filter($series, fn ($s) => array_sum($s) > 0)];
    }

    /**
     * Beban sumber daya per bulan (jam teralokasi ÷ kapasitas × 100) untuk peta panas kapasitas.
     *
     * @return array{months: array<int,string>, rows: array<int,array{resource:Resource, cells: array<int,float>}>}
     */
    public static function capacity(int $months = 12, ?CarbonInterface $from = null): array
    {
        $from = ($from ?? static::statusDate())->copy()->startOfMonth();
        $assign = Assignment::with('project:id,status')->get()->filter(fn ($a) => in_array($a->project?->status, ['aktif', 'ditunda']));
        $labels = [];
        $periods = [];
        for ($i = 0; $i < $months; $i++) {
            $m = $from->copy()->addMonths($i);
            $labels[] = $m->translatedFormat('M y');
            $periods[] = [$m->copy()->startOfMonth(), $m->copy()->endOfMonth()];
        }
        $rows = [];
        foreach (Resource::where('is_active', true)->where('type', '!=', 'peralatan')->orderBy('department')->orderBy('name')->get() as $r) {
            $mine = $assign->where('resource_id', $r->id);
            $cells = [];
            foreach ($periods as [$s, $e]) {
                $load = 0;
                foreach ($mine as $a) {
                    if (! $a->start_date || ! $a->finish_date || $a->finish_date->lt($s) || $a->start_date->gt($e)) {
                        continue;
                    }
                    $os = $a->start_date->gt($s) ? $a->start_date : $s;
                    $oe = $a->finish_date->lt($e) ? $a->finish_date : $e;
                    $load += $a->allocation * (($os->diffInDays($oe) + 1) / ($s->diffInDays($e) + 1));
                }
                $cells[] = round($load);
            }
            $rows[] = ['resource' => $r, 'cells' => $cells];
        }

        return ['months' => $labels, 'rows' => $rows];
    }

    public static function upcomingMilestones(int $days = 180): Collection
    {
        $d = static::statusDate();

        return WbsItem::with('project')->where('is_milestone', true)->where('percent_complete', '<', 100)
            ->whereBetween('finish_date', [$d->copy()->subDays(30), $d->copy()->addDays($days)])
            ->orderBy('finish_date')->limit(10)->get();
    }
}
