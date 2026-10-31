<?php

namespace App\Support;

use App\Models\Project;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

/**
 * Menyusun Gantt chart SVG sebuah proyek: batang tugas (aktual), batang baseline, progres,
 * milestone, garis tanggal status, dan panah keterkaitan FS/SS/FF/SF (dengan lag).
 */
class GanttChart
{
    public const ROW = 28;

    public const HEADER = 46;

    public const LABEL = 360;

    /** @return array<string,mixed> */
    public static function build(Project $project, int $maxLevel = 3): array
    {
        $items = $project->wbsItems()->with('resource')->get()->where('level', '<=', $maxLevel);
        $deps = $project->dependencies()->with(['predecessor', 'successor'])->get();
        $ordered = self::treeOrder($items);
        $parents = $items->pluck('parent_id')->filter()->unique();

        $dated = $ordered->filter(fn ($i) => $i->start_date && $i->finish_date);
        $min = collect([$dated->min('start_date'), $dated->min('baseline_start'), $project->start_date])->filter()->min();
        $max = collect([$dated->max('finish_date'), $dated->max('baseline_finish'), $project->finish_date])->filter()->max();
        $min = Carbon::parse($min ?? now())->startOfMonth();
        $max = Carbon::parse($max ?? now()->addMonths(6))->endOfMonth();
        $days = max(30, (int) $min->diffInDays($max) + 1);
        $ppd = max(0.45, min(14, 660 / $days));
        $timelineW = (int) ceil($days * $ppd);

        $x = fn (?CarbonInterface $d) => $d ? self::LABEL + $min->diffInDays($d) * $ppd : null;

        $rows = [];
        $index = [];
        foreach ($ordered->values() as $n => $i) {
            $y = self::HEADER + $n * self::ROW;
            $index[$i->id] = $n;
            $x1 = $x($i->start_date);
            $x2 = $i->finish_date ? $x($i->finish_date) + $ppd : null;
            $bx1 = $x($i->baseline_start);
            $bx2 = $i->baseline_finish ? $x($i->baseline_finish) + $ppd : null;
            $rows[] = [
                'id' => $i->id,
                'code' => $i->code,
                'name' => $i->name,
                'level' => (int) $i->level,
                'y' => $y,
                'x1' => $x1, 'x2' => $x2,
                'bx1' => $bx1, 'bx2' => $bx2,
                'pct' => (float) $i->percent_complete,
                'summary' => $parents->contains($i->id),
                'milestone' => (bool) $i->is_milestone,
                'late' => $i->slip_days > 0 && $i->percent_complete < 100,
                'slip' => $i->slip_days,
                'start' => $i->start_date?->format('d/m/y'),
                'finish' => $i->finish_date?->format('d/m/y'),
                'resource' => $i->resource?->name,
                'tooltip' => sprintf('%s %s · %s – %s · %s%% selesai%s', $i->code, $i->name,
                    $i->start_date?->format('d/m/Y') ?? '-', $i->finish_date?->format('d/m/Y') ?? '-',
                    Fmt::num($i->percent_complete), $i->slip_days > 0 ? ' · terlambat '.$i->slip_days.' hari dari baseline' : ''),
            ];
        }

        $arrows = [];
        foreach ($deps as $d) {
            if (! isset($index[$d->predecessor_id], $index[$d->successor_id])) {
                continue;
            }
            $p = $rows[$index[$d->predecessor_id]];
            $s = $rows[$index[$d->successor_id]];
            if ($p['x1'] === null || $s['x1'] === null) {
                continue;
            }
            $fromEnd = in_array($d->type, ['FS', 'FF'], true);
            $toStart = in_array($d->type, ['FS', 'SS'], true);
            $sx = $fromEnd ? $p['x2'] : $p['x1'];
            $tx = $toStart ? $s['x1'] : $s['x2'];
            if ($s['milestone']) {
                $tx = $s['x1'] + ($toStart ? -6 : 6);
            }
            $sy = $p['y'] + self::ROW / 2;
            $ty = $s['y'] + self::ROW / 2;
            $a = $sx + ($fromEnd ? 8 : -8);
            $b = $tx + ($toStart ? -8 : 8);
            $direct = $toStart ? $a <= $b : $a >= $b;
            $path = $direct
                ? sprintf('M%.1f %.1f H%.1f V%.1f H%.1f', $sx, $sy, $a, $ty, $tx)
                : sprintf('M%.1f %.1f H%.1f V%.1f H%.1f V%.1f H%.1f', $sx, $sy, $a, $ty + ($ty > $sy ? -1 : 1) * self::ROW / 2, $b, $ty, $tx);
            $arrows[] = [
                'path' => $path,
                'type' => $d->type,
                'label' => $d->label,
                'ok' => $d->isSatisfied(),
                'title' => sprintf('%s → %s (%s)%s', $p['code'], $s['code'], $d->label, $d->isSatisfied() ? '' : ' · keterkaitan dilanggar'),
            ];
        }

        $months = [];
        for ($m = $min->copy(); $m->lte($max); $m->addMonth()) {
            $months[] = ['x' => $x($m), 'w' => $m->daysInMonth * $ppd, 'label' => $m->translatedFormat($ppd * 30 < 46 ? 'M' : 'M y'), 'year' => $m->year, 'month' => $m->month];
        }
        $years = collect($months)->groupBy('year')->map(fn ($g, $y) => ['x' => $g->first()['x'], 'w' => $g->sum('w'), 'label' => (string) $y])->values()->all();

        $statusDate = $project->statusDate();

        return [
            'rows' => $rows,
            'arrows' => $arrows,
            'months' => $months,
            'years' => $years,
            'width' => self::LABEL + $timelineW + 20,
            'height' => self::HEADER + count($rows) * self::ROW + 8,
            'label' => self::LABEL,
            'row' => self::ROW,
            'header' => self::HEADER,
            'today' => $statusDate->between($min, $max) ? $x($statusDate) : null,
            'statusDate' => $statusDate->format('d/m/Y'),
        ];
    }

    /** Urutan pohon WBS (induk lalu anaknya) berdasarkan sort & kode. */
    protected static function treeOrder(Collection $items): Collection
    {
        $byParent = $items->groupBy(fn ($i) => $i->parent_id ?? 0);
        $out = collect();
        $walk = function ($parentId) use (&$walk, $byParent, $out) {
            foreach (($byParent[$parentId] ?? collect())->sortBy(fn ($i) => sprintf('%05d-%s', $i->sort, $i->code)) as $i) {
                $out->push($i);
                $walk($i->id);
            }
        };
        $walk(0);

        return $out;
    }
}
