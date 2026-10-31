<?php

namespace App\Models;

use Carbon\CarbonInterface;
use App\Models\Concerns\HasDocuments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Elemen WBS. Level 1 = deliverable/fase utama, level 2 = paket kerja (work package) tempat
 * jadwal, anggaran (planned value) dan progres dicatat; level 1 merupakan agregat anaknya.
 */
class WbsItem extends Model
{
    use Concerns\ScopedByProjectCompany;

    use HasDocuments;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'finish_date' => 'date',
            'baseline_start' => 'date',
            'baseline_finish' => 'date',
            'percent_complete' => 'float',
            'planned_cost' => 'float',
            'actual_cost' => 'float',
            'is_milestone' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $item) {
            $item->level = $item->parent_id ? min(3, (int) (static::find($item->parent_id)?->level ?? 1) + 1) : 1;
            if ($item->is_milestone && $item->start_date) {
                $item->finish_date = $item->start_date;
            }
            $item->baseline_start ??= $item->start_date;
            $item->baseline_finish ??= $item->finish_date;
        });
        static::saved(fn (self $item) => $item->parent?->rollup());
        static::deleted(fn (self $item) => $item->parent?->rollup());
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort')->orderBy('code');
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    public function predecessors(): HasMany
    {
        return $this->hasMany(TaskDependency::class, 'successor_id');
    }

    public function successors(): HasMany
    {
        return $this->hasMany(TaskDependency::class, 'predecessor_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function isSummary(): bool
    {
        return $this->children()->exists();
    }

    /** Ringkasan level atas: tanggal, biaya, dan progres tertimbang biaya dari paket kerja di bawahnya. */
    public function rollup(): void
    {
        $kids = $this->children()->get();
        if ($kids->isEmpty()) {
            return;
        }
        $planned = (float) $kids->sum('planned_cost');
        $pct = $planned > 0
            ? $kids->sum(fn ($k) => $k->planned_cost * $k->percent_complete) / $planned
            : (float) $kids->avg('percent_complete');
        $this->forceFill([
            'start_date' => $kids->min('start_date'),
            'finish_date' => $kids->max('finish_date'),
            'baseline_start' => $kids->min('baseline_start'),
            'baseline_finish' => $kids->max('baseline_finish'),
            'planned_cost' => $planned,
            'actual_cost' => (float) $kids->sum('actual_cost'),
            'percent_complete' => round($pct, 2),
        ])->saveQuietly();
        $this->parent?->rollup();
    }

    public function getDurationDaysAttribute(): int
    {
        return $this->start_date && $this->finish_date ? (int) $this->start_date->diffInDays($this->finish_date) + 1 : 0;
    }

    /** Persentase rencana pada tanggal status menurut baseline (distribusi linier). */
    public function plannedPercentAt(CarbonInterface $date): float
    {
        $bs = $this->baseline_start ?? $this->start_date;
        $bf = $this->baseline_finish ?? $this->finish_date;
        if (! $bs || ! $bf) {
            return 0;
        }
        if ($date->lt($bs)) {
            return 0;
        }
        if ($date->gte($bf)) {
            return 100;
        }
        $total = max(1, $bs->diffInDays($bf) + 1);

        return round(($bs->diffInDays($date) + 1) / $total * 100, 2);
    }

    public function getSlipDaysAttribute(): int
    {
        return $this->finish_date && $this->baseline_finish ? (int) $this->baseline_finish->diffInDays($this->finish_date, false) : 0;
    }

    public function getLabelAttribute(): string
    {
        return $this->code.' '.$this->name;
    }
}
