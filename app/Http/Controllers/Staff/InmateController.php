<?php
namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Inmate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InmateController extends Controller
{
    public function index()
    {
        $staff = auth()->user();
        $inmates = Inmate::where('institution_id', $staff->institution_id)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->paginate(15);
        return view('staff.inmates.index', compact('inmates'));
    }
    public function create()
    {
        $institution = Auth::user()->institution; // lock to staff institution
        $inmateTypes = [
            'Child Resident' => 'child',
            'Elderly Resident' => 'elderly',
            'Mental Health Patient' => 'mental_health',
            'Rehabilitation Patient' => 'rehabilitation',
        ];
        return view('staff.inmates.create', compact('institution','inmateTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'registration_number' => 'nullable|string|max:100',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'admission_date' => 'required|date',
            'notes' => 'nullable|string',
            'critical_alert' => 'nullable|string|max:1000',
            'guardian_relation' => 'nullable|string|max:100',
            'guardian_first_name' => 'nullable|string|max:255',
            'guardian_last_name' => 'nullable|string|max:255',
            'guardian_email' => 'nullable|email|max:255',
            'guardian_phone' => 'nullable|string|max:50',
            'guardian_address' => 'nullable|string',
            'aadhaar_number' => 'nullable|string|max:100',
            'type' => 'nullable|string|max:50',
            'intake_history' => 'nullable|string',
            'mobility_status' => 'nullable|string|max:255',
            'dietary_needs' => 'nullable|string|max:255',
            'emergency_contact_details' => 'nullable|string',
            'rehab_primary_issue' => 'nullable|string|max:255',
            'rehab_program_phase' => 'nullable|string|max:255',
            'rehab_goals' => 'nullable|string',
            'mh_diagnosis' => 'nullable|string|max:255',
            'mh_therapy_frequency' => 'nullable|string|max:255',
            'mh_current_meds' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'aadhaar_card' => 'nullable|file|max:8192',
            'ration_card' => 'nullable|file|max:8192',
            'panchayath_letter' => 'nullable|file|max:8192',
            'disability_card' => 'nullable|file|max:8192',
            'doctor_certificate' => 'nullable|file|max:8192',
            'vincent_depaul_card' => 'nullable|file|max:8192',
            'doc_names.*' => 'nullable|string|max:255',
            'doc_files.*' => 'nullable|file|max:8192',
            'location_id' => 'nullable|integer',
        ]);

        // Enforce staff's institution
        $data['institution_id'] = Auth::user()->institution_id;

        // Optional photo upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $dir = \App\Support\StoragePath::inmatePhotoDir('new');
            $name = \App\Support\StoragePath::uniqueName($file);
            $data['photo_path'] = \Storage::putFileAs($dir, $file, $name);
        }

        // Optional standard documents (same set admins can upload)
        foreach([
            'aadhaar_card' => 'aadhaar_card_path',
            'ration_card' => 'ration_card_path',
            'panchayath_letter' => 'panchayath_letter_path',
            'disability_card' => 'disability_card_path',
            'doctor_certificate' => 'doctor_certificate_path',
            'vincent_depaul_card' => 'vincent_depaul_card_path',
        ] as $input => $column){
            if($request->hasFile($input)){
                $file = $request->file($input);
                $dir = \App\Support\StoragePath::inmateDocDir('new');
                $name = \App\Support\StoragePath::uniqueName($file);
                $data[$column] = \Storage::putFileAs($dir, $file, $name);
            }
        }

        $inmate = Inmate::create($data);

        // Optional initial location assignment (validate within same institution)
        if($request->filled('location_id')){
            $location = \App\Models\Location::where('id',$request->input('location_id'))
                ->where('institution_id', $inmate->institution_id)->first();
            if($location){
                \App\Models\LocationAssignment::create([
                    'inmate_id' => $inmate->id,
                    'location_id' => $location->id,
                    'start_date' => now(),
                    'end_date' => null,
                ]);
            }
        }

        // Type-specific records
        if($inmate->type==='elderly'){
            if($request->filled('mobility_status') || $request->filled('dietary_needs') || $request->filled('emergency_contact_details')){
                $ecd = $request->filled('emergency_contact_details') ? json_decode($request->emergency_contact_details,true) : [];
                $inmate->geriatricCarePlan()->create([
                    'mobility_status'=>$request->mobility_status,
                    'dietary_needs'=>$request->dietary_needs,
                    'emergency_contact_details'=>$ecd ?? [],
                ]);
            }
        }
        if($inmate->type==='mental_health'){
            if($request->filled('mh_diagnosis') || $request->filled('mh_therapy_frequency') || $request->filled('mh_current_meds')){
                $inmate->mentalHealthPlan()->create([
                    'diagnosis'=>$request->mh_diagnosis,
                    'therapy_frequency'=>$request->mh_therapy_frequency,
                    'current_meds'=>$request->mh_current_meds,
                ]);
            }
        }
        if($inmate->type==='rehabilitation'){
            if($request->filled('rehab_primary_issue') || $request->filled('rehab_program_phase') || $request->filled('rehab_goals')){
                $inmate->rehabilitationPlan()->create([
                    'primary_issue'=>$request->rehab_primary_issue,
                    'program_phase'=>$request->rehab_program_phase,
                    'goals'=>$request->rehab_goals,
                ]);
            }
        }

        // Additional docs uploads (names + files)
        if ($request->filled('doc_names')) {
            foreach ($request->doc_names as $idx => $docName) {
                if ($docName && isset($request->doc_files[$idx])) {
                    $file = $request->doc_files[$idx];
                    $dir = \App\Support\StoragePath::inmateDocDir($inmate->id);
                    $name = \App\Support\StoragePath::uniqueName($file);
                    $path = \Storage::putFileAs($dir, $file, $name);
                    \App\Models\InmateDocument::create([
                        'inmate_id' => $inmate->id,
                        'document_name' => $docName,
                        'file_path' => $path,
                    ]);
                }
            }
        }

        return redirect()->route('staff.inmates.index')->with('status','Inmate registered successfully.');
    }
}
