<?php

return [
    'brand_name' => env('PROMISE_BRAND_NAME', 'Promise'),
    'brand_tagline' => env('PROMISE_BRAND_TAGLINE', 'Project Management Information System'),

    // Situs demo/promosi: tombol "Masuk sebagai Tamu" (hanya lihat)
    'guest_login' => (bool) env('PROMISE_GUEST_LOGIN', false),
    'guest_email' => env('PROMISE_GUEST_EMAIL', 'tamu@demo.promise.local'),

    // Muat portofolio contoh walau APP_ENV=production
    'seed_demo' => (bool) env('PROMISE_SEED_DEMO', false),

    // Mata uang tunggal: Rupiah (IDR); lihat App\Support\Fmt::CURRENCY
    'currency' => 'Rp',

    // Tanggal status (data date) untuk perhitungan Earned Value; kosong = hari ini
    'status_date' => env('PROMISE_STATUS_DATE'),

    // Nama perusahaan yang ditampilkan kecil di setiap halaman (bila tidak memakai data demo)
    'company_name' => env('PROMISE_COMPANY_NAME'),

    'contact_whatsapp' => env('PROMISE_WHATSAPP', '6281234567890'),

    // Akun admin awal yang dibuat oleh DatabaseSeeder
    'admin_email' => env('PROMISE_ADMIN_EMAIL', 'admin@promise.local'),
    'admin_password' => env('PROMISE_ADMIN_PASSWORD'),
];
