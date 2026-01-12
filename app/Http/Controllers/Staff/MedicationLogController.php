<?php
namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\MedicationLog;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;

class MedicationLogController extends Controller
{
    public function store(Request $request, MedicalRecord $medicalRecord)
    {
        $staff = auth()->user();
        abort_unless($medicalRecord->inmate && $medicalRecord->inmate->institution_id === $staff->institution_id, 403);
        MedicationLog::create([
            'medical_record_id' => $medicalRecord->id,
            'nurse_id' => $staff->id, // reuse nurse_id column to track staff administration
            'administration_time' => now(),
        ]);
        return redirect()->route('staff.dashboard')->with('status','Medication administration logged.');
    }
}
