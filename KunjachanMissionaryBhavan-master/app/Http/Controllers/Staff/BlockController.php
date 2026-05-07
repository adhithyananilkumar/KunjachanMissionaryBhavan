<?php
namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\Location;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    // API: list locations for the staff user's institution (read-only)
    public function apiLocationsForInstitution(Request $request)
    {
        $user = auth()->user();
        $institution = $user->institution; if(!$institution){ return response()->json(['locations'=>[]]); }
        $showOccupied = (bool)$request->query('show_occupied', false);
        $locations = Location::with([
                'assignments' => function($q){ $q->whereNull('end_date')->with('inmate'); },
                'block'
            ])
            ->where('institution_id', $institution->id)
            ->orderBy('type')->orderBy('number')
            ->get();

        $payload = $locations->map(function(Location $loc){
            $active = $loc->assignments->first();
            $occupied = $active !== null || ($loc->computed_status === 'occupied');
            $occupantId = $active?->inmate_id;
            $occupantName = $active && $active->inmate ? $active->inmate->full_name : null;
            return [
                'id' => $loc->id,
                'name' => $loc->name,
                'status' => $loc->status,
                'occupied' => (bool)$occupied,
                'occupant_id' => $occupantId,
                'occupant' => $occupantName ?: ($occupantId ? ('Inmate #'.$occupantId) : null),
                'type' => $loc->type,
                'number' => $loc->number,
            ];
        });
        if(!$showOccupied){ $payload = $payload->where('occupied', false)->values(); }
        return response()->json(['locations'=>$payload->values()]);
    }
}
