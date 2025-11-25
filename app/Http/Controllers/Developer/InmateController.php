<?php
namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\Inmate;
use App\Models\Institution;
use App\Models\InmateDocument;
use App\Models\MentalHealthPlan;
use App\Models\RehabilitationPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InmateController extends Controller
{
    public function index(Request $request)
    {
        $query = Inmate::with('institution');
        $institutionId = $request->get('institution_id');
        $type = $request->get('type');
        $sort = $request->get('sort','name_asc');
        if($institutionId){ $query->where('institution_id',$institutionId); }
        if($type){ $query->where('type',$type); }
        match($sort){
            'name_desc' => $query->orderBy('first_name','desc'),
            'created_asc' => $query->orderBy('id','asc'),
            'created_desc' => $query->orderBy('id','desc'),
            default => $query->orderBy('first_name','asc'),
        };
        $inmates = $query->paginate(15)->appends($request->only('institution_id','type','sort'));
        $institutions = Institution::orderBy('name')->get(['id','name']);
        $types = Inmate::select('type')->whereNotNull('type')->where('type','!=','')->distinct()->orderBy('type')->pluck('type');
        return view('developer.inmates.index', compact('inmates','institutions','institutionId','types','type','sort'));
    }

    public function create()
    {
        $institutions = Institution::orderBy('name')->get();
        $inmateTypes = [
            'Child Resident' => 'child',
            'Elderly Resident' => 'elderly',
            'Mental Health Patient' => 'mental_health',
            'Rehabilitation Patient' => 'rehabilitation',
        ];
        return view('developer.inmates.create', compact('institutions','inmateTypes'));
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
            'type' => 'required|in:child,elderly,mental_health,rehabilitation',
            'intake_history' => 'nullable|string',
            // Geriatric
            'mobility_status' => 'nullable|string|max:255',
            'dietary_needs' => 'nullable|string|max:255',
            'emergency_contact_details' => 'nullable|string',
            // Rehab
            'rehab_primary_issue' => 'nullable|string|max:255',
            'rehab_program_phase' => 'nullable|string|max:255',
            'rehab_goals' => 'nullable|string',
            // Mental Health
            'mh_diagnosis' => 'nullable|string|max:255',
            'mh_therapy_frequency' => 'nullable|string|max:255',
            'mh_current_meds' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'aadhaar_card' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'ration_card' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'panchayath_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'disability_card' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'doctor_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'vincent_depaul_card' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'doc_names.*' => 'nullable|string|max:255',
            'doc_files.*' => 'nullable|file|max:8192',
        ]);

    // Handle file uploads to default disk
        $fileMap = [
            'photo' => 'photo_path',
            'aadhaar_card' => 'aadhaar_card_path',
            'ration_card' => 'ration_card_path',
            'panchayath_letter' => 'panchayath_letter_path',
            'disability_card' => 'disability_card_path',
            'doctor_certificate' => 'doctor_certificate_path',
            'vincent_depaul_card' => 'vincent_depaul_card_path',
        ];
        $pendingFiles = [];
        foreach ($fileMap as $input => $column) {
            if ($request->hasFile($input)) {
                $pendingFiles[$column] = $request->file($input);
                unset($data[$input]);
            }
        }

        $inmate = Inmate::create($data);

        // Store core files with structured dirs
        foreach ($pendingFiles as $column => $file) {
            $dir = $column === 'photo_path'
                ? \App\Support\StoragePath::inmatePhotoDir($inmate->id)
                : \App\Support\StoragePath::inmateDocDir($inmate->id);
            $name = \App\Support\StoragePath::uniqueName($file);
            $path = \Storage::putFileAs($dir, $file, $name);
            $inmate->{$column} = $path;
        }
        if (!empty($pendingFiles)) { $inmate->save(); }

        // Type specific persistence (basic examples; expand as needed)
        if($data['type']==='elderly'){
            if($request->filled('mobility_status') || $request->filled('dietary_needs') || $request->filled('emergency_contact_details')){
                $ecd = null; if($request->filled('emergency_contact_details')){ $ecd = json_decode($request->emergency_contact_details, true); }
                $inmate->geriatricCarePlan()->create([
                    'mobility_status' => $request->mobility_status,
                    'dietary_needs' => $request->dietary_needs,
                    'emergency_contact_details' => $ecd ?? []
                ]);
            }
        }
        if($data['type']==='child' && $request->filled('intake_history')){
            // intake_history already saved on inmate
        }
        if($data['type']==='mental_health'){
            if($request->filled('mh_diagnosis') || $request->filled('mh_therapy_frequency') || $request->filled('mh_current_meds')){
                $inmate->mentalHealthPlan()->create([
                    'diagnosis' => $request->mh_diagnosis,
                    'therapy_frequency' => $request->mh_therapy_frequency,
                    'current_meds' => $request->mh_current_meds,
                ]);
            }
        }
        if($data['type']==='rehabilitation'){
            if($request->filled('rehab_primary_issue') || $request->filled('rehab_program_phase') || $request->filled('rehab_goals')){
                $inmate->rehabilitationPlan()->create([
                    'primary_issue' => $request->rehab_primary_issue,
                    'program_phase' => $request->rehab_program_phase,
                    'goals' => $request->rehab_goals,
                ]);
            }
        }
        // Additional type-specific models (rehab, mental health) could be added similarly later.

        // Extra documents
    if ($request->filled('doc_names')) {
            foreach ($request->doc_names as $idx => $docName) {
                if ($docName && isset($request->doc_files[$idx])) {
            $file = $request->doc_files[$idx];
            $dir = \App\Support\StoragePath::inmateDocDir($inmate->id);
            $name = \App\Support\StoragePath::uniqueName($file);
            $path = \Storage::putFileAs($dir, $file, $name);
                    InmateDocument::create([
                        'inmate_id' => $inmate->id,
                        'document_name' => $docName,
                        'file_path' => $path,
                    ]);
                }
            }
        }

        return redirect()->route('developer.inmates.index')->with('success', 'Inmate created successfully.');
    }

    public function edit(Inmate $inmate)
    {
        return view('developer.inmates.edit', compact('inmate'));
    }

    public function show(Inmate $inmate)
    {
        return view('developer.inmates.show', compact('inmate'));
    }

    public function update(Request $request, Inmate $inmate)
    {
        $data = $request->validate([
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
            'photo' => 'nullable|image|max:2048',
            'aadhaar_card' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'ration_card' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'panchayath_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'disability_card' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'doctor_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'vincent_depaul_card' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'doc_names.*' => 'nullable|string|max:255',
            'doc_files.*' => 'nullable|file|max:8192',
        ]);

        $fileMap = [
            'photo' => 'photo_path',
            'aadhaar_card' => 'aadhaar_card_path',
            'ration_card' => 'ration_card_path',
            'panchayath_letter' => 'panchayath_letter_path',
            'disability_card' => 'disability_card_path',
            'doctor_certificate' => 'doctor_certificate_path',
            'vincent_depaul_card' => 'vincent_depaul_card_path',
        ];
        foreach ($fileMap as $input => $column) {
            if ($request->hasFile($input)) {
                if ($inmate->{$column}) {
                    Storage::delete($inmate->{$column});
                }
                $file = $request->file($input);
                $dir = $input === 'photo'
                    ? \App\Support\StoragePath::inmatePhotoDir($inmate->id)
                    : \App\Support\StoragePath::inmateDocDir($inmate->id);
                $name = \App\Support\StoragePath::uniqueName($file);
                $data[$column] = \Storage::putFileAs($dir, $file, $name);
            }
        }

        $inmate->update($data);

    if ($request->filled('doc_names')) {
            foreach ($request->doc_names as $idx => $docName) {
                if ($docName && isset($request->doc_files[$idx])) {
            $file = $request->doc_files[$idx];
            $dir = \App\Support\StoragePath::inmateDocDir($inmate->id);
            $name = \App\Support\StoragePath::uniqueName($file);
            $path = \Storage::putFileAs($dir, $file, $name);
                    InmateDocument::create([
                        'inmate_id' => $inmate->id,
                        'document_name' => $docName,
                        'file_path' => $path,
                    ]);
                }
            }
        }

        return redirect()->route('developer.inmates.index')->with('success', 'Inmate updated successfully.');
    }

    public function destroy(Inmate $inmate)
    {
        $inmate->delete();
        return redirect()->route('developer.inmates.index')->with('success', 'Inmate deleted successfully.');
    }
}
