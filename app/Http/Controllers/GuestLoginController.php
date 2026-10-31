<?php

namespace App\Http\Controllers;

use App\Support\GuestMode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/** Masuk sebagai Tamu (hanya lihat) dengan satu klik dari halaman depan atau halaman login. */
class GuestLoginController extends Controller
{
    public function __invoke(Request $request)
    {
        abort_unless(GuestMode::enabled(), 404);

        if (Auth::check()) {
            Auth::logout();
        }
        Auth::login(GuestMode::user());
        $request->session()->regenerate();

        return redirect('/admin');
    }
}
