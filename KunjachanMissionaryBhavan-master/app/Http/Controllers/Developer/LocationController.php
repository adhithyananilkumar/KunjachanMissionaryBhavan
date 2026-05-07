<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\Location;
use App\Models\Block;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Request $request)
    {
    $institutionId = $request->get('institution_id');
    $blockId = $request->get('block_id');
    $type = $request->get('type');
    $query = Location::query()->with(['institution','block']);
    if ($institutionId) { $query->where('institution_id', $institutionId); }
    if ($blockId) { $query->where('block_id', $blockId); }
    if ($type) { $query->where('type', $type); }
        $locations = $query->orderBy('type')
            ->orderBy('block_id')
            ->orderBy('number')
            ->paginate(20)
            ->appends($request->only('institution_id'));
    $institutions = Institution::orderBy('name')->get(['id','name']);
    $blocks = $institutionId
            ? Block::where('institution_id', $institutionId)->orderBy('name')->get(['id','name','prefix'])
            : collect();
    return view('developer.allocation.index', compact('locations','institutions','institutionId','blocks','blockId','type'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'block_id' => 'required|exists:blocks,id',
            'type' => 'required|in:room,bed,cell',
            'number' => 'required|string|max:50',
            'capacity' => 'nullable|integer|min:1',
        ]);
        $block = Block::findOrFail($data['block_id']);
        Location::create([
            'institution_id' => $block->institution_id,
            'block_id' => $block->id,
            'type' => $data['type'],
            'number' => $data['number'],
            'capacity' => $data['capacity'] ?? 1,
            'status' => 'available',
        ]);
        return back()->with('success','Location created');
    }

    public function update(Request $request, Location $location)
    {
        $data = $request->validate([
            'status' => 'required|string|max:50',
            'capacity' => 'nullable|integer|min:1',
        ]);
        $location->update([
            'status' => $data['status'],
            'capacity' => $data['capacity'] ?? $location->capacity,
        ]);
        return back()->with('success','Location updated');
    }

    public function destroy(Location $location)
    {
        $location->delete();
        return back()->with('success','Location deleted');
    }
}
