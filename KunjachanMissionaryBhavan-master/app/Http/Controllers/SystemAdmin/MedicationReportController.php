<?php
namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Models\MedicationLog;

class MedicationReportController extends Controller
{
    public function index()
    {
        $logs = MedicationLog::with(['medicalRecord.inmate.institution','nurse'])->latest('administration_time')->paginate(50);
        $summary = [
            'taken' => MedicationLog::where('status','taken')->count(),
            'missed' => MedicationLog::where('status','missed')->count(),
        ];
        return view('system_admin.medications.report', compact('logs','summary'));
    }
}
