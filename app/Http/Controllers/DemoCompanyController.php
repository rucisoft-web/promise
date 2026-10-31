<?php

namespace App\Http\Controllers;

use App\Support\DemoCompany;
use Illuminate\Http\Request;

/** Ganti perusahaan data demo yang ditampilkan (medco, smbc, atau semua). */
class DemoCompanyController extends Controller
{
    public function __invoke(Request $request, string $company)
    {
        abort_unless($company === 'semua' || DemoCompany::valid($company), 404);
        DemoCompany::set($company === 'semua' ? null : $company);

        // Kembali ke halaman daftar sebelumnya; halaman satu rekaman bisa milik perusahaan lain
        $admin = url('/admin');
        $previous = strtok(url()->previous(), '?');
        if (! str_starts_with($previous, $admin) || preg_match('#/\d+(/|$)#', substr($previous, strlen($admin))) || str_contains($previous, '/login')) {
            $previous = $admin;
        }

        return redirect()->to($previous);
    }
}
