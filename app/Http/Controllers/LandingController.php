<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class LandingController extends Controller
{
    public function index(): View
    {
        // These values come from the PostgreSQL demo table, not hardcoded HTML.
        $availableCount = DB::table('circlo_demo_items')
            ->where('status', 'available')
            ->count();

        $featuredItems = DB::table('circlo_demo_items')
            ->where('status', 'available')
            ->orderBy('id')
            ->limit(3)
            ->get();

        return view('landing', compact('availableCount', 'featuredItems'));
    }
}
