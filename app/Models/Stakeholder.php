<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stakeholder extends Model
{
    protected $guarded = ['id'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
