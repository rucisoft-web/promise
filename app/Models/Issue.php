<?php

namespace App\Models;

use App\Models\Concerns\HasDocuments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Issue extends Model
{
    use Concerns\ScopedByProjectCompany;

    use HasDocuments;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'raised_at' => 'date',
            'due_date' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function risk(): BelongsTo
    {
        return $this->belongsTo(Risk::class);
    }
}
