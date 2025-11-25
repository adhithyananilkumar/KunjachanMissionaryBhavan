<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlockController extends Controller
{
    // List blocks for admin institution
    public function index()
    {
        $blocks = Block::where('institution_id', Auth::user()->institution_id)->orderBy('name')->paginate(20);
        return view('admin.allocation.blocks', compact('blocks'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'prefix' => 'nullable|string|max:50',
        ]);
        $data['institution_id'] = Auth::user()->institution_id;
        Block::create($data);
        return back()->with('success','Block created');
    }

    public function update(Request $request, Block $block)
    {
        abort_unless($block->institution_id === Auth::user()->institution_id, 403);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'prefix' => 'nullable|string|max:50',
        ]);
        $block->update($data);
        return back()->with('success','Block updated');
    }

    public function destroy(Block $block)
    {
        abort_unless($block->institution_id === Auth::user()->institution_id, 403);
        $block->delete();
        return back()->with('success','Block deleted');
    }

    // Manage locations within a block
    public function locations(Block $block)
    {
        abort_unless($block->institution_id === Auth::user()->institution_id, 403);
        $locations = Location::where('block_id',$block->id)->orderBy('type')->orderBy('number')->paginate(30);
        return view('admin.allocation.block_locations', compact('block','locations'));
    }

    public function storeLocation(Request $request, Block $block)
    {
        abort_unless($block->institution_id === Auth::user()->institution_id, 403);
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

    // Update an individual location status (available/maintenance) with safety checks
    public function updateLocation(Request $request, Location $location)
    {
        abort_unless($location->institution_id === Auth::user()->institution_id, 403);
        $data = $request->validate([
            'status' => 'required|in:available,maintenance',
        ]);
        $hasActive = $location->assignments()->whereNull('end_date')->exists();
        if($data['status'] === 'available' && $hasActive){
            return back()->with('error','Cannot mark available while occupants are present.');
        }
        if($data['status'] === 'maintenance' && $hasActive){
            return back()->with('error','Shift occupants before marking Maintenance.');
        }
        $location->update(['status' => $data['status']]);
        return back()->with('success','Location updated');
    }

    // Delete a location if no active occupants
    public function destroyLocation(Location $location)
    {
        abort_unless($location->institution_id === Auth::user()->institution_id, 403);
        if($location->assignments()->whereNull('end_date')->exists()){
            return back()->with('error','Cannot delete: location has active occupants.');
        }
        $location->delete();
        return back()->with('success','Location deleted');
    }

    // API: list locations (admin scoped) for Assign/Transfer lookups
    public function apiLocationsByInstitution(\App\Models\Institution $institution, Request $request)
    {
        abort_unless($institution->id === \Auth::user()->institution_id, 403);
        $showOccupied = (bool)$request->query('show_occupied', false);
        $locations = \App\Models\Location::with([
                'assignments' => function($q){ $q->whereNull('end_date')->with('inmate'); },
                'block'
            ])
            ->where('institution_id', $institution->id)
            ->orderBy('type')->orderBy('number')
            ->get();

        $payload = $locations->map(function(\App\Models\Location $loc){
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

    // API: list inmates (admin scoped) for Allocate modal
    public function apiInmatesByInstitution(\App\Models\Institution $institution, Request $request)
    {
        abort_unless($institution->id === \Auth::user()->institution_id, 403);
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
