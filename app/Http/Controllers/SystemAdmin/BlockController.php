<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Institution;
use App\Models\Location;
use App\Models\LocationAssignment;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function index(Request $request)
    {
        $institutionIds = array_filter((array) $request->get('institution_ids', []));
        $query = Block::query()->with('institution');
        if (!empty($institutionIds)) { $query->whereIn('institution_id',$institutionIds); }
        $blocks = $query->orderBy('name')->paginate(20)->appends(['institution_ids'=>$institutionIds]);
        $institutions = Institution::orderBy('name')->get(['id','name']);
        return view('system_admin.allocation.blocks', compact('blocks','institutions','institutionIds'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'name' => 'required|string|max:255',
            'prefix' => 'nullable|string|max:50',
        ]);
        Block::create($data);
        return back()->with('success','Block created');
    }

    public function update(Request $request, Block $block)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'prefix' => 'nullable|string|max:50',
        ]);
        $block->update($data);
        return back()->with('success','Block updated');
    }

    public function destroy(Block $block)
    {
        $block->delete();
        return back()->with('success','Block deleted');
    }

    public function locations(Block $block)
    {
        $locations = Location::where('block_id',$block->id)->orderBy('type')->orderBy('number')->paginate(30);
        return view('system_admin.allocation.block_locations', compact('block','locations'));
    }

    public function storeLocation(Request $request, Block $block)
    {
        $data = $request->validate([
            'type' => 'required|in:room,bed,cell',
            'number' => 'required|string|max:50',
        ]);
        Location::create([
            'institution_id' => $block->institution_id,
            'block_id' => $block->id,
            'type' => $data['type'],
            'number' => $data['number'],
            'capacity' => 1,
            'status' => 'available',
        ]);
        return back()->with('success','Location created');
    }

    public function updateLocation(Request $request, Location $location)
    {
        $data = $request->validate([
            'status' => 'required|in:available,maintenance',
        ]);
        $hasActive = $location->assignments()->whereNull('end_date')->exists();
        // If trying to mark available but there are occupants, block
        if($data['status']==='available' && $hasActive){
            return back()->with('error','Cannot mark available while occupants are present.');
        }
        // If trying to mark maintenance while occupied, require shifting first
        if($data['status']==='maintenance' && $hasActive){
            return back()->with('error','Shift occupants before marking Maintenance.');
        }
        $location->update(['status'=>$data['status']]);
        return back()->with('success','Location updated');
    }

    public function destroyLocation(Location $location)
    {
        if($location->assignments()->whereNull('end_date')->exists()){
            return back()->with('error','Cannot delete: location has active occupants.');
        }
        $location->delete();
        return back()->with('success','Location deleted');
    }

    // API: list locations for an institution with occupancy and occupant label
    public function apiLocationsByInstitution(Institution $institution, Request $request)
    {
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

    // API: list inmates at an institution (for Allocate modal)
    public function apiInmatesByInstitution(Institution $institution, Request $request)
    {
        $term = trim((string)$request->query('term',''));
        $query = \App\Models\Inmate::where('institution_id', $institution->id);
        if($term !== ''){
            $query->where(function($q) use($term){
                $q->where('first_name','like',"%$term%")
                  ->orWhere('last_name','like',"%$term%")
                  ->orWhere('registration_number','like',"%$term%");
            });
        }
        $inmates = $query->orderBy('first_name')->limit(20)->get(['id','first_name','last_name']);
        return response()->json([
            'inmates' => $inmates->map(fn($i)=>[
                'id'=>$i->id,
                'name'=>trim($i->first_name.' '.($i->last_name??''))
            ])
        ]);
    }
}
