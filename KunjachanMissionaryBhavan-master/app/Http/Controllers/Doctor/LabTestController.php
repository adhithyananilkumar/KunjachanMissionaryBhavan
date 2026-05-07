<?php
namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Inmate;
use App\Models\LabTest;
use App\Models\User;
use App\Notifications\LabTestOrdered;
use Illuminate\Http\Request;
use App\Events\LabTestOrderedEvent;

class LabTestController extends Controller
{
    public function index()
    {
        $doctor = auth()->user();
        // Show only tests ordered by this doctor (across inmates in their institution)
        $tests = \App\Models\LabTest::with(['inmate'])
            ->where('ordered_by', $doctor->id)
            ->whereHas('inmate', function($q) use ($doctor){
                $q->where('institution_id', $doctor->institution_id);
            })
            ->orderByDesc('ordered_date')
            ->paginate(20);
        return view('doctor.lab-tests.index', compact('tests'));
    }
    public function create(Inmate $inmate)
    {
        $doctor = auth()->user();
        abort_unless($inmate->institution_id === $doctor->institution_id, 403);
        // Creating lab orders requires assignment when feature is enabled
        if(optional($doctor->institution)->doctor_assignment_enabled){
            abort_unless(
                $inmate->doctor_id === $doctor->id || \DB::table('doctor_inmate')->where('inmate_id',$inmate->id)->where('doctor_id',$doctor->id)->exists(),
                403
            );
        }
        return view('doctor.lab-tests.create', compact('inmate'));
    }

    public function store(Request $request, Inmate $inmate)
    {
    $doctor = auth()->user();
    abort_unless($inmate->institution_id === $doctor->institution_id, 403);
        if(optional($doctor->institution)->doctor_assignment_enabled){
            abort_unless(
                $inmate->doctor_id === $doctor->id || \DB::table('doctor_inmate')->where('inmate_id',$inmate->id)->where('doctor_id',$doctor->id)->exists(),
                403
            );
        }
        $data = $request->validate([
            'test_name' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);
        $data['inmate_id'] = $inmate->id;
        $data['ordered_by'] = auth()->id();
        $data['ordered_date'] = now();
        $labTest = LabTest::create($data);
        // Notify nurses & staff in same institution
        $institutionId = auth()->user()->institution_id;
        $recipients = User::where('institution_id',$institutionId)
            ->whereIn('role',["nurse","staff"]) // adjust if different role naming
            ->get();
    foreach($recipients as $user){ $user->notify(new LabTestOrdered($labTest)); }
    // Per-user broadcast handled by the Notification via('broadcast') channel
        return redirect()->route('doctor.inmates.show', $inmate)->with('status','Lab test ordered.');
    }

    public function edit(LabTest $labTest)
    {
    $doctor = auth()->user();
    abort_unless($labTest->inmate && $labTest->inmate->institution_id === $doctor->institution_id, 403);
    // Unify experience: doctors don't upload or edit report files here.
    // Always send them to the details page where they can Accept/Reject.
    return redirect()->route('doctor.lab-tests.show', $labTest);
    }

    public function show(LabTest $labTest)
    {
    $doctor = auth()->user();
    abort_unless($labTest->inmate && $labTest->inmate->institution_id === $doctor->institution_id, 403);
        return view('doctor.lab-tests.show', compact('labTest'));
    }

    public function update(Request $request, LabTest $labTest)
    {
    $doctor = auth()->user();
    abort_unless($labTest->inmate && $labTest->inmate->institution_id === $doctor->institution_id, 403);
    if(optional($doctor->institution)->doctor_assignment_enabled){ abort_unless($labTest->inmate->doctor_id === $doctor->id, 403); }
        // If already reviewed, block further edits
        if($labTest->reviewed_at){
            return redirect()->back()->with('status','Lab test is locked after acceptance.');
        }
        $data = $request->validate([
            'status' => 'sometimes|in:ordered,in_progress,completed,cancelled',
            'result_notes' => 'nullable|string',
            'result_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'doctor_review' => 'nullable|string|max:2000', // optional review notes for accept/reject
            'accept' => 'nullable|boolean',
            'reject' => 'nullable|boolean',
        ]);
        if($request->hasFile('result_file')){
            $file = $request->file('result_file');
            $dir = \App\Support\StoragePath::labReportDir($labTest->id);
            $name = \App\Support\StoragePath::uniqueName($file);
            $data['result_file_path'] = \Storage::putFileAs($dir, $file, $name);
        }
        if($data['status']==='completed' && !$labTest->completed_date){
            $data['completed_date'] = now();
        }
        $data['updated_by'] = auth()->id();
        $accepting = (bool)($data['accept'] ?? false);
        $rejecting = (bool)($data['reject'] ?? false);
        unset($data['accept'], $data['reject']);
        $labTest->update($data);
        if($rejecting){
            // Send back for revision: unlock, set status to in_progress, keep completed_date as historical
            $labTest->forceFill([
                'reviewed_at' => null,
                'reviewed_by' => null,
                'status' => 'in_progress',
            ])->save();
            // Notify nurses and staff in institution with reason (doctor_review)
            $institutionId = $doctor->institution_id;
            $recipients = \App\Models\User::where('institution_id',$institutionId)->whereIn('role',["nurse","staff"])->get();
            foreach($recipients as $user){ $user->notify(new \App\Notifications\LabResultRejected($labTest, trim((string)($request->input('doctor_review') ?? '')))); }
            return redirect()->route('doctor.lab-tests.show', $labTest)->with('success','Lab report rejected and sent back for updates.');
        }
        // If explicitly accepting and test is completed, mark reviewed and write a MedicalRecord entry with doctor's review (if provided)
        if($accepting && $labTest->fresh()->status === 'completed' && !$labTest->reviewed_at){
            $labTest->forceFill([
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id(),
            ])->save();
            // Create a concise medical record entry capturing the doctor's review of the result
            $reviewText = trim((string)($request->input('doctor_review') ?? ''));
            if($reviewText !== ''){
                \App\Models\MedicalRecord::create([
                    'inmate_id' => $labTest->inmate_id,
                    'doctor_id' => $doctor->id,
                    'diagnosis' => $reviewText,
                    'prescription' => null,
                ]);
            }
        }
    return redirect()->route('doctor.inmates.show', $labTest->inmate_id)->with('success',$accepting ? 'Lab test accepted and recorded.' : 'Lab test updated.');
    }
}
