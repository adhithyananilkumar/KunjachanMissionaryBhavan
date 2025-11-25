<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Models\Inmate;
use App\Models\Institution;
use App\Models\InmateDocument;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreInmateRequest;
use App\Http\Requests\UpdateInmateRequest;
use App\Services\AdmissionNumberGenerator;
use Illuminate\Support\Facades\Storage;

class InmateController extends Controller
{
    public function index(Request $request){
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
        return view('system_admin.inmates.index', compact('inmates','institutions','institutionId','types','type','sort'));
    }

    public function create(){
        $institutions = Institution::orderBy('name')->get();
        $inmateTypes = [
            'Child Resident' => 'child',
            'Elderly Resident' => 'elderly',
            'Mental Health Patient' => 'mental_health',
            'Rehabilitation Patient' => 'rehabilitation',
        ];
        $staff = User::orderBy('name')->get(['id','name']);
        return view('system_admin.inmates.create', compact('institutions','inmateTypes','staff'));
    }

    public function store(StoreInmateRequest $request){
        $data = $request->validated();

        // If consent checkbox checked, set consent_signed_at
        if ($request->boolean('consent_signed')) {
            $data['consent_signed_at'] = now();
        }
        // Normalize health_info to JSON structure if user typed plain text
        if (isset($data['health_info'])) {
            if (is_string($data['health_info'])) {
                $trim = trim($data['health_info']);
                $decoded = json_decode($trim, true);
                $data['health_info'] = json_last_error() === JSON_ERROR_NONE ? $decoded : ['notes' => $trim];
            }
        }

        $fileMap = [
            'photo' => 'photo_path',
            'aadhaar_card' => 'aadhaar_card_path',
            'ration_card' => 'ration_card_path',
            'panchayath_letter' => 'panchayath_letter_path',
            'disability_card' => 'disability_card_path',
            'doctor_certificate' => 'doctor_certificate_path',
            'vincent_depaul_card' => 'vincent_depaul_card_path',
        ];
        // Defer file storage until inmate is created to place under structured dirs
        $pendingFiles = [];
        foreach ($fileMap as $input => $column) {
            if ($request->hasFile($input)) {
                $pendingFiles[$column] = $request->file($input);
                unset($data[$input]);
            }
        }

    // Admission number: allow manual override if provided & valid, else generate
        if (empty($data['admitted_by'])) { $data['admitted_by'] = auth()->id(); }
        // derive age from DOB if provided
        if (!empty($data['date_of_birth'])) {
            try { $data['age'] = \Carbon\Carbon::parse($data['date_of_birth'])->age; } catch (\Throwable $e) {}
        }
        $manual = $request->input('admission_number');
        if($manual){
            // Basic format safeguard; accept if matches pattern else normalize error via validation later (skip here silently)
            if(!preg_match('/^ADM\d{4}\d{6}$/', $manual)){
                // If invalid pattern, ignore manual and proceed to generate
                $manual = null;
            } else {
                $exists = Inmate::where('admission_number',$manual)->exists();
                if($exists){ $manual = null; }
            }
        }
        if($manual){
            $data['admission_number'] = $manual;
            $inmate = Inmate::create($data);
        } else {
            $inmate = null; $attempts = 0;
            do {
                $data['admission_number'] = AdmissionNumberGenerator::generate();
                try {
                    $inmate = Inmate::create($data);
                } catch (\Illuminate\Database\QueryException $qe) {
                    if (str_contains(strtolower($qe->getMessage()), 'unique') && $attempts < 3) { $attempts++; continue; }
                    throw $qe;
                }
                break;
            } while($attempts < 3);
        }

        // Store core files into final directories with unique names
        $docsMeta = [];
            $storageDisk = \Storage::disk(config('filesystems.default'));
            foreach ($pendingFiles as $column => $file) {
                // Use immutable inmate ID based directories (admission number may change; ID won't)
                $dir = $column === 'photo_path'
                    ? \App\Support\StoragePath::inmatePhotoDir($inmate->id)
                    : \App\Support\StoragePath::inmateDocDir($inmate->id);
                $name = \App\Support\StoragePath::uniqueName($file);
                $path = $storageDisk->putFileAs($dir, $file, $name);
                $inmate->{$column} = $path;
                $docsMeta[] = [
                    'field' => $column,
                    'original' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime' => $file->getClientMimeType(),
                ];
        }
        if (!empty($pendingFiles)) { $inmate->documents = array_values(array_merge($inmate->documents ?? [], $docsMeta)); $inmate->save(); }

        // optional initial location assignment
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
                $inmate->room_location_id = $location->id;
                $inmate->save();
            }
        }

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

        if ($request->filled('doc_names')) {
            foreach ($request->doc_names as $idx => $docName) {
                if ($docName && isset($request->doc_files[$idx])) {
                    $file = $request->doc_files[$idx];
                    if ($file instanceof \Illuminate\Http\UploadedFile) {
                        $dir = \App\Support\StoragePath::inmateDocDir($inmate->id);
                        $name = \App\Support\StoragePath::uniqueName($file);
                        $path = $storageDisk->putFileAs($dir, $file, $name);
                        InmateDocument::create([
                            'inmate_id' => $inmate->id,
                            'document_name' => $docName,
                            'file_path' => $path,
                        ]);
                        try {
                            $inmate->documents = array_values(array_merge($inmate->documents ?? [], [[
                                'field' => 'extra', 'name' => $docName, 'path' => $path, 'mime' => $file->getClientMimeType()
                            ]]));
                            $inmate->save();
                        } catch (\Throwable $e) { /* ignore if column removed */ }
                    }
                }
            }
        }


        return redirect()->route('system_admin.inmates.index')->with('success', 'Inmate created successfully.');
    }

    public function edit(Inmate $inmate){
        $institutions = Institution::orderBy('name')->get(['id','name']);
        return view('system_admin.inmates.edit', compact('inmate','institutions'));
    }

    public function show(Inmate $inmate){
        $inmate->loadMissing(
            'geriatricCarePlan','mentalHealthPlan','rehabilitationPlan',
            'educationalRecords','documents','medications','labTests','therapySessionLogs','appointments','caseLogEntries','institution'
        );
        // If AJAX or explicit partial param, return tab partials
        $partial = request('partial');
        if (request()->ajax() && $partial) {
            return match($partial){
                'overview' => view('system_admin.inmates.tabs.overview', compact('inmate')),
                'medical' => view('system_admin.inmates.tabs.medical', compact('inmate')),
                'history' => view('system_admin.inmates.tabs.history', compact('inmate')),
                'documents' => view('system_admin.inmates.tabs.documents', compact('inmate')),
                'allocation' => view('system_admin.inmates.tabs.allocation', compact('inmate')),
                'settings' => view('system_admin.inmates.tabs.settings', compact('inmate')),
                default => view('system_admin.inmates.tabs.overview', compact('inmate')),
            };
        }
        return view('system_admin.inmates.show', compact('inmate'));
    }

    public function assignLocation(Request $request, Inmate $inmate)
    {
        $data = $request->validate([
            'location_id' => 'nullable|exists:locations,id'
        ]);
        // Create/validate new assignment if provided
        if(!empty($data['location_id'])){
            $location = \App\Models\Location::where('id',$data['location_id'])->where('institution_id',$inmate->institution_id)->firstOrFail();
            if($location->status === 'maintenance'){
                $msg = 'Cannot allocate to a location under maintenance.';
                return $request->wantsJson()
                    ? response()->json(['ok'=>false,'message'=>$msg], 422)
                    : back()->with('error',$msg);
            }
            $activeCount = \App\Models\LocationAssignment::where('location_id',$location->id)->whereNull('end_date')->count();
            if($location->type === 'bed' && $activeCount > 0){
                $msg = 'This bed is already occupied.';
                return $request->wantsJson()
                    ? response()->json(['ok'=>false,'message'=>$msg], 422)
                    : back()->with('error',$msg);
            }
            // Close current assignment if exists
            $current = \App\Models\LocationAssignment::where('inmate_id',$inmate->id)->whereNull('end_date')->first();
            if($current){ $current->end_date = now(); $current->save(); }
            // Create new assignment
            \App\Models\LocationAssignment::create([
                'inmate_id' => $inmate->id,
                'location_id' => $location->id,
                'start_date' => now(),
                'end_date' => null,
            ]);
        } else {
            // Clearing assignment
            $current = \App\Models\LocationAssignment::where('inmate_id',$inmate->id)->whereNull('end_date')->first();
            if($current){ $current->end_date = now(); $current->save(); }
        }
        if ($request->wantsJson()) { return response()->json(['ok'=>true,'message'=>'Location updated.']); }
        return back()->with('success','Location updated.');
    }

    /**
     * Upload/replace a single core file (photo, aadhaar_card, etc.) for an inmate.
     * Accepts field and file; returns JSON on success.
     */
    public function uploadFile(Request $request, Inmate $inmate)
    {
        $field = $request->input('field');
        $rules = [
            'field' => 'required|in:photo,aadhaar_card,ration_card,panchayath_letter,disability_card,doctor_certificate,vincent_depaul_card',
        ];
        // Conditional file validation based on field
        if ($field === 'photo') {
            $rules['file'] = 'required|image|max:2048';
        } else {
            $rules['file'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:4096';
        }
        $data = $request->validate($rules);

        $map = [
            'photo' => 'photo_path',
            'aadhaar_card' => 'aadhaar_card_path',
            'ration_card' => 'ration_card_path',
            'panchayath_letter' => 'panchayath_letter_path',
            'disability_card' => 'disability_card_path',
            'doctor_certificate' => 'doctor_certificate_path',
            'vincent_depaul_card' => 'vincent_depaul_card_path',
        ];
        $column = $map[$field];

        // Remove old file if present
        if (!empty($inmate->{$column})) {
            Storage::delete($inmate->{$column});
        }
        $file = $request->file('file');
        $dir = $field === 'photo'
            ? \App\Support\StoragePath::inmatePhotoDir($inmate->id)
            : \App\Support\StoragePath::inmateDocDir($inmate->id);
        $name = \App\Support\StoragePath::uniqueName($file);
        $path = \Storage::putFileAs($dir, $file, $name);
        $inmate->update([$n = $column => $path]);
        $disk = \Storage::disk(config('filesystems.default'));
        try {
            $url = config('filesystems.default') === 's3'
                ? $disk->temporaryUrl($path, now()->addMinutes(5))
                : $disk->url($path);
        } catch (\Throwable $e) {
            $url = null;
        }
        return response()->json([
            'ok' => true,
            'field' => $field,
            'column' => $column,
            'url' => $url,
            'path' => $path,
        ]);
    }

    /**
     * Store a new extra inmate document (name + file) via AJAX.
     */
    public function storeDocument(Request $request, Inmate $inmate)
    {
        $data = $request->validate([
            'document_name' => 'required|string|max:255',
            'doc_file' => 'required|file|max:8192',
        ]);
            $storageDisk = \Storage::disk(config('filesystems.default'));
            $file = $request->file('doc_file');
            $dir = \App\Support\StoragePath::inmateDocDir($inmate->id);
            $name = \App\Support\StoragePath::uniqueName($file);
            $path = $storageDisk->putFileAs($dir, $file, $name);
        $doc = InmateDocument::create([
            'inmate_id' => $inmate->id,
            'document_name' => $data['document_name'],
            'file_path' => $path,
        ]);
        $disk = \Storage::disk(config('filesystems.default'));
        try {
            $url = config('filesystems.default') === 's3'
                ? $disk->temporaryUrl($doc->file_path, now()->addMinutes(5))
                : $disk->url($doc->file_path);
        } catch (\Throwable $e) {
            $url = null;
        }
        return response()->json([
            'ok' => true,
            'document' => [
                'id' => $doc->id,
                'name' => $doc->document_name,
                'url' => $url,
            ]
        ]);
    }

    public function update(UpdateInmateRequest $request, Inmate $inmate){
        $data = $request->validated();

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
                    $data[$column] = \Storage::disk(config('filesystems.default'))->putFileAs($dir, $file, $name);
                $inmate->documents = array_values(array_merge($inmate->documents ?? [], [[
                    'field' => $column, 'original' => $file->getClientOriginalName(), 'path' => $data[$column], 'mime' => $file->getClientMimeType()
                ]]));
            }
        }

        $oldInstitutionId = $inmate->institution_id;
        // derive age if dob changed
        if (!empty($data['date_of_birth'])) {
            try { $data['age'] = \Carbon\Carbon::parse($data['date_of_birth'])->age; } catch (\Throwable $e) {}
        }
        // If consent checkbox checked on update, (re)stamp signed time
        if ($request->boolean('consent_signed')) {
            $data['consent_signed_at'] = now();
        }
        // Normalize health_info input if present
        if (isset($data['health_info'])) {
            if (is_string($data['health_info'])) {
                $trim = trim($data['health_info']);
                $decoded = json_decode($trim, true);
                $data['health_info'] = json_last_error() === JSON_ERROR_NONE ? $decoded : ['notes' => $trim];
            }
        }

        $inmate->update($data);

        // If institution changed, close any active location assignment tied to old institution
        if(isset($data['institution_id']) && (int)$data['institution_id'] !== (int)$oldInstitutionId){
            $current = \App\Models\LocationAssignment::where('inmate_id',$inmate->id)->whereNull('end_date')->first();
            if($current){ $current->end_date = now(); $current->save(); }
        }

        switch($inmate->type){
            case 'elderly':
                if($request->filled('mobility_status') || $request->filled('dietary_needs') || $request->filled('emergency_contact_details')){
                    $ecd = [];
                    if($request->filled('emergency_contact_details')){
                        $decoded = json_decode($request->emergency_contact_details, true);
                        if(is_array($decoded)) $ecd = $decoded;
                    }
                    $plan = $inmate->geriatricCarePlan; 
                    $payload = [
                        'mobility_status' => $request->mobility_status,
                        'dietary_needs' => $request->dietary_needs,
                        'emergency_contact_details' => $ecd,
                    ];
                    $plan ? $plan->update($payload) : $inmate->geriatricCarePlan()->create($payload);
                }
                break;
            case 'mental_health':
                if($request->filled('mh_diagnosis') || $request->filled('mh_therapy_frequency') || $request->filled('mh_current_meds')){
                    $payload = [
                        'diagnosis' => $request->mh_diagnosis,
                        'therapy_frequency' => $request->mh_therapy_frequency,
                        'current_meds' => $request->mh_current_meds,
                    ];
                    $plan = $inmate->mentalHealthPlan;
                    $plan ? $plan->update($payload) : $inmate->mentalHealthPlan()->create($payload);
                }
                break;
            case 'rehabilitation':
                if($request->filled('rehab_primary_issue') || $request->filled('rehab_program_phase') || $request->filled('rehab_goals')){
                    $payload = [
                        'primary_issue' => $request->rehab_primary_issue,
                        'program_phase' => $request->rehab_program_phase,
                        'goals' => $request->rehab_goals,
                    ];
                    $plan = $inmate->rehabilitationPlan;
                    $plan ? $plan->update($payload) : $inmate->rehabilitationPlan()->create($payload);
                }
                break;
            case 'child':
                break;
        }

        if ($request->filled('doc_names')) {
            foreach ($request->doc_names as $idx => $docName) {
                if ($docName && isset($request->doc_files[$idx])) {
                    $file = $request->doc_files[$idx];
                    if ($file instanceof \Illuminate\Http\UploadedFile) {
                        $storageDisk = \Storage::disk(config('filesystems.default'));
                        $dir = \App\Support\StoragePath::inmateDocDir($inmate->id);
                        $name = \App\Support\StoragePath::uniqueName($file);
                        $path = $storageDisk->putFileAs($dir, $file, $name);
                        InmateDocument::create([
                            'inmate_id' => $inmate->id,
                            'document_name' => $docName,
                            'file_path' => $path,
                        ]);
                        try {
                            $inmate->documents = array_values(array_merge($inmate->documents ?? [], [[
                                'field' => 'extra', 'name' => $docName, 'path' => $path, 'mime' => $file->getClientMimeType()
                            ]]));
                            $inmate->save();
                        } catch (\Throwable $e) { /* ignore if column removed */ }
                    }
                }
            }
        }


        if ($request->wantsJson()) {
            return response()->json(['ok'=>true,'message'=>'Inmate updated successfully.']);
        }
        return redirect()->route('system_admin.inmates.edit',$inmate)->with('success', 'Inmate updated successfully.');
    }

    public function destroy(Inmate $inmate){
        $inmate->delete();
        return redirect()->route('system_admin.inmates.index')->with('success', 'Inmate deleted successfully.');
    }

    /**
     * Toggle guardian share flag for an inmate document (System Admin scope).
     */
    public function toggleDocumentShare(Request $request, Inmate $inmate, InmateDocument $document)
    {
        abort_unless($document->inmate_id === $inmate->id, 404);
        $document->is_sharable_with_guardian = !$document->is_sharable_with_guardian;
        $document->save();
        return $request->wantsJson()
            ? response()->json(['ok'=>true,'shared'=>$document->is_sharable_with_guardian])
            : back()->with('success', 'Share setting updated.');
    }
}
