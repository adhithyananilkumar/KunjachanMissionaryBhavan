<?php
namespace App\Http\Controllers\Doctor;
use App\Http\Controllers\Controller;
use App\Models\Inmate;

class InmateController extends Controller
{
    public function index()
    {
        $doctor = auth()->user();
        $query = Inmate::query()->where('institution_id', $doctor->institution_id);
        // Search by name or registration number
        $q = request()->get('q');
        if($q){
            $term = '%'.trim($q).'%';
            $query->where(function($sub) use($term){
                $sub->where('first_name','like',$term)
                    ->orWhere('last_name','like',$term)
                    ->orWhere('registration_number','like',$term);
            });
        }
        if ($doctor->institution && $doctor->institution->doctor_assignment_enabled) {
            // If enabled, list only assigned inmates via pivot or legacy column
            $query->where(function($q) use ($doctor){
                $q->where('doctor_id', $doctor->id) // legacy
                  ->orWhereExists(function($sub) use ($doctor){
                      $sub->selectRaw('1')
                          ->from('doctor_inmate as di')
                          ->whereColumn('di.inmate_id','inmates.id')
                          ->where('di.doctor_id',$doctor->id);
                  });
            });
        }
        $inmates = $query->orderBy('first_name')->orderBy('last_name')->paginate(15)->appends(request()->only('q'));
        $search = $q;
        return view('doctor.inmates.index', compact('inmates','search'));
    }

    public function show(Inmate $inmate)
    {
        $doctor = auth()->user();
        abort_unless($inmate->institution_id === $doctor->institution_id, 403);
    // New requirement: doctors can view medical history and reports for all inmates in their institution even if not assigned
        $inmate->load(['medicalRecords.doctor']);
        return view('doctor.inmates.show', compact('inmate'));
    }
}
