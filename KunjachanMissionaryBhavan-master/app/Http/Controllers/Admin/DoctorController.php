<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Inmate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = User::where('institution_id', Auth::user()->institution_id)
            ->where('role', 'doctor')
            ->orderBy('name')
            ->paginate(15);
        return view('admin.doctors.index', compact('doctors'));
    }

    public function show(User $doctor)
    {
        abort_unless($doctor->role === 'doctor' && $doctor->institution_id === Auth::user()->institution_id, 403);
        $inmates = \App\Models\Inmate::where('institution_id', Auth::user()->institution_id)
            ->orderBy('first_name')->orderBy('last_name')->get();
        $assignedIds = $doctor->assignedInmates()->pluck('inmate_id')->toArray();
        return view('admin.doctors.show', compact('doctor','inmates','assignedIds'));
    }

    public function saveAssignments(User $doctor, Request $request)
    {
        abort_unless($doctor->role === 'doctor' && $doctor->institution_id === Auth::user()->institution_id, 403);
        $data = $request->validate(['inmate_ids' => 'array', 'inmate_ids.*' => 'integer|exists:inmates,id']);
        $ids = collect($data['inmate_ids'] ?? [])->unique()->values();
        // Scope to institution
        $validIds = Inmate::where('institution_id', Auth::user()->institution_id)
            ->whereIn('id', $ids)->pluck('id');
        $doctor->assignedInmates()->sync($validIds);
        return back()->with('success','Assignments updated.');
    }

    public function feed(User $doctor, Request $request)
    {
        abort_unless($doctor->role === 'doctor' && $doctor->institution_id === Auth::user()->institution_id, 403);
        $start = $request->query('start');
        $end = $request->query('end');
        $query = Appointment::with('inmate')
            ->where('scheduled_by', $doctor->id)
            ->whereHas('inmate', fn ($q) => $q->where('institution_id', $doctor->institution_id));
        if ($start) { $query->where('scheduled_for', '>=', $start); }
        if ($end) { $query->where('scheduled_for', '<=', $end); }
        $events = $query->get()->map(function ($a) {
            $isEmergency = str_contains(strtoupper($a->title), 'EMERGENCY');
            return [
                'id' => $a->id,
                'title' => $a->title . ' - ' . ($a->inmate->full_name ?? 'Inmate #' . $a->inmate_id),
                'start' => $a->scheduled_for->toIso8601String(),
                'allDay' => true,
                'color' => $isEmergency ? '#dc3545' : null,
                'extendedProps' => [
                    'inmate_id' => $a->inmate_id,
                    'status' => $a->status,
                    'notes' => $a->notes,
                    'is_emergency' => $isEmergency,
                ],
            ];
        });
        return response()->json($events);
    }

    public function scheduleEmergency(User $doctor, Request $request)
    {
        abort_unless($doctor->role === 'doctor' && $doctor->institution_id === Auth::user()->institution_id, 403);
        $data = $request->validate([
            'inmate_id' => 'required|exists:inmates,id',
            'scheduled_for' => 'required|date',
            'reason' => 'required|string|max:500',
        ]);

        // Ensure inmate belongs to this institution and, if assignment is enabled, to this doctor
        $inmate = Inmate::where('id', $data['inmate_id'])
            ->where('institution_id', $doctor->institution_id)
            ->when(optional($doctor->institution)->doctor_assignment_enabled, function($q) use ($doctor){
                $q->where('doctor_id', $doctor->id);
            })
            ->firstOrFail();

        $appointment = Appointment::create([
            'inmate_id' => $inmate->id,
            'scheduled_by' => Auth::id(), // created by admin
            'title' => '[EMERGENCY] Urgent Checkup',
            'notes' => $data['reason'],
            'scheduled_for' => $data['scheduled_for'],
            'status' => 'scheduled',
        ]);

        // Notify the doctor
        try {
            $doctor->notify(new \App\Notifications\EmergencyAppointmentScheduled($appointment));
        } catch (\Throwable $e) {
            // swallow notification errors
        }

        return back()->with('success', 'Emergency appointment scheduled and the doctor has been notified.');
    }
}
