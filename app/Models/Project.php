<?php

namespace App\Models;

use App\Support\Pmbok;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Concerns\HasDocuments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasDocuments;

    protected $guarded = ['id'];

    /** @var array<string,float|null>|null */
    protected ?array $evmCache = null;

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'finish_date' => 'date',
            'baseline_start' => 'date',
            'baseline_finish' => 'date',
            'status_date' => 'date',
            'budget' => 'float',
            'contingency' => 'float',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $p) {
            $p->baseline_start ??= $p->start_date;
            $p->baseline_finish ??= $p->finish_date;
        });
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function wbsItems(): HasMany
    {
        return $this->hasMany(WbsItem::class)->orderBy('sort')->orderBy('code');
    }

    public function dependencies(): HasMany
    {
        return $this->hasMany(TaskDependency::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function timesheets(): HasMany
    {
        return $this->hasMany(Timesheet::class);
    }

    public function risks(): HasMany
    {
        return $this->hasMany(Risk::class);
    }

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }

    public function changeRequests(): HasMany
    {
        return $this->hasMany(ChangeRequest::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(LessonLearned::class);
    }

    public function qualityChecks(): HasMany
    {
        return $this->hasMany(QualityCheck::class);
    }

    public function stakeholders(): HasMany
    {
        return $this->hasMany(Stakeholder::class);
    }

    public function communicationPlans(): HasMany
    {
        return $this->hasMany(CommunicationPlan::class);
    }

    public function statusReports(): HasMany
    {
        return $this->hasMany(StatusReport::class)->orderBy('period_date');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('status', 'aktif');
    }

    /** Proyek yang boleh diubah pengguna: admin/PMO semua, manajer proyeknya sendiri. */
    public function scopeEditableBy(Builder $q, ?User $user): Builder
    {
        if ($user?->managesAll()) {
            return $q;
        }

        return $q->where('manager_id', $user?->id ?? 0);
    }

    public function statusDate(): CarbonInterface
    {
        if ($this->status_date) {
            return $this->status_date;
        }
        $env = config('promise.status_date');

        return $env ? Carbon::parse($env) : Carbon::today();
    }

    /** Paket kerja: elemen WBS tanpa anak (tempat nilai PV/EV/AC dicatat). */
    public function workPackages()
    {
        $items = $this->relationLoaded('wbsItems') ? $this->wbsItems : $this->wbsItems()->get();
        $parents = $items->pluck('parent_id')->filter()->unique();

        return $items->reject(fn ($i) => $parents->contains($i->id))->values();
    }

    /**
     * Manajemen Nilai Hasil (Earned Value Management) pada tanggal status.
     *
     * @return array{bac:float,pv:float,ev:float,ac:float,sv:float,cv:float,spi:?float,cpi:?float,eac:float,etc:float,vac:float,tcpi:?float,planned_pct:float,actual_pct:float}
     */
    public function evm(?CarbonInterface $date = null): array
    {
        if ($this->evmCache !== null && $date === null) {
            return $this->evmCache;
        }
        $date ??= $this->statusDate();
        $wp = $this->workPackages();
        $bac = (float) $wp->sum('planned_cost') ?: (float) $this->budget;
        $pv = (float) $wp->sum(fn ($i) => $i->planned_cost * $i->plannedPercentAt($date) / 100);
        $ev = (float) $wp->sum(fn ($i) => $i->planned_cost * $i->percent_complete / 100);
        $ac = (float) $wp->sum('actual_cost');
        $spi = $pv > 0 ? $ev / $pv : null;
        $cpi = $ac > 0 ? $ev / $ac : null;
        $eac = $cpi ? $bac / $cpi : $bac;
        $out = [
            'bac' => $bac, 'pv' => $pv, 'ev' => $ev, 'ac' => $ac,
            'sv' => $ev - $pv, 'cv' => $ev - $ac,
            'spi' => $spi, 'cpi' => $cpi,
            'eac' => $eac, 'etc' => max(0, $eac - $ac), 'vac' => $bac - $eac,
            'tcpi' => ($bac - $ac) > 0 ? ($bac - $ev) / ($bac - $ac) : null,
            'planned_pct' => $bac > 0 ? $pv / $bac * 100 : 0,
            'actual_pct' => $bac > 0 ? $ev / $bac * 100 : 0,
        ];

        return $date->eq($this->statusDate()) ? ($this->evmCache = $out) : $out;
    }

    public function forgetEvm(): void
    {
        $this->evmCache = null;
    }

    /** Kesehatan proyek dari SPI & CPI: hijau ≥ 0,95; kuning ≥ 0,85; selain itu merah. */
    public function health(): string
    {
        if ($this->status === 'selesai') {
            return 'hijau';
        }
        $e = $this->evm();
        $worst = min($e['spi'] ?? 1, $e['cpi'] ?? 1);

        return match (true) {
            $worst >= 0.95 => 'hijau',
            $worst >= 0.85 => 'kuning',
            default => 'merah',
        };
    }

    public function getPhaseLabelAttribute(): string
    {
        return Pmbok::PHASES[$this->phase] ?? $this->phase;
    }

    public function getLabelAttribute(): string
    {
        return $this->code.' · '.$this->name;
    }
}
