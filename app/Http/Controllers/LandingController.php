<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Support\GuestMode;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/** Halaman depan situs demo; bila login Tamu tidak aktif langsung ke panel. */
class LandingController extends Controller
{
    public function __invoke()
    {
        if (! GuestMode::enabled()) {
            return redirect('/admin');
        }

        $stats = Cache::remember('promise.landing.stats', 600, function () {
            if (! Schema::hasTable('projects')) {
                return ['projects' => 0, 'wbs' => 0, 'risks' => 0, 'budget' => 0];
            }

            return [
                'projects' => Project::count(),
                'wbs' => \App\Models\WbsItem::count(),
                'risks' => \App\Models\Risk::count(),
                'budget' => (float) Project::sum('budget'),
            ];
        });

        return view('landing', ['stats' => $stats, 'whatsapp' => config('promise.contact_whatsapp')]);
    }
}
