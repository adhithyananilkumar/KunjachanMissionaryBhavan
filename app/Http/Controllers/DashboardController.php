<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return match($user->role) {
            'developer' => redirect()->route('developer.dashboard'),
            'system_admin' => redirect()->route('system_admin.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            'doctor' => redirect()->route('doctor.dashboard'),
            'nurse' => redirect()->route('nurse.dashboard'),
            'staff' => redirect()->route('staff.dashboard'),
            'guardian' => redirect()->route('guardian.dashboard'),
            default => redirect()->route('dashboard'),
        };
    }
}
