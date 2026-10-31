<?php

namespace App\Models;

use App\Models\Concerns\HasDocuments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LessonLearned extends Model
{
    use HasDocuments;

    protected $table = 'lessons_learned';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'recorded_at' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
