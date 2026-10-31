<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommunicationPlan extends Model
{
    use Concerns\ScopedByProjectCompany;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'next_date' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
