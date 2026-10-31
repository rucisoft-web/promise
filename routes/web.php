<?php

use Illuminate\Support\Facades\Route;

// Halaman depan situs demo (PROMISE_GUEST_LOGIN=true); selain itu langsung ke panel
Route::middleware('web')->get('/', \App\Http\Controllers\LandingController::class)->name('promise.landing');

// Workshop Project Management for Executive (mengenal Promise)
Route::middleware('web')->get('/workshop', \App\Http\Controllers\WorkshopController::class)->name('promise.workshop');

// Situs demo: masuk sebagai Tamu (hanya lihat)
Route::middleware(['web', 'throttle:20,1'])
    ->get('/masuk-tamu', \App\Http\Controllers\GuestLoginController::class)
    ->name('promise.guest');

// Lampiran dokumen (disk privat)
Route::middleware(['web', 'auth'])
    ->get('/dokumen/{document}', \App\Http\Controllers\DocumentController::class)
    ->name('promise.document');

// Ganti perusahaan data demo yang ditampilkan
Route::middleware(['web', 'auth'])
    ->get('/perusahaan-demo/{company}', \App\Http\Controllers\DemoCompanyController::class)
    ->name('promise.company');
