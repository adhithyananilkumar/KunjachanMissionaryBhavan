<?php
namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\MedicineInventory;
use App\Models\Institution;
use App\Models\Medication;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    private function slotsFromFrequency(?string $freq): array
    {
        $freq = trim((string)$freq);
        if($freq==='') return [];
        $norm = preg_replace('/[\s\-]+/','/', $freq);
        $norm = str_replace('\\','/', $norm);
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
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $medicines = Medicine::query()->when($q, fn($qq)=>$qq->where('name','like',"%{$q}%"))->orderBy('name')->paginate(20);
        $byInstitution = MedicineInventory::with(['medicine','institution'])->get()->groupBy('institution_id');
        $institutions = Institution::orderBy('name')->get();
    // Overview
    $totalMedicines = Medicine::count();
    $lowStockCount = MedicineInventory::whereColumn('quantity','<=','threshold')->count();
    $activePrescriptions = Medication::where('status','active')->count();
    $recentlyAdded = Medicine::orderByDesc('created_at')->limit(5)->get();
    return view('system_admin.medicines.index', compact('medicines','byInstitution','institutions','q','totalMedicines','lowStockCount','activePrescriptions','recentlyAdded'));
    }

    // Which institutions need which medicines (threshold > quantity)
    public function needs(Request $request)
    {
        $needs = MedicineInventory::with(['medicine','institution'])
            ->whereColumn('quantity','<','threshold')
            ->orderBy('institution_id')
            ->get();
        return response()->json($needs);
    }

    // Availability lookup for a medicine across institutions
    public function availability(Request $request)
    {
        $medicineId = (int)$request->get('medicine_id');
        $items = MedicineInventory::with(['institution','medicine'])
            ->when($medicineId, fn($q)=>$q->where('medicine_id',$medicineId))
            ->orderByDesc('quantity')
            ->get();
        return response()->json($items);
    }

    // Create or update a global catalog item
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'form' => 'nullable|string|max:100',
            'strength' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:25',
            'is_active' => 'nullable|boolean',
        ]);
        $medicine = Medicine::updateOrCreate(
            ['name' => $data['name']],
            [
                'form' => $data['form'] ?? null,
                'strength' => $data['strength'] ?? null,
                'unit' => $data['unit'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]
        );
        return $request->wantsJson() ? response()->json(['ok'=>true,'medicine'=>$medicine]) : back()->with('success','Medicine saved');
    }

    public function update(Request $request, Medicine $medicine)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'form' => 'nullable|string|max:100',
            'strength' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:25',
            'is_active' => 'nullable|boolean',
        ]);
        $medicine->update($data + ['is_active' => $data['is_active'] ?? $medicine->is_active]);
        return $request->wantsJson() ? response()->json(['ok'=>true,'medicine'=>$medicine]) : back()->with('success','Medicine updated');
    }

    public function deactivate(Request $request, Medicine $medicine)
    {
        $medicine->update(['is_active'=>false]);
        return $request->wantsJson() ? response()->json(['ok'=>true]) : back();
    }

    public function activate(Request $request, Medicine $medicine)
    {
        $medicine->update(['is_active'=>true]);
        return $request->wantsJson() ? response()->json(['ok'=>true]) : back();
    }

    public function destroy(Request $request, Medicine $medicine)
    {
        $medicine->delete();
        return $request->wantsJson() ? response()->json(['ok'=>true]) : back()->with('success','Medicine deleted');
    }

    // Global usage across institutions with assigned inmates and durations
    public function usage(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $days = (int)($request->get('days', 30));
        $start = now()->subDays(max(1,$days));
        $query = Medication::query()
            ->where('created_at','>=',$start)
            ->with(['inmate:id,first_name,last_name,institution_id','medicalRecord:id,doctor_id','medicalRecord.doctor:id,name','inmate.institution:id,name'])
            ->orderByDesc('created_at');
        if($q){
            $query->where(function($w) use ($q){
                $w->where('name','like',"%{$q}%")
                  ->orWhereHas('inmate', function($qq) use ($q){
                      $qq->whereRaw("concat(first_name,' ',last_name) like ?", ["%{$q}%"]);
                  });
            });
        }
        $items = $query->limit(200)->get();
        // Normalize payload for UI
        $resp = $items->map(function($m){
            return [
                'id' => $m->id,
                'name' => $m->name,
                'inmate' => [
                    'id' => $m->inmate->id,
                    'name' => trim($m->inmate->first_name.' '.$m->inmate->last_name),
                    'institution' => $m->inmate->institution?->name,
                ],
                'doctor' => $m->medicalRecord?->doctor?->name,
                'start_date' => $m->start_date,
                'end_date' => $m->end_date,
                'status' => $m->status,
                'created_at' => $m->created_at->toDateTimeString(),
            ];
        });
        return response()->json($resp);
    }

    // Names prescribed that are not present in the global catalog (recent window)
    public function uncatalogued(Request $request)
    {
        $days = (int)($request->get('days', 90));
        $start = now()->subDays(max(1,$days));
        $catalogNames = Medicine::pluck('name')->map(fn($n)=>mb_strtolower($n))->all();
        $items = Medication::query()
            ->where('created_at','>=',$start)
            ->selectRaw('name, COUNT(*) as cnt')
            ->groupBy('name')
            ->orderByDesc('cnt')
            ->get()
            ->filter(fn($row)=>!in_array(mb_strtolower($row->name), $catalogNames));
        return response()->json($items->values());
    }

    // All inmates assigned a given medicine name across institutions
    public function assignees(Request $request)
    {
        $name = (string)$request->get('name');
        abort_if($name==='',$this->httpResponseCode ?? 400, 'name required');
        $rows = Medication::with(['inmate:id,first_name,last_name,institution_id','inmate.institution:id,name','medicalRecord.doctor:id,name'])
            ->where('name',$name)
            ->orderByDesc('created_at')
            ->limit(300)
            ->get()
            ->map(fn($m)=>[
                'inmate' => trim($m->inmate->first_name.' '.$m->inmate->last_name),
                'institution' => $m->inmate->institution?->name,
                'doctor' => $m->medicalRecord?->doctor?->name,
                'status' => $m->status,
                'start_date' => $m->start_date,
                'end_date' => $m->end_date,
            ]);
        return response()->json($rows);
    }

    public function lowStock(Request $request)
    {
        $items = MedicineInventory::with(['medicine','institution'])
            ->whereColumn('quantity','<=','threshold')
            ->orderBy('institution_id')
            ->get()
            ->map(fn($i)=>[
                'institution' => $i->institution?->name,
                'name' => $i->medicine?->name,
                'quantity' => $i->quantity,
                'threshold' => $i->threshold,
            ]);
        return response()->json($items);
    }

    public function history(Request $request)
    {
        $days = (int)($request->get('days', 30));
        $start = now()->subDays(max(1,$days));
        $prescriptions = Medication::with(['inmate.institution','medicalRecord.doctor'])
            ->where('created_at','>=',$start)
            ->orderByDesc('created_at')
            ->limit(300)
            ->get()
            ->map(fn($m)=>[
                'type' => 'prescribed',
                'name' => $m->name,
                'by' => $m->medicalRecord?->doctor?->name,
                'at' => $m->created_at->toDateTimeString(),
                'meta' => [
                    'inmate' => trim($m->inmate->first_name.' '.$m->inmate->last_name),
                    'institution' => $m->inmate->institution?->name,
                    'status' => $m->status,
                ],
            ]);
        $invChanges = MedicineInventory::with(['medicine','institution'])
            ->where('updated_at','>=',$start)
            ->orderByDesc('updated_at')
            ->limit(300)
            ->get()
            ->map(fn($i)=>[
                'type' => 'inventory',
                'name' => $i->medicine?->name,
                'by' => null,
                'at' => $i->updated_at->toDateTimeString(),
                'meta' => [
                    'institution' => $i->institution?->name,
                    'quantity' => $i->quantity,
                    'threshold' => $i->threshold,
                ],
            ]);
        $all = $prescriptions->merge($invChanges)->sortByDesc('at')->values();
        return response()->json($all);
    }

    // CSV reports
    public function reportStock(Request $request)
    {
        $rows = MedicineInventory::with(['medicine','institution'])->get();
        $csv = \fopen('php://temp','r+');
        \fputcsv($csv, ['Institution','Medicine','Form','Strength','Unit','Quantity','Threshold']);
        foreach($rows as $r){
            \fputcsv($csv, [$r->institution?->name,$r->medicine?->name,$r->medicine?->form,$r->medicine?->strength,$r->medicine?->unit,$r->quantity,$r->threshold]);
        }
        rewind($csv);
        $content = stream_get_contents($csv);
        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="stock_all.csv"'
        ]);
    }

    public function reportPrescriptions(Request $request)
    {
        $days = (int)($request->get('days', 30));
        $start = now()->subDays(max(1,$days));
        $rows = Medication::with(['inmate.institution','medicalRecord.doctor'])
            ->where('created_at','>=',$start)
            ->orderByDesc('created_at')->get();
        $csv = \fopen('php://temp','r+');
        \fputcsv($csv, ['Institution','Medicine','Inmate','Doctor','Status','Start','End','Created At']);
        foreach($rows as $m){
            \fputcsv($csv, [$m->inmate->institution?->name, $m->name, trim($m->inmate->first_name.' '.$m->inmate->last_name), $m->medicalRecord?->doctor?->name, $m->status, $m->start_date, $m->end_date, $m->created_at]);
        }
        rewind($csv);
        $content = stream_get_contents($csv);
        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="prescriptions_all.csv"'
        ]);
    }

    public function reportUsageTrends(Request $request)
    {
        $days = (int)($request->get('days', 30));
        $start = now()->subDays(max(1,$days));
        $rows = \DB::table('medications as m')
            ->join('inmates as i','i.id','=','m.inmate_id')
            ->join('institutions as ins','ins.id','=','i.institution_id')
            ->where('m.created_at','>=',$start)
            ->selectRaw('date(m.created_at) as d, ins.name as institution, m.name as name, count(*) as cnt')
            ->groupBy('d','institution','name')
            ->orderBy('d')
            ->get();
        $csv = \fopen('php://temp','r+');
        \fputcsv($csv, ['Date','Institution','Medicine','Count']);
        foreach($rows as $r){ \fputcsv($csv, [$r->d, $r->institution, $r->name, $r->cnt]); }
        rewind($csv);
        $content = stream_get_contents($csv);
        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="usage-trends_all.csv"'
        ]);
    }

    // Live medications across all institutions
    public function live(Request $request)
    {
        $tz = config('app.timezone', 'UTC');
        $now = now($tz);
        $today = $now->toDateString();
        $windows = config('medication.windows');
        $meds = Medication::with(['inmate:id,first_name,last_name,institution_id','inmate.institution:id,name'])
            ->where('status','active')
            ->where(function($q) use ($today){ $q->whereNull('start_date')->orWhereDate('start_date','<=',$today); })
            ->where(function($q) use ($today){ $q->whereNull('end_date')->orWhereDate('end_date','>=',$today); })
            ->orderBy('name')
            ->limit(1000)
            ->get();
        $rows = [];
    foreach($meds as $m) {
            $slots = $this->slotsFromFrequency($m->frequency);
            $takenToday = \App\Models\MedicationLog::where('medication_id',$m->id)
                ->whereDate('administration_time',$today)->where('status','taken')->exists();
            $loggedToday = \App\Models\MedicationLog::where('medication_id',$m->id)
                ->whereDate('administration_time',$today)->whereIn('status',["taken","missed"]) 
                ->exists();
            $dueNow=false; $pastAll=true; $any=false; $windowNames=[]; $currentStart=null; $currentEnd=null;
            foreach($slots as $slot){
                if(!isset($windows[$slot])) continue;
                $any=true; $windowNames[]=$slot;
                [$s,$e]=$windows[$slot];
                $start=\Carbon\Carbon::parse($today.' '.$s, $tz);
                $end=\Carbon\Carbon::parse($today.' '.$e, $tz);
                if($now->between($start,$end)){ $dueNow=true; $pastAll=false; $currentStart=$start; $currentEnd=$end; }
                if($now->lt($start)){ $pastAll=false; }
            }
            if(!$any){
                $windowNames=['morning'];
                [$s,$e]=$windows['morning'];
                $start=\Carbon\Carbon::parse($today.' '.$s, $tz);
                $end=\Carbon\Carbon::parse($today.' '.$e, $tz);
                if($now->between($start,$end)){ $dueNow=true; $currentStart=$start; $currentEnd=$end; $pastAll=false; }
                else { $dueNow=false; $pastAll=$now->gt($end); }
            }
            if($dueNow){
                $takenInWindow = \App\Models\MedicationLog::where('medication_id',$m->id)
                    ->where('status','taken')
                    ->whereBetween('administration_time', [$currentStart, $currentEnd])->exists();
                $status = $takenInWindow ? 'taken' : 'due';
            } else {
                $status = $pastAll ? ($takenToday ? 'taken' : 'missable') : 'waiting';
            }
            $canLog = !$loggedToday && ( $status==='due' || $status==='missable' || ($status==='waiting' && $dueNow) );
            $rows[] = [
                'id'=>$m->id,
                'patient_id'=>$m->inmate_id,
                'institution'=>$m->inmate?->institution?->name,
                'patient'=>trim($m->inmate->first_name.' '.($m->inmate->last_name ?? '')),
                'name'=>$m->name,
                'dosage'=>$m->dosage,
                'route'=>$m->route,
                'frequency'=>$m->frequency,
                'windows'=>$windowNames,
                'status'=>$status,
                'takenToday'=>$takenToday,
                'loggedToday'=>$loggedToday,
                'canLog'=>$canLog,
            ];
        }
        return response()->json($rows);
    }

    public function logs(Request $request)
    {
        $days = (int)($request->get('days', 7));
        $start = now()->subDays(max(1,$days));
        $logs = \App\Models\MedicationLog::with(['medication.inmate.institution','nurse'])
            ->where('administration_time','>=',$start)
            ->orderByDesc('administration_time')
            ->limit(1000)
            ->get()
            ->map(function($l){
                return [
                    'time'=>$l->administration_time?->toDateTimeString(),
                    'institution'=>$l->medication?->inmate?->institution?->name,
                    'patient'=>trim($l->medication?->inmate?->first_name.' '.($l->medication?->inmate?->last_name ?? '')),
                    'medicine'=>$l->medication?->name,
                    'status'=>$l->status,
                    'by'=>$l->nurse?->name,
                ];
            });
        return response()->json($logs);
    }

    // System Admin logs a medication administration (used by intake UI)
    public function logMedication(Request $request)
    {
        $data = $request->validate([
            'medication_id' => 'required|exists:medications,id',
            'status' => 'required|in:taken,missed',
        ]);
        $today = now()->toDateString();
        $med = \App\Models\Medication::with(['inmate','medicalRecord'])->findOrFail($data['medication_id']);
        // System admin can log for any institution; just ensure associations exist
        $recordId = $med->medical_record_id;
        if(!$recordId){
            $doctorId = $med->inmate?->doctor_id ?? auth()->id();
            $rec = \App\Models\MedicalRecord::create([
                'inmate_id' => $med->inmate_id,
                'doctor_id' => $doctorId,
                'diagnosis' => 'Medication administration',
                'prescription' => null,
            ]);
            $recordId = $rec->id; $med->update(['medical_record_id'=>$recordId]);
        }
        $already = \App\Models\MedicationLog::where('medication_id',$med->id)->whereDate('administration_time',$today)->where('status','taken')->exists();
        if($already && $data['status']==='taken'){
            return response()->json(['ok'=>true,'duplicate'=>true]);
        }
        \App\Models\MedicationLog::create([
            'medical_record_id' => $recordId,
            'medication_id' => $med->id,
            'nurse_id' => auth()->id(),
            'administration_time' => now(),
            'status' => $data['status'],
        ]);
        return response()->json(['ok'=>true]);
    }
}
