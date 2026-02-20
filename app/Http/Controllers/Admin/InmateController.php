<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inmate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Location;
use App\Models\LocationAssignment;
use App\Models\InmateDocument;
use App\Models\InmateDocumentArchive;
use Illuminate\Database\Eloquent\Builder;

class InmateController extends Controller
{
    public function index(Request $request)
    {
        $institutionId = Auth::user()->institution_id;

        $query = Inmate::query()
            ->where('institution_id', $institutionId)
            ->with('institution');

        $search = trim((string) $request->get('search', ''));
        $type = $request->get('type');
        $sort = $request->get('sort', 'created_desc');

        if ($search !== '') {
            $query->where(function (Builder $q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(first_name,' ',COALESCE(last_name,'')) like ?", ["%{$search}%"])
                    ->orWhere('admission_number', 'like', "%{$search}%");
            });

            $safe = str_replace(['%', '_'], ['\\%', '\\_'], $search);
            $prefix = $safe . '%';
            $contains = '%' . $safe . '%';

            $query->orderByRaw(
                "CASE\n"
                . " WHEN CONCAT(first_name,' ',COALESCE(last_name,'')) LIKE ? THEN 1\n"
                . " WHEN first_name LIKE ? THEN 2\n"
                . " WHEN last_name LIKE ? THEN 3\n"
                . " WHEN admission_number LIKE ? THEN 4\n"
                . " WHEN CONCAT(first_name,' ',COALESCE(last_name,'')) LIKE ? THEN 5\n"
                . " WHEN first_name LIKE ? THEN 6\n"
                . " WHEN last_name LIKE ? THEN 7\n"
                . " ELSE 8\n"
                . "END",
                [$prefix, $prefix, $prefix, $prefix, $contains, $contains, $contains]
            );
        }

        if ($type) {
            $query->where('type', $type);
        }

        match ($sort) {
            'name_asc' => $query->orderBy('first_name', 'asc')->orderBy('id', 'asc'),
            'name_desc' => $query->orderBy('first_name', 'desc')->orderBy('id', 'asc'),
            'created_asc' => $query->orderBy('id', 'asc'),
            default => $query->orderBy('id', 'desc'),
        };

        $inmates = $query->paginate(15)->appends($request->only('search', 'type', 'sort'));

        if ($request->ajax()) {
            return view('admin.inmates._list', compact('inmates'));
        }

        return view('admin.inmates.index', compact('inmates'));
    }

