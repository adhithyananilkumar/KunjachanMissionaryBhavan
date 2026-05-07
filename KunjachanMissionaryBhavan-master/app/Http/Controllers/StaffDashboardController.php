<?php
namespace App\Http\Controllers;

use App\Models\Inmate;

class StaffDashboardController extends Controller
{
    public function index()
    {
        $staff = auth()->user();
        $inmates = Inmate::where('institution_id', $staff->institution_id)
            ->with(['medicalRecords' => function($q){ $q->latest(); }])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
        return view('dashboards.staff', compact('inmates'));
    }
}
