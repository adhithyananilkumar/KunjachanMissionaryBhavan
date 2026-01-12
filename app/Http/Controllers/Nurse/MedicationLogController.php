<?php
namespace App\Http\Controllers\Nurse;

use App\Http\Controllers\Controller;
use App\Models\MedicationLog;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;

class MedicationLogController extends Controller
{
    public function store(Request $request, MedicalRecord $medicalRecord)
    {
        $nurse = auth()->user();
        // Ensure nurse belongs to same institution as the inmate related to the record
        abort_unless($medicalRecord->inmate && $medicalRecord->inmate->institution_id === $nurse->institution_id, 403);

        MedicationLog::create([
            'medical_record_id' => $medicalRecord->id,
            'nurse_id' => $nurse->id,
            'administration_time' => now(),
        ]);

        return redirect()->route('nurse.dashboard')->with('status','Medication administration logged.');
    }
}
