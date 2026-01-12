<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class GuardianDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $guardian = $user->guardian; // may be null
        $inmate = null;
        if($guardian) {
            $inmate = $guardian->inmate()?->with(['medicalRecords.doctor'])->first();
        }
        return view('dashboards.guardian', compact('inmate','guardian'));
    }
}
