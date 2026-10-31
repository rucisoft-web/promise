<?php

namespace App\Models\Concerns;

use App\Models\Document;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasDocuments
{
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'attachable')->latest();
    }

    public static function bootHasDocuments(): void
    {
        static::deleting(fn ($model) => $model->documents()->get()->each->delete());
    }
}
