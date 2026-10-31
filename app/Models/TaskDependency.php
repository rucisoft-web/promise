<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class TaskDependency extends Model
{
    use Concerns\ScopedByProjectCompany;

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::saving(function (self $dep) {
            if ((int) $dep->predecessor_id === (int) $dep->successor_id) {
                throw ValidationException::withMessages(['successor_id' => 'Tugas tidak boleh bergantung pada dirinya sendiri.']);
            }
            $dep->project_id ??= WbsItem::find($dep->successor_id)?->project_id;
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function predecessor(): BelongsTo
    {
        return $this->belongsTo(WbsItem::class, 'predecessor_id');
    }

    public function successor(): BelongsTo
    {
        return $this->belongsTo(WbsItem::class, 'successor_id');
    }

    public function getLabelAttribute(): string
    {
        $lag = (int) $this->lag_days;

        return $this->type.($lag > 0 ? '+'.$lag.'h' : ($lag < 0 ? $lag.'h' : ''));
    }

    /**
     * Apakah tanggal tugas memenuhi keterkaitan ini (dengan lag)?
     * FS: mulai penerus ≥ selesai pendahulu; SS: mulai ≥ mulai; FF: selesai ≥ selesai; SF: selesai penerus ≥ mulai pendahulu.
     */
    public function isSatisfied(): bool
    {
        $p = $this->predecessor;
        $s = $this->successor;
        if (! $p?->start_date || ! $s?->start_date) {
            return true;
        }
        $lag = (int) $this->lag_days;
        [$from, $to] = match ($this->type) {
            'SS' => [$p->start_date, $s->start_date],
            'FF' => [$p->finish_date, $s->finish_date],
            'SF' => [$p->start_date, $s->finish_date],
            default => [$p->finish_date, $s->start_date],
        };

        return $to->greaterThanOrEqualTo($from->copy()->addDays($lag));
    }
}
