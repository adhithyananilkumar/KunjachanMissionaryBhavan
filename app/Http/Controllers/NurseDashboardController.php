<?php

namespace App\Http\Controllers;

use App\Models\Inmate;
use Illuminate\Database\Eloquent\Relations\HasOne;

class NurseDashboardController extends Controller
{
    public function index()
    {
        $nurse = auth()->user();
        $inmates = Inmate::where('institution_id', $nurse->institution_id)
            ->with(['medicalRecords' => function ($q) { $q->latest(); }])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
        // We'll take only the first (latest) record in view logic.
        return view('dashboards.nurse', compact('inmates'));
    }
}
