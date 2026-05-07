<?php
namespace App\Http\Controllers\Nurse;

use App\Http\Controllers\Controller;
use App\Models\Medication;
use App\Models\MedicationLog;
use Illuminate\Http\Request;

class MedicationScheduleController extends Controller
{
    public function index()
    {
        $institutionId = auth()->user()->institution_id;
        // naive: show active meds whose date window includes today
        $today = now()->toDateString();
        $meds = Medication::with('inmate')
            ->whereHas('inmate', fn($q)=>$q->where('institution_id',$institutionId))
            ->where('status','active')
            ->where(function($q) use ($today){
                $q->whereNull('start_date')->orWhereDate('start_date','<=',$today);
            })
            ->where(function($q) use ($today){
                $q->whereNull('end_date')->orWhereDate('end_date','>=',$today);
            })
            ->orderBy('name')
            ->get();

        // Compute simple time-slot state for buttons
    $windows = config('medication.windows');
    $tz = config('app.timezone','UTC');
    $now = now($tz);
    $today = $now->toDateString();
        $states = [];
        foreach($meds as $m){
            $slots = $this->slotsFromFrequency($m->frequency);
            $takenToday = \App\Models\MedicationLog::where('medication_id',$m->id)
                ->whereDate('administration_time',$today)
                ->where('status','taken')->exists();
            $dueNow = false; $canMissNow = false; $anySlot = false; $pastAll = true; $currentStart=null; $currentEnd=null;
            foreach($slots as $slot){
                if(!isset($windows[$slot])) continue; $anySlot = true;
                [$s,$e] = $windows[$slot];
                $start = \Carbon\Carbon::parse($today.' '.$s, $tz);
                $end = \Carbon\Carbon::parse($today.' '.$e, $tz);
                if($now->between($start,$end)) { $pastAll = false; $currentStart=$start; $currentEnd=$end; }
                if($now->lt($start)) { $pastAll = false; }
            }
            if(!$anySlot){ // default to morning if unspecified
                [$s,$e] = $windows['morning'];
                $start = \Carbon\Carbon::parse($today.' '.$s, $tz); $end = \Carbon\Carbon::parse($today.' '.$e, $tz);
                if($now->between($start,$end)){ $currentStart=$start; $currentEnd=$end; $pastAll=false; }
                else { $pastAll = $now->gt($end); }
            }
            if($currentStart && $currentEnd){
                $takenInWindow = \App\Models\MedicationLog::where('medication_id',$m->id)
                    ->where('status','taken')
                    ->whereBetween('administration_time', [$currentStart, $currentEnd])->exists();
                $dueNow = !$takenInWindow; // only due if not already taken in this window
            } else {
                $dueNow = false;
            }
            $canMissNow = $pastAll && !$takenToday;
            $states[$m->id] = ['taken'=>$takenToday,'dueNow'=>$dueNow,'canMissNow'=>$canMissNow];
        }

        return view('nurse.medications.schedule', compact('meds','states'));
    }

    public function log(Request $request)
    {
        $data = $request->validate([
            'medication_id' => 'required|exists:medications,id',
            'status' => 'required|in:taken,missed',
        ]);

        $med = Medication::with('inmate','medicalRecord')->findOrFail($data['medication_id']);
        abort_unless($med->inmate && $med->inmate->institution_id === auth()->user()->institution_id, 403);
        $recordId = $med->medical_record_id;
        if(!$recordId){
            // Create a lightweight record to attach logs to
            $doctorId = $med->inmate->doctor_id ?? auth()->id();
            $rec = \App\Models\MedicalRecord::create([
                'inmate_id' => $med->inmate_id,
                'doctor_id' => $doctorId,
                'diagnosis' => 'Medication administration',
                'prescription' => null,
            ]);
            $recordId = $rec->id;
            $med->update(['medical_record_id' => $recordId]);
        }

        MedicationLog::create([
            'medical_record_id' => $recordId,
            'medication_id' => $med->id,
            'nurse_id' => auth()->id(),
            'administration_time' => now(),
            'status' => $data['status'],
        ]);
        return back()->with('success','Medication '.$data['status'].' logged.');
    }

    private function slotsFromFrequency(?string $freq): array
    {
        $freq = trim((string)$freq);
        if($freq==='') return [];
        $norm = preg_replace('/[\s\-]+/','/', $freq);
        if(preg_match('/^(\d+)\/(\d+)\/(\d+)$/', $norm, $m)){
            $map=['morning','noon','night']; $out=[]; for($i=1;$i<=3;$i++){ if(((int)$m[$i])>0) $out[]=$map[$i-1]; } return $out;
        }
        $u = strtoupper($freq);
        if(in_array($u,['OD','QD'])) return ['morning'];
        if(in_array($u,['BD','BID'])) return ['morning','night'];
        if(in_array($u,['TDS','TID'])) return ['morning','noon','night'];
        if(in_array($u,['HS'])) return ['night'];
        return [];
    }
}
