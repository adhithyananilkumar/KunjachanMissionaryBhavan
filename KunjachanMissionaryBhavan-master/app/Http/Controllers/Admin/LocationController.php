<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    public function index()
    {
        $institutionId = Auth::user()->institution_id;
        $locations = Location::where('institution_id',$institutionId)
            ->orderBy('type')
            ->orderBy('block_id')
            ->orderBy('number')
            ->paginate(20);
        return view('admin.allocation.index', compact('locations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:room,bed,cell',
            'capacity' => 'nullable|integer|min:1',
        ]);
        $data['institution_id'] = Auth::user()->institution_id;
        $data['capacity'] = $data['capacity'] ?? 1;
        $data['status'] = 'available';
        Location::create($data);
        return back()->with('success','Location created');
    }

    public function update(Request $request, Location $location)
    {
        abort_unless($location->institution_id === Auth::user()->institution_id, 403);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:room,bed,cell',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|string|max:50',
        ]);
        $location->update($data);
        return back()->with('success','Location updated');
    }

    public function destroy(Location $location)
    {
        abort_unless($location->institution_id === Auth::user()->institution_id, 403);
        // prevent deleting if has active occupants
        $occupied = $location->assignments()->whereNull('end_date')->exists();
        if($occupied){
            return back()->with('error','Cannot delete: location has active occupants.');
        }
        $location->delete();
        return back()->with('success','Location deleted');
    }
}