    public function create()
    {
        $institution = Auth::user()->institution;
        $features = $institution->enabled_features ?? [];

        $types = [];
        if (in_array('orphan_care', $features)) {
            $types[] = ['value' => 'child_resident', 'label' => 'Child Resident'];
        }
        if (in_array('elderly_care', $features)) {
            $types[] = ['value' => 'elderly_resident', 'label' => 'Elderly Resident'];
        }
        if (in_array('mental_health', $features)) {
            $types[] = ['value' => 'mental_health_patient', 'label' => 'Mental Health Patient'];
        }

        if (empty($types)) {
            $types[] = ['value' => 'undefined', 'label' => 'Other / Undefined'];
        }
        return view('admin.inmates.create', compact('types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'nullable|string|max:50',
            'admission_number' => ['required','string','max:32','regex:/^(ADM\d{10}|\d{1,20})$/','unique:inmates,admission_number'],
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'admission_date' => 'required|date',
            'notes' => 'nullable|string',
            'intake_history' => 'nullable|string',
            'guardian_relation' => 'nullable|string|max:100',
            'guardian_first_name' => 'nullable|string|max:255',
            'guardian_last_name' => 'nullable|string|max:255',
            'guardian_email' => 'nullable|email|max:255',
            'guardian_phone' => 'nullable|string|max:50',
            'guardian_address' => 'nullable|string',
            'aadhaar_number' => 'nullable|string|max:100',
            'photo' => 'nullable|file|mimes:jpg,jpeg,png,webp,heic,heif|max:8192',
            'aadhaar_card' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,heic,heif|max:10240',
            'ration_card' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,heic,heif|max:10240',
            'panchayath_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,heic,heif|max:10240',
            'disability_card' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,heic,heif|max:10240',
            'doctor_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,heic,heif|max:10240',
            'vincent_depaul_card' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,heic,heif|max:10240',
            'doc_names.*' => 'nullable|string|max:255',
            'doc_files.*' => 'nullable|file|max:10240',
        ]);
        $data['institution_id'] = Auth::user()->institution_id;
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
                $dir = $input === 'photo'
                    ? \App\Support\StoragePath::inmatePhotoDir(0) // replaced below once inmate exists
                    : \App\Support\StoragePath::inmateDocDir(0);
                // Temporarily store; will move after create to proper inmate id path
                $data[$column] = $request->file($input)->store('tmp');
            }
        }
        $inmate = Inmate::create($data);
        // Move any core files uploaded during create to final structured dirs
        foreach ([
            'photo_path' => \App\Support\StoragePath::inmatePhotoDir($inmate->id),
            'aadhaar_card_path' => \App\Support\StoragePath::inmateDocDir($inmate->id),
            'ration_card_path' => \App\Support\StoragePath::inmateDocDir($inmate->id),
            'panchayath_letter_path' => \App\Support\StoragePath::inmateDocDir($inmate->id),
            'disability_card_path' => \App\Support\StoragePath::inmateDocDir($inmate->id),
            'doctor_certificate_path' => \App\Support\StoragePath::inmateDocDir($inmate->id),
            'vincent_depaul_card_path' => \App\Support\StoragePath::inmateDocDir($inmate->id),
        ] as $column => $dir) {
            if (!empty($inmate->{$column}) && str_starts_with($inmate->{$column}, 'tmp/')) {
                $name = basename($inmate->{$column});
                $final = $dir.'/'.$name;
                if (\Storage::move($inmate->{$column}, $final)) {
                    $inmate->{$column} = $final;
                }
            }
        }
        $inmate->save();
        if ($request->filled('doc_names')) {
            foreach ($request->doc_names as $idx => $docName) {
                if ($docName && isset($request->doc_files[$idx])) {
                    $path = $request->doc_files[$idx]->store('tmp');
                    $doc = InmateDocument::create([
                        'inmate_id' => $inmate->id,
                        'document_name' => $docName,
                        'file_path' => $path,
                    ]);
                    // Move to final
                    $finalDir = \App\Support\StoragePath::inmateDocDir($inmate->id);
                    $name = basename($path);
                    $final = $finalDir.'/'.$name;
                    if (\Storage::move($path, $final)) { $doc->update(['file_path'=>$final]); }
                }
            }
        }
        return redirect()->route('admin.inmates.index')->with('success','Inmate created successfully.');
    }

    public function edit(Inmate $inmate)
    {
        $this->authorizeAccess($inmate);
        $institution = Auth::user()->institution; $features = $institution->enabled_features ?? [];
        $types = [];
        if(in_array('orphan_care',$features)){
            $types[] = ['value'=>'child_resident','label'=>'Child Resident'];
        }
        if(in_array('elderly_care',$features)){
            $types[] = ['value'=>'elderly_resident','label'=>'Elderly Resident'];
        }
        if(in_array('mental_health',$features)){
            $types[] = ['value'=>'mental_health_patient','label'=>'Mental Health Patient'];
        }
        if(in_array('rehabilitation',$features)){
            $types[] = ['value'=>'rehabilitation_patient','label'=>'Rehabilitation Patient'];
        }
        if(in_array('undefined_inmate',$features)){
            $types[] = ['value'=>'undefined','label'=>'Other / Undefined'];
        }
        return view('admin.inmates.edit', compact('inmate','types'));
    }

    public function show(Inmate $inmate)
    {
        $this->authorizeAccess($inmate);
        $inmate->loadMissing(
            'geriatricCarePlan','mentalHealthPlan','rehabilitationPlan',
            'educationalRecords','documents','medications','labTests','therapySessionLogs','appointments','caseLogEntries','institution'
        );
        $partial = request('partial');
        if (request()->ajax() && $partial) {
            return match($partial){
                'overview' => view('admin.inmates.tabs.overview', compact('inmate')),
                'medical' => view('admin.inmates.tabs.medical', compact('inmate')),
                'history' => view('admin.inmates.tabs.history', compact('inmate')),
                'documents' => view('admin.inmates.tabs.documents', compact('inmate')),
                'allocation' => view('admin.inmates.tabs.allocation', compact('inmate')),
                'settings' => view('admin.inmates.tabs.settings', compact('inmate')),
                default => view('admin.inmates.tabs.overview', compact('inmate')),
            };
        }
        return view('admin.inmates.show', compact('inmate'));
    }

    public function assignLocation(Request $request, Inmate $inmate)
    {
        $this->authorizeAccess($inmate);
        $data = $request->validate([
            'location_id' => 'nullable|exists:locations,id'
        ]);
        // Create/validate new assignment if provided
        if(!empty($data['location_id'])){
            $location = Location::where('id',$data['location_id'])->where('institution_id',Auth::user()->institution_id)->firstOrFail();
            if($location->status === 'maintenance'){
                $msg = 'Cannot allocate to a location under maintenance.';
                return $request->wantsJson()
                    ? response()->json(['ok'=>false,'message'=>$msg], 422)
                    : back()->with('error',$msg);
            }
            $activeCount = LocationAssignment::where('location_id',$location->id)->whereNull('end_date')->count();
            if($location->type === 'bed' && $activeCount > 0){
                $msg = 'This bed is already occupied.';
                return $request->wantsJson()
                    ? response()->json(['ok'=>false,'message'=>$msg], 422)
                    : back()->with('error',$msg);
            }
            // Close current assignment if exists
            $current = LocationAssignment::where('inmate_id',$inmate->id)->whereNull('end_date')->first();
            if($current){ $current->end_date = now(); $current->save(); }
            // Create new assignment
            LocationAssignment::create([
                'inmate_id' => $inmate->id,
                'location_id' => $location->id,
                'start_date' => now(),
                'end_date' => null,
            ]);
        } else {
            // If clearing assignment: close current if exists
            $current = LocationAssignment::where('inmate_id',$inmate->id)->whereNull('end_date')->first();
            if($current){ $current->end_date = now(); $current->save(); }
        }
        return $request->wantsJson()
            ? response()->json(['ok'=>true,'message'=>'Location updated.'])
            : back()->with('success','Location updated.');
    }

    public function assignDoctor(Request $request, Inmate $inmate)
    {
        $this->authorizeAccess($inmate);
        $data = $request->validate([
            'doctor_id' => 'nullable|exists:users,id'
        ]);
        // ensure doctor belongs to same institution and has role doctor
        if (!empty($data['doctor_id'])) {
            $doctor = \App\Models\User::where('id', $data['doctor_id'])
                ->where('institution_id', auth()->user()->institution_id)
                ->where('role', 'doctor')
                ->firstOrFail();
            // assign doctor
            $inmate->doctor_id = $doctor->id;
        } else {
            $inmate->doctor_id = null;
        }
        $inmate->save();
        return back()->with('success', 'Doctor assignment updated.');
    }

    public function transferDoctor(Request $request, Inmate $inmate)
    {
        $this->authorizeAccess($inmate);
        $data = $request->validate([
            'to_doctor_id' => 'required|exists:users,id',
            'reason' => 'nullable|string|max:1000',
        ]);
        $from = $inmate->doctor_id; // may be null
        $toDoctor = \App\Models\User::where('id',$data['to_doctor_id'])
            ->where('institution_id', auth()->user()->institution_id)
            ->where('role','doctor')
            ->firstOrFail();
        if($from === $toDoctor->id){ return back()->with('error','Already assigned to this doctor.'); }
        // Log handoff
        \App\Models\DoctorHandoff::create([
            'inmate_id' => $inmate->id,
            'from_doctor_id' => $from,
            'to_doctor_id' => $toDoctor->id,
            'admin_id' => auth()->id(),
            'reason' => $data['reason'] ?? null,
        ]);
        // Update assignment
        $inmate->doctor_id = $toDoctor->id;
        $inmate->save();
        // Notify old and new doctors (best-effort)
        try{
            if($from){
                $old = \App\Models\User::find($from);
                if($old){ $old->notify(new \App\Notifications\TransferOfCareNotification($inmate, 'outgoing', $toDoctor)); }
            }
            $toDoctor->notify(new \App\Notifications\TransferOfCareNotification($inmate, 'incoming'));
        }catch(\Throwable $e){}
        return back()->with('success','Patient transferred successfully.');
    }

    /**
     * Upload/replace a single core file (photo, aadhaar_card, etc.) for an inmate (Admin scope).
     * Accepts field and file; returns JSON on success.
     */
    public function uploadFile(Request $request, Inmate $inmate)
    {
        $this->authorizeAccess($inmate);
        $field = $request->input('field');
        $rules = [
            'field' => 'required|in:photo,aadhaar_card,ration_card,panchayath_letter,disability_card,doctor_certificate,vincent_depaul_card',
        ];
        $messages = [
            'file.mimes' => 'Invalid file type. Allowed: PDF, JPG, JPEG, PNG, WEBP, HEIC, HEIF.',
            'file.max' => 'File too large. Max allowed is 10 MB.',
        ];
        // Conditional file validation based on field
        if ($field === 'photo') {
            $rules['file'] = 'required|file|mimes:jpg,jpeg,png,webp,heic,heif|max:8192';
            $messages['file.mimes'] = 'Invalid photo type. Allowed: JPG, JPEG, PNG, WEBP, HEIC, HEIF.';
            $messages['file.max'] = 'Photo too large. Max allowed is 8 MB.';
        } else {
            $rules['file'] = 'required|file|mimes:pdf,jpg,jpeg,png,webp,heic,heif|max:10240';
        }
        $data = $request->validate($rules, $messages);

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

        // Archive old file if present (keep history)
        if (!empty($inmate->{$column})) {
            try {
                InmateDocumentArchive::create([
                    'inmate_id' => $inmate->id,
                    'source_type' => 'core',
                    'source_key' => $field,
                    'document_name' => str_replace('_', ' ', ucfirst($field)),
                    'file_path' => $inmate->{$column},
                    'archived_by' => auth()->id(),
                    'archived_at' => now(),
                ]);
            } catch (\Throwable $e) {
                // best-effort archival
            }
        }
        $file = $request->file('file');
        $dir = $field === 'photo'
            ? \App\Support\StoragePath::inmatePhotoDir($inmate->id)
            : \App\Support\StoragePath::inmateDocDir($inmate->id);
        $name = \App\Support\StoragePath::uniqueName($file);
        $path = \Storage::putFileAs($dir, $file, $name);
        $inmate->update([$column => $path]);

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
     * Store a new extra inmate document (name + file) via AJAX (Admin scope).
     */
    public function storeDocument(Request $request, Inmate $inmate)
    {
        $this->authorizeAccess($inmate);
        $data = $request->validate([
            'document_name' => 'required|string|max:255',
            'doc_file' => 'required|file|max:8192',
        ]);
        $file = $request->file('doc_file');
        $dir = \App\Support\StoragePath::inmateDocDir($inmate->id);
        $name = \App\Support\StoragePath::uniqueName($file);
        $path = \Storage::putFileAs($dir, $file, $name);

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

    /**
     * Replace an existing extra document file and archive the previous file.
     */
    public function replaceDocument(Request $request, Inmate $inmate, InmateDocument $document)
    {
        $this->authorizeAccess($inmate);
        abort_unless($document->inmate_id === $inmate->id, 404);

        $request->validate([
            'doc_file' => 'required|file|max:8192',
        ]);

        if (!empty($document->file_path)) {
            try {
                InmateDocumentArchive::create([
                    'inmate_id' => $inmate->id,
                    'source_type' => 'extra',
                    'source_key' => null,
                    'inmate_document_id' => $document->id,
                    'document_name' => $document->document_name,
                    'file_path' => $document->file_path,
                    'archived_by' => auth()->id(),
                    'archived_at' => now(),
                ]);
            } catch (\Throwable $e) {
                // best-effort archival
            }
        }

        $file = $request->file('doc_file');
        $dir = \App\Support\StoragePath::inmateDocDir($inmate->id);
        $name = \App\Support\StoragePath::uniqueName($file);
        $path = \Storage::putFileAs($dir, $file, $name);
        $document->file_path = $path;
        $document->save();

        $disk = \Storage::disk(config('filesystems.default'));
        try {
            $url = config('filesystems.default') === 's3'
                ? $disk->temporaryUrl($document->file_path, now()->addMinutes(5))
                : $disk->url($document->file_path);
        } catch (\Throwable $e) {
            $url = null;
        }

        return response()->json([
            'ok' => true,
            'document' => [
                'id' => $document->id,
                'name' => $document->document_name,
                'url' => $url,
                'path' => $document->file_path,
            ]
        ]);
    }

    /**
     * Toggle guardian share flag for an inmate document (Admin scope, institution-guarded).
     */
    public function toggleDocumentShare(Request $request, Inmate $inmate, InmateDocument $document)
    {
        $this->authorizeAccess($inmate);
        abort_unless($document->inmate_id === $inmate->id, 404);
        $document->is_sharable_with_guardian = !$document->is_sharable_with_guardian;
        $document->save();
        return $request->wantsJson()
            ? response()->json(['ok'=>true,'shared'=>$document->is_sharable_with_guardian])
            : back()->with('success', 'Share setting updated.');
    }

    public function update(Request $request, Inmate $inmate)
    {
        $this->authorizeAccess($inmate);
        $data = $request->validate([
            'type' => 'nullable|string|max:50',
            'admission_number' => ['required','string','max:32','regex:/^(ADM\d{10}|\d{1,20})$/','unique:inmates,admission_number,'.$inmate->id],
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'admission_date' => 'required|date',
            'notes' => 'nullable|string',
            'intake_history' => 'nullable|string',
            'guardian_relation' => 'nullable|string|max:100',
            'guardian_first_name' => 'nullable|string|max:255',
            'guardian_last_name' => 'nullable|string|max:255',
            'guardian_email' => 'nullable|email|max:255',
            'guardian_phone' => 'nullable|string|max:50',
            'guardian_address' => 'nullable|string',
            'aadhaar_number' => 'nullable|string|max:100',
            'photo' => 'nullable|file|mimes:jpg,jpeg,png,webp,heic,heif|max:8192',
            'aadhaar_card' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,heic,heif|max:10240',
            'ration_card' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,heic,heif|max:10240',
            'panchayath_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,heic,heif|max:10240',
            'disability_card' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,heic,heif|max:10240',
            'doctor_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,heic,heif|max:10240',
            'vincent_depaul_card' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,heic,heif|max:10240',
            'doc_names.*' => 'nullable|string|max:255',
            'doc_files.*' => 'nullable|file|max:10240',
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
                    try {
                        InmateDocumentArchive::create([
                            'inmate_id' => $inmate->id,
                            'source_type' => 'core',
                            'source_key' => $input,
                            'document_name' => str_replace('_', ' ', ucfirst($input)),
                            'file_path' => $inmate->{$column},
                            'archived_by' => auth()->id(),
                            'archived_at' => now(),
                        ]);
                    } catch (\Throwable $e) {
                        // best-effort archival
                    }
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
        if ($request->wantsJson()) {
            return response()->json(['ok'=>true,'message'=>'Inmate updated successfully.']);
        }
        return redirect()->route('admin.inmates.index')->with('success','Inmate updated successfully.');
    }

    public function destroy(Inmate $inmate)
    {
        $this->authorizeAccess($inmate);
        $inmate->delete();
        return redirect()->route('admin.inmates.index')->with('success','Inmate deleted successfully.');
    }

    protected function authorizeAccess(Inmate $inmate): void
    {
        abort_unless($inmate->institution_id === Auth::user()->institution_id, 403);
    }
}
