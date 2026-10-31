<?php

namespace App\Filament\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/** Data induk (portofolio, kelompok sumber daya, vendor): diubah admin/PMO, dilihat semua. */
trait AdminOnlyWrite
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
        return (bool) static::user()?->managesAll();
    }

    public static function canEdit(Model $record): bool
    {
        return (bool) static::user()?->managesAll();
    }

    public static function canDelete(Model $record): bool
    {
        return (bool) static::user()?->managesAll();
    }

    public static function canDeleteAny(): bool
    {
        return (bool) static::user()?->managesAll();
    }
}
