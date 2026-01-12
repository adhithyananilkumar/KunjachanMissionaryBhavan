<?php
namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Inmate;
use App\Models\Medication;
use Illuminate\Http\Request;

class MedicationController extends Controller
{
    public function store(Request $request, Inmate $inmate)
    {
        abort_unless($inmate->institution_id === auth()->user()->institution_id, 403);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'dosage' => 'nullable|string|max:255',
            'route' => 'nullable|string|max:50',
            'frequency' => ['nullable','string','max:100', function($attr,$value,$fail){
                if($value===null || $value==='') return;
                $norm = strtoupper(trim($value));
                $norm = preg_replace('/[\s\-]+/','/', $norm);
                if(preg_match('/^(\d+)\/(\d+)\/(\d+)$/',$norm)) return; // 1/0/1
                if(in_array($norm,['OD','QD','BD','BID','TDS','TID','HS'])) return;
                $fail('Frequency must be like 1/0/1 or a standard code (OD, BD, TDS, HS).');
            }],
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'instructions' => 'nullable|string',
        ]);
        // Canonicalize frequency storage
        if(!empty($data['frequency'])){
            $norm = strtoupper(trim($data['frequency']));
            $norm = preg_replace('/[\s\-]+/','/', $norm);
            $data['frequency'] = $norm;
        }
        $data['inmate_id'] = $inmate->id;
        $data['status'] = 'active';
        $med = Medication::create($data);
        return response()->json(['ok'=>true,'medication'=>$med]);
    }

    public function update(Request $request, Medication $medication)
    {
        abort_unless($medication->inmate && $medication->inmate->institution_id === auth()->user()->institution_id, 403);
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'dosage' => 'nullable|string|max:255',
            'route' => 'nullable|string|max:50',
            'frequency' => ['nullable','string','max:100', function($attr,$value,$fail){
                if($value===null || $value==='') return;
                $norm = strtoupper(trim($value));
                $norm = preg_replace('/[\s\-]+/','/', $norm);
                if(preg_match('/^(\d+)\/(\d+)\/(\d+)$/',$norm)) return; // 1/0/1
                if(in_array($norm,['OD','QD','BD','BID','TDS','TID','HS'])) return;
                $fail('Frequency must be like 1/0/1 or a standard code (OD, BD, TDS, HS).');
            }],
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'instructions' => 'nullable|string',
            'status' => 'nullable|in:active,stopped',
        ]);
        if(isset($data['frequency'])){
            $norm = strtoupper(trim((string)$data['frequency']));
            $norm = preg_replace('/[\s\-]+/','/', $norm);
            $data['frequency'] = $norm;
        }
        $medication->update($data);
        return response()->json(['ok'=>true,'medication'=>$medication->fresh()]);
    }
}
