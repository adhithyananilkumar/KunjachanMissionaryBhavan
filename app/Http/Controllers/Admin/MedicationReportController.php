<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedicationLog;

class MedicationReportController extends Controller
{
    public function index()
    {
        $institutionId = auth()->user()->institution_id;
        $logs = MedicationLog::with(['medicalRecord.inmate','nurse'])
            ->whereHas('medicalRecord.inmate', fn($q)=>$q->where('institution_id',$institutionId))
            ->latest('administration_time')
            ->paginate(50);
        $summary = [
            'taken' => MedicationLog::whereHas('medicalRecord.inmate', fn($q)=>$q->where('institution_id',$institutionId))->where('status','taken')->count(),
            'missed' => MedicationLog::whereHas('medicalRecord.inmate', fn($q)=>$q->where('institution_id',$institutionId))->where('status','missed')->count(),
        ];
        return view('admin.medications.report', compact('logs','summary'));
    }
}
