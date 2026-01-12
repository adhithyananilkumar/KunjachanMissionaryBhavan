<?php
namespace App\Http\Controllers\Nurse;

use App\Http\Controllers\Controller;
use App\Models\LabTest;
use App\Notifications\LabResultUploaded;
use Illuminate\Http\Request;

class LabTestController extends Controller
{
    public function index()
    {
        $tests = LabTest::with('inmate')
            ->whereHas('inmate', fn($q)=>$q->where('institution_id', auth()->user()->institution_id))
            ->orderByDesc('ordered_date')
            ->paginate(20);
        return view('nurse.lab-tests.index', compact('tests'));
    }
    public function show(LabTest $labTest)
    {
        abort_unless($labTest->inmate && $labTest->inmate->institution_id === auth()->user()->institution_id, 403);
        return view('nurse.lab-tests.show', compact('labTest'));
    }

    public function partialUpdate(Request $request, LabTest $labTest)
    {
        abort_unless($labTest->inmate && $labTest->inmate->institution_id === auth()->user()->institution_id, 403);
        if($labTest->reviewed_at){
            return redirect()->route('nurse.lab-tests.show',$labTest)->with('status','This test has been accepted by the doctor and is locked.');
        }
        $data = $request->validate([
            'result_notes' => 'nullable|string',
            'result_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);
        $completedNow = false;
        if($request->hasFile('result_file')){
            $file = $request->file('result_file');
            $dir = \App\Support\StoragePath::labReportDir($labTest->id);
            $name = \App\Support\StoragePath::uniqueName($file);
            $data['result_file_path'] = \Storage::putFileAs($dir, $file, $name);
        }
        $hasNotes = isset($data['result_notes']) && trim((string)$data['result_notes']) !== '';
        $hasFile = isset($data['result_file_path']);
        if(($hasNotes || $hasFile) && $labTest->status !== 'completed'){
            $data['status'] = 'completed';
            if(!$labTest->completed_date){ $data['completed_date'] = now(); }
            $completedNow = true;
        }
        $data['updated_by'] = auth()->id();
        $labTest->update($data);
        // If auto-completed, notify ordering doctor
        if($completedNow){
            $labTest->refresh();
            if($labTest->orderedBy){
                $labTest->orderedBy->notify(new LabResultUploaded($labTest));
            }
        }
        return redirect()->route('nurse.lab-tests.show', $labTest)->with('status','Lab test updated.');
    }
}
