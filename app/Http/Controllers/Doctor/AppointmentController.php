<?php
namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Inmate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        return view('doctor.appointments.calendar');
    }

    public function feed(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');
        $doctor = Auth::user();
        // Doctors can view all inmates' appointments in their institution
        $query = Appointment::with('inmate')
            ->whereHas('inmate', function ($q) use ($doctor) {
                $q->where('institution_id', $doctor->institution_id);
            });
        if ($start) {
            $query->where('scheduled_for', '>=', $start);
        }
        if ($end) {
            $query->where('scheduled_for', '<=', $end);
        }
        $events = $query->get()->map(function ($a) {
            $isEmergency = str_contains(strtoupper($a->title), 'EMERGENCY');
            return [
                'id' => $a->id,
                'title' => $a->title . ' - ' . ($a->inmate->full_name ?? 'Inmate #' . $a->inmate_id),
                'start' => $a->scheduled_for->toDateString(),
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

    public function store(Request $request)
    {
        $data = $request->validate([
            'inmate_id' => 'required|exists:inmates,id',
            'title' => 'required|string|max:255',
            'scheduled_for' => 'required', // accept multiple formats
            'notes' => 'nullable|string',
            'status' => 'sometimes|in:scheduled,completed,cancelled'
        ]);
        $inmate = Inmate::where('id', $data['inmate_id'])
            ->where('institution_id', Auth::user()->institution_id)
            ->firstOrFail();
        if(optional(Auth::user()->institution)->doctor_assignment_enabled){
            abort_unless(
                $inmate->doctor_id === Auth::id() || \DB::table('doctor_inmate')->where('inmate_id',$inmate->id)->where('doctor_id',Auth::id())->exists(),
                403
            );
        }
        $data['scheduled_by'] = Auth::id();
        if(!isset($data['status'])) { $data['status'] = 'scheduled'; }
        $parsed = $this->parseDate($data['scheduled_for']);
        if(!$parsed){
            return response()->json(['ok'=>false,'message'=>'Invalid date format (use YYYY-MM-DD or DD/MM/YYYY).'],422);
        }
        $data['scheduled_for'] = $parsed->format('Y-m-d');
        $appointment = Appointment::create($data);
        return response()->json([
            'ok' => true,
            'message' => 'Appointment created successfully!',
            'appointment' => [
                'id' => $appointment->id,
                'title' => $appointment->title,
                'scheduled_for' => $appointment->scheduled_for->toIso8601String(),
                'start' => $appointment->scheduled_for->toDateString(),
                'inmate_id' => $appointment->inmate_id,
                'status' => $appointment->status,
                'notes' => $appointment->notes,
            ]
        ]);
    }

    public function update(Request $request, Appointment $appointment)
    {
        // Authorization check (use inmate relation institution since model has no direct institution_id column)
        $doctor = Auth::user();
        abort_unless($appointment->inmate->institution_id === $doctor->institution_id, 403);
        if (optional($doctor->institution)->doctor_assignment_enabled) {
            abort_unless(
                $appointment->inmate->doctor_id === $doctor->id || \DB::table('doctor_inmate')->where('inmate_id',$appointment->inmate_id)->where('doctor_id',$doctor->id)->exists(),
                403
            );
        }

        try {
            $validated = $request->validate([
                'inmate_id' => 'required|exists:inmates,id',
                'title' => 'required|string|max:255',
                'scheduled_for' => 'required', // multi-format parse
                'notes' => 'nullable|string',
                'status' => 'required|string|in:scheduled,completed,cancelled',
            ]);

            // Ensure inmate belongs to same institution
            $inmate = Inmate::where('id',$validated['inmate_id'])
                ->where('institution_id', $doctor->institution_id)
                ->firstOrFail();
            if(optional($doctor->institution)->doctor_assignment_enabled){
                abort_unless(
                    $inmate->doctor_id === $doctor->id || \DB::table('doctor_inmate')->where('inmate_id',$inmate->id)->where('doctor_id',$doctor->id)->exists(),
                    403
                );
            }

            $parsed = $this->parseDate($validated['scheduled_for']);
            if(!$parsed){ return response()->json(['message'=>'Invalid date format.'],422); }
            $appointment->update([
                'inmate_id' => $inmate->id,
                'title' => $validated['title'],
                'scheduled_for' => $parsed->format('Y-m-d'),
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'],
            ]);

            return response()->json([
                'ok' => true,
                'message' => 'Appointment updated successfully!',
                'appointment' => $appointment->fresh()->load('inmate')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation Failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Appointment update failed: '.$e->getMessage());
            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }

    public function destroy(Appointment $appointment)
    {
        abort_unless($appointment->inmate->institution_id === Auth::user()->institution_id, 403);
        $appointment->delete();
        return response()->json(['ok' => true]);
    }

    private function parseDate(string $value): ?\Carbon\Carbon
    {
        $value = trim($value);
        $formats = ['Y-m-d','d/m/Y'];
        foreach($formats as $fmt){
            try{ $dt = \Carbon\Carbon::createFromFormat($fmt,$value); if($dt!==false){ return $dt; } }catch(\Throwable $e){}
        }
        return null;
    }
}
