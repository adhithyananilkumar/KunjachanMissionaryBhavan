<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\MedicineInventory;
use App\Models\Medication;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    private function slotsFromFrequency(?string $freq): array
    {
        $freq = trim((string)$freq);
        if($freq==='') return [];
        // Normalize separators: allow "1-0-1", "1 0 1", "1/0/1"
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
        $institutionId = auth()->user()->institution_id;
        $q = trim((string)$request->get('q'));
        $medicines = Medicine::query()
            ->when($q, fn($qq)=>$qq->where('name','like',"%{$q}%"))
            ->orderBy('name')
            ->get();
        $inventories = MedicineInventory::with('medicine')
            ->where('institution_id',$institutionId)
            ->get()
            ->keyBy('medicine_id');
        // Overview metrics
        $totalAvailable = MedicineInventory::where('institution_id',$institutionId)->where('quantity','>',0)->count();
        $lowStockCount = MedicineInventory::where('institution_id',$institutionId)->whereColumn('quantity','<=','threshold')->count();
        $activePrescriptions = Medication::whereHas('inmate', function($q) use ($institutionId){ $q->where('institution_id',$institutionId); })
            ->where('status','active')->count();
        $recentlyAdded = MedicineInventory::with('medicine')
            ->where('institution_id',$institutionId)
            ->orderByDesc('created_at')->limit(5)->get();
        // Aggregate recent prescriptions by name (last 30 days)
        $recent = Medication::query()
            ->where('status','active')
            ->whereHas('inmate', function($q) use ($institutionId){ $q->where('institution_id', $institutionId); })
            ->where('created_at','>=', now()->subDays(30))
            ->selectRaw('name, COUNT(*) as cnt')
            ->groupBy('name')
            ->orderByDesc('cnt')
            ->limit(10)
            ->get()
            ->map(function($row) use ($medicines, $inventories){
                $catalog = $medicines->firstWhere('name', $row->name);
                $inv = $catalog ? ($inventories[$catalog->id] ?? null) : null;
                return [
                    'name' => $row->name,
                    'count' => (int)$row->cnt,
                    'catalog' => $catalog,
                    'inventory' => $inv,
                ];
            });
        return view('admin.medicines.index', compact('medicines','inventories','q','recent','totalAvailable','lowStockCount','activePrescriptions','recentlyAdded'));
    }

    // Live medications for this institution with due state
    public function live(Request $request)
    {
        $institutionId = auth()->user()->institution_id;
        // Evaluate windows in the app timezone to avoid UTC skew (e.g., IST mornings appearing as "waiting").
        $tz = config('app.timezone', 'UTC');
        $now = now($tz);
        $today = $now->toDateString();
        $windows = config('medication.windows');
        $meds = Medication::with(['inmate:id,first_name,last_name,institution_id'])
            ->whereHas('inmate', fn($q)=>$q->where('institution_id',$institutionId))
            ->where('status','active')
            ->where(function($q) use ($today){ $q->whereNull('start_date')->orWhereDate('start_date','<=',$today); })
            ->where(function($q) use ($today){ $q->whereNull('end_date')->orWhereDate('end_date','>=',$today); })
            ->orderBy('name')
            ->limit(500)
            ->get();
        $rows = [];
    foreach($meds as $m){
            $slots = $this->slotsFromFrequency($m->frequency);
            $takenToday = \App\Models\MedicationLog::where('medication_id',$m->id)
                ->whereDate('administration_time',$today)->where('status','taken')->exists();
            $loggedToday = \App\Models\MedicationLog::where('medication_id',$m->id)
                ->whereDate('administration_time',$today)->whereIn('status',["taken","missed"]) 
                ->exists();
            $dueNow=false; $pastAll=true; $any=false; $windowNames=[]; $currentStart=null; $currentEnd=null;
            foreach($slots as $slot){
                if(!isset($windows[$slot])) continue; $any=true; $windowNames[]=$slot; [$s,$e] = $windows[$slot];
                $start = \Carbon\Carbon::parse($today.' '.$s, $tz); $end = \Carbon\Carbon::parse($today.' '.$e, $tz);
                if($now->between($start,$end)){ $dueNow=true; $pastAll=false; $currentStart=$start; $currentEnd=$end; }
                if($now->lt($start)){ $pastAll=false; }
            }
            if(!$any){
                $windowNames=['morning'];
                [$s,$e]=$windows['morning']; $start=\Carbon\Carbon::parse($today.' '.$s, $tz); $end=\Carbon\Carbon::parse($today.' '.$e, $tz);
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
                'patient_id'=>$m->inmate->id,
                'patient'=>trim($m->inmate->first_name.' '.$m->inmate->last_name),
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

    // Attendance log for this institution (today or range)
    public function logs(Request $request)
    {
        $institutionId = auth()->user()->institution_id;
        $days = (int)($request->get('days', 7));
        $start = now()->subDays(max(1,$days));
        $logs = \App\Models\MedicationLog::with(['medication.inmate','nurse'])
            ->whereHas('medication.inmate', fn($q)=>$q->where('institution_id',$institutionId))
            ->where('administration_time','>=',$start)
            ->orderByDesc('administration_time')
            ->limit(500)
            ->get()
            ->map(function($l){
                return [
                    'time' => $l->administration_time?->toDateTimeString(),
                    'patient' => trim($l->medication?->inmate?->first_name.' '.($l->medication?->inmate?->last_name ?? '')),
                    'medicine' => $l->medication?->name,
                    'status' => $l->status,
                    'by' => $l->nurse?->name,
                ];
            });
        return response()->json($logs);
    }

    // Admin logs a medication administration (used by intake UI)
    public function logMedication(Request $request)
    {
        $data = $request->validate([
            'medication_id' => 'required|exists:medications,id',
            'status' => 'required|in:taken,missed',
        ]);
        $today = now()->toDateString();
        $med = \App\Models\Medication::with(['inmate','medicalRecord'])->findOrFail($data['medication_id']);
        abort_unless($med->inmate && $med->inmate->institution_id === auth()->user()->institution_id, 403);
        // Ensure medical record exists
        $recordId = $med->medical_record_id;
        if(!$recordId){
            $doctorId = $med->inmate->doctor_id ?? auth()->id();
            $rec = \App\Models\MedicalRecord::create([
                'inmate_id' => $med->inmate_id,
                'doctor_id' => $doctorId,
                'diagnosis' => 'Medication administration',
                'prescription' => null,
            ]);
            $recordId = $rec->id; $med->update(['medical_record_id'=>$recordId]);
        }
        // Prevent duplicate taken for same day
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

    public function store(Request $request)
    {
        $institutionId = auth()->user()->institution_id;
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'form' => 'nullable|string|max:100',
            'strength' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:25',
            'quantity' => 'required|integer|min:0',
            'threshold' => 'nullable|integer|min:0',
        ]);
        $medicine = Medicine::firstOrCreate(
            ['name' => $data['name']],
            ['form'=>$data['form'] ?? null,'strength'=>$data['strength'] ?? null,'unit'=>$data['unit'] ?? null]
        );
        $inv = MedicineInventory::updateOrCreate(
            ['institution_id'=>$institutionId,'medicine_id'=>$medicine->id],
            ['quantity'=>$data['quantity'],'threshold'=>$data['threshold'] ?? 0,'updated_by'=>auth()->id()]
        );
        return $request->wantsJson()
            ? response()->json(['ok'=>true,'inventory'=>$inv->load('medicine')])
            : back()->with('success','Medicine saved.');
    }

    public function update(Request $request, MedicineInventory $inventory)
    {
        abort_unless($inventory->institution_id === auth()->user()->institution_id, 403);
        $data = $request->validate([
            'quantity' => 'required|integer|min:0',
            'threshold' => 'nullable|integer|min:0',
        ]);
        $inventory->update($data + ['updated_by'=>auth()->id()]);
        return $request->wantsJson()
            ? response()->json(['ok'=>true,'inventory'=>$inventory->fresh('medicine')])
            : back()->with('success','Inventory updated.');
    }

    public function destroy(MedicineInventory $inventory)
    {
        abort_unless($inventory->institution_id === auth()->user()->institution_id, 403);
        $inventory->delete();
        return back()->with('success','Removed from inventory.');
    }

    // AJAX tabs (if needed later; current page renders server-side initially)
    public function tabInventory(Request $request)
    {
        $institutionId = auth()->user()->institution_id;
        $inventories = MedicineInventory::with('medicine')->where('institution_id',$institutionId)->get();
        return view('admin.medicines.partials.inventory', compact('inventories'));
    }

    public function tabCatalog(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $medicines = Medicine::query()->when($q, fn($qq)=>$qq->where('name','like',"%{$q}%"))->orderBy('name')->get();
        $inventories = MedicineInventory::where('institution_id', auth()->user()->institution_id)->get()->keyBy('medicine_id');
        return view('admin.medicines.partials.catalog', compact('medicines','inventories','q'));
    }

    public function usage(Request $request)
    {
        $institutionId = auth()->user()->institution_id;
        $q = trim((string)$request->get('q'));
        $days = (int)($request->get('days', 30));
        $start = now()->subDays(max(1,$days));
        $query = \App\Models\Medication::query()
            ->where('created_at','>=',$start)
            ->whereHas('inmate', fn($qq)=>$qq->where('institution_id',$institutionId))
            ->with(['inmate:id,first_name,last_name,institution_id','medicalRecord:id,doctor_id','medicalRecord.doctor:id,name'])
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
        $resp = $items->map(function($m){
            return [
                'id' => $m->id,
                'name' => $m->name,
                'inmate' => [
                    'id' => $m->inmate->id,
                    'name' => trim($m->inmate->first_name.' '.$m->inmate->last_name),
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

    // Prescriptions for names not present in catalog
    public function uncatalogued(Request $request)
    {
        $institutionId = auth()->user()->institution_id;
        $days = (int)($request->get('days', 90));
        $start = now()->subDays(max(1,$days));
        $catalogNames = Medicine::pluck('name')->map(fn($n)=>mb_strtolower($n))->all();
        $items = \App\Models\Medication::query()
            ->where('created_at','>=',$start)
            ->whereHas('inmate', fn($q)=>$q->where('institution_id',$institutionId))
            ->selectRaw('name, COUNT(*) as cnt')
            ->groupBy('name')
            ->orderByDesc('cnt')
            ->get()
            ->filter(fn($row)=>!in_array(mb_strtolower($row->name), $catalogNames));
        return response()->json($items->values());
    }

    // All inmates currently assigned a given medicine name (by string match)
    public function assignees(Request $request)
    {
        $institutionId = auth()->user()->institution_id;
        $name = (string)$request->get('name');
        abort_if($name==='',$this->httpResponseCode ?? 400, 'name required');
        $rows = \App\Models\Medication::with(['inmate:id,first_name,last_name','medicalRecord.doctor:id,name'])
            ->whereHas('inmate', fn($q)=>$q->where('institution_id',$institutionId))
            ->where('name',$name)
            ->orderByDesc('created_at')
            ->limit(200)
            ->get()
            ->map(fn($m)=>[
                'inmate' => trim($m->inmate->first_name.' '.$m->inmate->last_name),
                'doctor' => $m->medicalRecord?->doctor?->name,
                'status' => $m->status,
                'start_date' => $m->start_date,
                'end_date' => $m->end_date,
            ]);
        return response()->json($rows);
    }

    // Low stock list for this institution
    public function lowStock(Request $request)
    {
        $institutionId = auth()->user()->institution_id;
        $items = MedicineInventory::with('medicine')
            ->where('institution_id',$institutionId)
            ->whereColumn('quantity','<=','threshold')
            ->orderBy('quantity')
            ->get()
            ->map(fn($i)=>[
                'id' => $i->id,
                'name' => $i->medicine?->name,
                'form' => $i->medicine?->form,
                'strength' => $i->medicine?->strength,
                'unit' => $i->medicine?->unit,
                'quantity' => $i->quantity,
                'threshold' => $i->threshold,
            ]);
        return response()->json($items);
    }

    // Mixed history: prescriptions and inventory changes (approximate)
    public function history(Request $request)
    {
        $institutionId = auth()->user()->institution_id;
        $days = (int)($request->get('days', 30));
        $start = now()->subDays(max(1,$days));

        $prescriptions = \App\Models\Medication::whereHas('inmate', fn($q)=>$q->where('institution_id',$institutionId))
            ->where('created_at','>=',$start)
            ->orderByDesc('created_at')
            ->limit(200)
            ->get()
            ->map(fn($m)=>[
                'type' => 'prescribed',
                'name' => $m->name,
                'by' => $m->medicalRecord?->doctor?->name,
                'at' => $m->created_at->toDateTimeString(),
                'meta' => [
                    'inmate' => trim($m->inmate->first_name.' '.$m->inmate->last_name),
                    'status' => $m->status,
                ],
            ]);

        $invChanges = MedicineInventory::with('medicine')
            ->where('institution_id',$institutionId)
            ->where('updated_at','>=',$start)
            ->orderByDesc('updated_at')
            ->limit(200)
            ->get()
            ->map(fn($i)=>[
                'type' => 'inventory',
                'name' => $i->medicine?->name,
                'by' => null,
                'at' => $i->updated_at->toDateTimeString(),
                'meta' => [
                    'quantity' => $i->quantity,
                    'threshold' => $i->threshold,
                ],
            ]);

        $all = $prescriptions->merge($invChanges)->sortByDesc('at')->values();
        return response()->json($all);
    }

    // Notify (stub): pretend to notify system admin about low stock
    public function notify(Request $request, MedicineInventory $inventory)
    {
        abort_unless($inventory->institution_id === auth()->user()->institution_id, 403);
        \Log::info('Low stock notification', ['inventory_id'=>$inventory->id,'institution_id'=>$inventory->institution_id,'user_id'=>auth()->id()]);
        return response()->json(['ok'=>true]);
    }

    // Reports as CSV downloads
    public function reportStock(Request $request)
    {
        $institutionId = auth()->user()->institution_id;
        $rows = MedicineInventory::with('medicine')
            ->where('institution_id',$institutionId)->get();
        $csv = \fopen('php://temp','r+');
        \fputcsv($csv, ['Medicine','Form','Strength','Unit','Quantity','Threshold']);
        foreach($rows as $r){
            \fputcsv($csv, [$r->medicine?->name,$r->medicine?->form,$r->medicine?->strength,$r->medicine?->unit,$r->quantity,$r->threshold]);
        }
        rewind($csv);
        $content = stream_get_contents($csv);
        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="stock.csv"'
        ]);
    }

    public function reportPrescriptions(Request $request)
    {
        $institutionId = auth()->user()->institution_id;
        $days = (int)($request->get('days', 30));
        $start = now()->subDays(max(1,$days));
        $rows = \App\Models\Medication::with(['inmate','medicalRecord.doctor'])
            ->whereHas('inmate', fn($q)=>$q->where('institution_id',$institutionId))
            ->where('created_at','>=',$start)
            ->orderByDesc('created_at')->get();
        $csv = \fopen('php://temp','r+');
        \fputcsv($csv, ['Medicine','Inmate','Doctor','Status','Start','End','Created At']);
        foreach($rows as $m){
            \fputcsv($csv, [
                $m->name,
                trim($m->inmate->first_name.' '.($m->inmate->last_name ?? '')),
                $m->medicalRecord?->doctor?->name,
                $m->status,
                $m->start_date,
                $m->end_date,
                $m->created_at,
            ]);
        }
        rewind($csv);
        $content = stream_get_contents($csv);
        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="prescriptions.csv"'
        ]);
    }

    public function reportUsageTrends(Request $request)
    {
        $institutionId = auth()->user()->institution_id;
        $days = (int)($request->get('days', 30));
        $start = now()->subDays(max(1,$days));
        $rows = \DB::table('medications as m')
            ->join('inmates as i','i.id','=','m.inmate_id')
            ->where('i.institution_id',$institutionId)
            ->where('m.created_at','>=',$start)
            ->selectRaw('date(m.created_at) as d, m.name as name, count(*) as cnt')
            ->groupBy('d','name')
            ->orderBy('d')
            ->get();
        $csv = \fopen('php://temp','r+');
        \fputcsv($csv, ['Date','Medicine','Count']);
        foreach($rows as $r){ \fputcsv($csv, [$r->d, $r->name, $r->cnt]); }
        rewind($csv);
        $content = stream_get_contents($csv);
        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="usage-trends.csv"'
        ]);
    }
}
