<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Inmate;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $institutionId = Auth::user()->institution_id;
        $stats = [
            'staff' => User::where('institution_id', $institutionId)->count(),
            'inmates' => Inmate::where('institution_id', $institutionId)->count(),
        ];
        return view('dashboards.admin', compact('stats'));
    }
}
