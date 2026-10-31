<?php

namespace App\Models;

use App\Models\Concerns\HasDocuments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Risk extends Model
{
    use HasDocuments;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'identified_at' => 'date',
            'due_date' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }

    public function getScoreAttribute(): int
    {
        return (int) $this->probability * (int) $this->impact;
    }
}
