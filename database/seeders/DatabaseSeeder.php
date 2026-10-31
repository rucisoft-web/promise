<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Di produksi admin hanya dibuat bila PROMISE_ADMIN_PASSWORD diisi; selain itu pakai `php artisan promise:create-admin`
        $canCreateAdmin = ! app()->isProduction() || filled(config('promise.admin_password'));
        if ($canCreateAdmin && ! User::where('role', 'admin')->exists()) {
            User::forceCreate([
                'name' => 'Administrator',
                'email' => config('promise.admin_email') ?: 'admin@promise.local',
                'password' => config('promise.admin_password') ?: 'Promise@2026',
                'job_title' => 'Administrator Sistem',
                'role' => 'admin',
                'is_active' => true,
            ]);
        }

        if (! app()->isProduction() || config('promise.seed_demo')) {
            $this->call(DemoMedcoSeeder::class);
        }
    }
}
