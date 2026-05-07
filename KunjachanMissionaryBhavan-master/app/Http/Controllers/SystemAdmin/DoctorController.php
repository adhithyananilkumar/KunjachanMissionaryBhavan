<?php
namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Models\Inmate;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Appointment;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $institutionId = $request->get('institution_id');
        $query = User::query()->where('role','doctor')->with('institution');
        if ($institutionId) { $query->where('institution_id',$institutionId); }
        $doctors = $query->orderBy('name')->paginate(20)->appends($request->only('institution_id'));
        $institutions = Institution::orderBy('name')->get(['id','name']);
        return view('system_admin.doctors.index', compact('doctors','institutions','institutionId'));
    }

    public function show(User $doctor)
    {
        abort_unless($doctor->role === 'doctor', 403);
        $inmates = Inmate::where('institution_id',$doctor->institution_id)->orderBy('first_name')->orderBy('last_name')->get();
        $assignedIds = $doctor->assignedInmates()->pluck('inmate_id')->toArray();
        // Professional quick stats
        $assignedCount = count($assignedIds);
        $upcomingCount = \App\Models\Appointment::where('scheduled_by', $doctor->id)
            ->whereDate('scheduled_for', '>=', now()->toDateString())
            ->count();

        // Recent activity (doctor-centric)
        $activities = [];
        try {
            $tLogs = \App\Models\TherapySessionLog::with('inmate')
                ->where('doctor_id',$doctor->id)->latest('session_date')->limit(8)->get();
            foreach($tLogs as $t){ $activities[]=['at'=>$t->session_date,'icon'=>'heart-pulse','text'=>'Therapy session · '.($t->inmate?->full_name ?? 'Inmate'),'url'=>$t->inmate? route('system_admin.inmates.show',$t->inmate) : null]; }
        } catch (\Throwable $e) {}
        try {
            $ordered = \App\Models\LabTest::with('inmate')->where('ordered_by',$doctor->id)->latest('ordered_date')->limit(6)->get();
            foreach($ordered as $lt){ $activities[]=['at'=>$lt->ordered_date,'icon'=>'beaker','text'=>'Ordered lab · '.($lt->test_name).' · '.($lt->inmate?->full_name ?? 'Inmate'),'url'=>$lt->inmate? route('system_admin.inmates.show',$lt->inmate).'#medical' : null]; }
        } catch (\Throwable $e) {}
        try {
            $appts = \App\Models\Appointment::with('inmate')->where('scheduled_by',$doctor->id)->latest('scheduled_for')->limit(6)->get();
            foreach($appts as $a){ $activities[]=['at'=>$a->scheduled_for,'icon'=>'calendar-event','text'=>'Scheduled appt · '.($a->inmate?->full_name ?? 'Inmate'),'url'=>$a->inmate? route('system_admin.inmates.show',$a->inmate) : null]; }
        } catch (\Throwable $e) {}
        usort($activities, fn($a,$b)=>($b['at']?->timestamp ?? 0) <=> ($a['at']?->timestamp ?? 0));
        $activities = array_slice($activities, 0, 12);

        return view('system_admin.doctors.show', compact('doctor','inmates','assignedIds','assignedCount','upcomingCount','activities'));
    }

    public function saveAssignments(User $doctor, Request $request)
    {
        abort_unless($doctor->role === 'doctor', 403);
        $data = $request->validate(['inmate_ids'=>'array','inmate_ids.*'=>'integer|exists:inmates,id']);
        $ids = collect($data['inmate_ids'] ?? [])->unique();
        $validIds = Inmate::where('institution_id',$doctor->institution_id)->whereIn('id',$ids)->pluck('id');
        $doctor->assignedInmates()->sync($validIds);
        if($request->expectsJson()){
            return response()->json(['ok'=>true,'assigned_count'=>$doctor->assignedInmates()->count()]);
        }
        return back()->with('success','Assignments updated.');
    }

    public function feed(User $doctor, Request $request)
    {
        abort_unless($doctor->role === 'doctor', 403);
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
        abort_unless($doctor->role === 'doctor', 403);
        $data = $request->validate([
            'inmate_id' => 'required|exists:inmates,id',
            'scheduled_for' => 'required|date',
            'reason' => 'required|string|max:500',
        ]);

        $inmate = Inmate::where('id', $data['inmate_id'])
            ->where('institution_id', $doctor->institution_id)
            ->firstOrFail();

        $appointment = Appointment::create([
            'inmate_id' => $inmate->id,
            'scheduled_by' => $doctor->id,
            'title' => '[EMERGENCY] Urgent Checkup',
            'notes' => $data['reason'],
            'scheduled_for' => $data['scheduled_for'],
            'status' => 'scheduled',
        ]);

        try { $doctor->notify(new \App\Notifications\EmergencyAppointmentScheduled($appointment)); } catch (\Throwable $e) {}

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'appointment_id' => $appointment->id]);
        }
        return back()->with('success', 'Emergency appointment scheduled and the doctor has been notified.');
    }
}
