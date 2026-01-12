<?php

namespace App\Http\Controllers;

use App\Models\Inmate;
use App\Models\Appointment;
use App\Models\LabTest;
use Illuminate\Support\Facades\Auth;

class DoctorDashboardController extends Controller
{
    public function index()
    {
        $doctor = auth()->user();

        // All inmates in doctor's institution (for searchable list)
        $inmatesQuery = Inmate::where('institution_id', $doctor->institution_id)
            ->when(optional($doctor->institution)->doctor_assignment_enabled, function($q) use ($doctor) {
                $q->where(function($x) use ($doctor){
                    $x->where('doctor_id', $doctor->id)
                      ->orWhereExists(function($sub) use ($doctor){
                          $sub->selectRaw('1')
                              ->from('doctor_inmate as di')
                              ->whereColumn('di.inmate_id','inmates.id')
                              ->where('di.doctor_id',$doctor->id);
                      });
                });
            })
            ->orderBy('first_name')
            ->orderBy('last_name');
        $inmates = $inmatesQuery->get();
        $inmateCount = $inmates->count();

        // Today's appointments for this doctor (by scheduled_by) within the same institution
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();
        $todaysAppointments = Appointment::with('inmate')
            ->where('scheduled_by', $doctor->id)
            ->whereBetween('scheduled_for', [$todayStart, $todayEnd])
            ->whereHas('inmate', function($q) use ($doctor){
                $q->where('institution_id', $doctor->institution_id)
                  ->when(optional($doctor->institution)->doctor_assignment_enabled, function($q2) use ($doctor){
                      $q2->where('doctor_id', $doctor->id);
                  });
            })
            ->orderBy('scheduled_for')
            ->get();
        $todaysAppointmentsCount = $todaysAppointments->count();

        // Pending lab results ordered by this doctor (not completed or cancelled)
        $pendingLabResultsCount = LabTest::where('ordered_by', $doctor->id)
            ->whereHas('inmate', function($q) use ($doctor) {
                $q->where('institution_id', $doctor->institution_id)
                  ->when(optional($doctor->institution)->doctor_assignment_enabled, function($q2) use ($doctor) {
                      $q2->where('doctor_id', $doctor->id);
                  });
            })
            ->whereIn('status', ['ordered','in_progress'])
            ->count();

        return view('dashboards.doctor', compact(
            'inmates',
            'inmateCount',
            'todaysAppointments',
            'todaysAppointmentsCount',
            'pendingLabResultsCount'
        ));
    }
}
