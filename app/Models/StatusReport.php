<?php

namespace App\Models;

use App\Models\Concerns\HasDocuments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusReport extends Model
{
    use HasDocuments;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'period_date' => 'date',
            'pv' => 'float',
            'ev' => 'float',
            'ac' => 'float',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
