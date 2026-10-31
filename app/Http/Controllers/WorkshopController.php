<?php

namespace App\Http\Controllers;

/** Halaman promosi Workshop Project Management for Executive (mengenal Promise). */
class WorkshopController extends Controller
{
    public function __invoke()
    {
        return view('workshop', ['whatsapp' => config('promise.contact_whatsapp')]);
    }
}
