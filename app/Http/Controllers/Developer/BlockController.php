<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Institution;
use App\Models\Location;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function index(Request $request)
    {
        $institutionIds = array_filter((array) $request->get('institution_ids', []));
        $query = Block::query()->with('institution');
        if (!empty($institutionIds)) { $query->whereIn('institution_id', $institutionIds); }
        $blocks = $query->orderBy('name')->paginate(20)->appends(['institution_ids'=>$institutionIds]);
        $institutions = Institution::orderBy('name')->get(['id','name']);
        return view('developer.allocation.blocks', compact('blocks','institutions','institutionIds'));
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
        return view('developer.allocation.block_locations', compact('block','locations'));
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
}
