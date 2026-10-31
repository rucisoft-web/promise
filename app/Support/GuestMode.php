<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Akses Tamu untuk situs demo: pengunjung masuk dengan satu klik dan hanya bisa melihat.
 * Aktif bila PROMISE_GUEST_LOGIN=true. Peran `tamu` tidak punya hak ubah di antarmuka; selain itu
 * setiap penyimpanan/penghapusan model oleh Tamu ditolak di sini sebagai pengaman terakhir.
 */
class GuestMode
{
    public const ROLE = 'tamu';

    public const MESSAGE = 'Mode demo: akun Tamu hanya dapat melihat, data tidak dapat diubah.';

    protected const USER_HOUSEKEEPING = ['remember_token', 'last_login_at', 'updated_at'];

    public static function enabled(): bool
    {
        return (bool) config('promise.guest_login');
    }

    public static function isGuest(?User $user = null): bool
    {
        $user ??= Auth::user();

        return $user instanceof User && $user->isGuestVisitor();
    }

    public static function user(): User
    {
        $user = User::where('email', config('promise.guest_email'))->first() ?? User::forceCreate([
            'name' => 'Tamu',
            'email' => config('promise.guest_email'),
            'password' => Str::password(32),
            'job_title' => 'Pengunjung demo',
            'role' => self::ROLE,
            'is_active' => true,
        ]);
        if (! $user->is_active || $user->role !== self::ROLE) {
            $user->forceFill(['is_active' => true, 'role' => self::ROLE])->saveQuietly();
        }

        return $user;
    }

    public static function registerWriteGuard(): void
    {
        Event::listen(['eloquent.saving: *', 'eloquent.deleting: *'], function (string $event, array $payload) {
            $model = $payload[0] ?? null;
            if (! $model instanceof Model || ! Auth::hasUser() || ! self::isGuest()) {
                return null;
            }
            if ($model instanceof DatabaseNotification) {
                return null;
            }
            if ($model instanceof User && $model->is(Auth::user()) && str_starts_with($event, 'eloquent.saving')
                && array_diff(array_keys($model->getDirty()), self::USER_HOUSEKEEPING) === []) {
                return null;
            }

            throw new HttpException(403, self::MESSAGE);
        });
    }
}
