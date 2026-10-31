<?php

namespace App\Providers;

use App\Models\User;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        \Filament\Forms\Components\DatePicker::configureUsing(fn ($c) => $c->native(false)->displayFormat('d/m/Y'));
        \Filament\Tables\Table::configureUsing(fn ($t) => $t->defaultPaginationPageOption(25)->striped());

        FilamentAsset::register([Css::make('promise', asset('css/promise.css').'?v=1')]);

        Password::defaults(fn () => Password::min(10)->letters()->numbers());

        Event::listen(Login::class, function (Login $event) {
            if ($event->user instanceof User) {
                $event->user->forceFill(['last_login_at' => now()])->saveQuietly();
            }
        });

        \App\Support\GuestMode::registerWriteGuard();
    }
}
