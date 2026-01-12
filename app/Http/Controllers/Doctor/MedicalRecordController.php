<?php
namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Inmate;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function store(Request $request, Inmate $inmate)
    {
        abort_unless($inmate->institution_id === auth()->user()->institution_id, 403);

        $data = $request->validate([
            'diagnosis' => ['required','string'],
            'prescription' => ['nullable','string'], // deprecated in favor of structured meds but kept optional
            'lab_test_id' => ['nullable','integer','exists:lab_tests,id'],
            'med_name.*' => ['nullable','string','max:255'],
            'med_dosage.*' => ['nullable','string','max:255'],
            'med_route.*' => ['nullable','string','max:50'],
            'med_frequency.*' => ['nullable','string','max:100'],
            'med_start_date.*' => ['nullable','date'],
            'med_end_date.*' => ['nullable','date','after_or_equal:med_start_date.*'],
            'med_instructions.*' => ['nullable','string'],
        ]);

        // Guard the lab test association: must belong to this inmate
        $labTestId = null;
        if(!empty($data['lab_test_id'])){
            $lt = \App\Models\LabTest::find($data['lab_test_id']);
            if($lt && (int)$lt->inmate_id === (int)$inmate->id){
                $labTestId = $lt->id;
            }
        }

        $record = MedicalRecord::create([
            'inmate_id' => $inmate->id,
            'doctor_id' => auth()->id(),
            'lab_test_id' => $labTestId,
            'diagnosis' => $data['diagnosis'],
            'prescription' => $data['prescription'] ?? null,
        ]);

        // Create structured medications
        if($request->has('med_name')){
            foreach($request->med_name as $idx=>$name){
                if(!$name) continue;
                \App\Models\Medication::create([
                    'inmate_id' => $inmate->id,
                    'medical_record_id' => $record->id,
                    'name' => $name,
                    'dosage' => $request->med_dosage[$idx] ?? null,
                    'route' => $request->med_route[$idx] ?? null,
                    'frequency' => $request->med_frequency[$idx] ?? null,
                    'start_date' => $request->med_start_date[$idx] ?? null,
                    'end_date' => $request->med_end_date[$idx] ?? null,
                    'instructions' => $request->med_instructions[$idx] ?? null,
                ]);
            }
        }

        if($request->wantsJson() || $request->ajax()){
            return response()->json([
                'ok' => true,
                'message' => 'Medical record added.',
            ]);
        }
        return redirect()->route('doctor.inmates.show', $inmate)->with('status','Medical record added.');
    }
}
