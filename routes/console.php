<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Validator;

// Antrian (driver database) dijalankan dari scheduler, cocok untuk hosting cPanel
Schedule::command('queue:work --stop-when-empty --tries=3')->everyMinute()->withoutOverlapping();

Artisan::command('promise:create-admin {--name=} {--email=} {--password=}', function () {
    $name = $this->option('name') ?: $this->ask('Nama', 'Administrator');
    $email = $this->option('email') ?: $this->ask('Email');
    $password = $this->option('password') ?: $this->secret('Kata sandi (min. 8 karakter)');

    $validator = Validator::make(compact('name', 'email', 'password'), [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255'],
        'password' => ['required', 'string', 'min:8'],
    ]);
    if ($validator->fails()) {
        foreach ($validator->errors()->all() as $error) {
            $this->error($error);
        }

        return 1;
    }

    $user = User::firstOrNew(['email' => $email]);
    $user->forceFill([
        'name' => $name,
        'password' => $password,
        'job_title' => $user->job_title ?: 'Administrator Sistem',
        'role' => 'admin',
        'is_active' => true,
    ])->save();

    $this->info("Admin {$email} siap dipakai. Masuk di ".url('/admin'));
})->purpose('Membuat atau memperbarui akun Administrator Promise');
