<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'finish_date' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    public function wbsItem(): BelongsTo
    {
        return $this->belongsTo(WbsItem::class);
    }
}
