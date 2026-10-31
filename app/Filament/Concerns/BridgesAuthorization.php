<?php

namespace App\Filament\Concerns;

use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/**
 * Filament 5 memeriksa tombol aksi (Buat, Ubah, Hapus) lewat getAuthorizationResponse(),
 * bukan lewat canCreate()/canEdit(). Trait ini menyalurkan pemeriksaan itu ke metode can*() resource.
 */
trait BridgesAuthorization
{
    public static function getAuthorizationResponse(string|UnitEnum $action, ?Model $record = null): Response
    {
        $action = $action instanceof UnitEnum ? ($action->value ?? $action->name) : $action;

        $allowed = match ($action) {
            'viewAny', 'view' => static::canViewAny(),
            'create', 'replicate' => static::canCreate(),
            'update' => $record ? static::canEdit($record) : static::canCreate(),
            'delete', 'forceDelete' => $record ? static::canDelete($record) : static::canDeleteAny(),
            'deleteAny', 'forceDeleteAny', 'restore', 'restoreAny', 'reorder' => static::canDeleteAny(),
            default => static::canViewAny(),
        };

        return $allowed ? Response::allow() : Response::deny();
    }
}
