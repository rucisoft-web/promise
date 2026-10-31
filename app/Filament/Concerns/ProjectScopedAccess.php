<?php

namespace App\Filament\Concerns;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/** Hak akses untuk data milik proyek: admin/PMO semua proyek, manajer proyek hanya proyeknya. */
trait ProjectScopedAccess
{
    protected static function user(): ?User
    {
        $u = auth()->user();

        return $u instanceof User ? $u : null;
    }

    public static function canViewAny(): bool
    {
        return static::user() !== null;
    }

    public static function canCreate(): bool
    {
        return (bool) static::user()?->canEditProjects();
    }

    public static function canEdit(Model $record): bool
    {
        $project = $record instanceof Project ? $record : $record->project;

        return (bool) static::user()?->canEditProject($project);
    }

    public static function canDelete(Model $record): bool
    {
        return static::canEdit($record);
    }

    public static function canDeleteAny(): bool
    {
        return (bool) static::user()?->canEditProjects();
    }
}
