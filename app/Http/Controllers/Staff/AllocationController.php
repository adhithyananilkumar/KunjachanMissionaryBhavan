<?php
namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Inmate;
use App\Models\Location;
use App\Models\LocationAssignment;
use Illuminate\Http\Request;

class AllocationController extends Controller
{
    public function edit(Inmate $inmate)
    {
        abort_unless($inmate->institution_id === auth()->user()->institution_id, 403);
        // List available locations at staff's institution (exclude occupied unless toggled)
        $locations = Location::where('institution_id', $inmate->institution_id)
            ->orderBy('type')->orderBy('number')->get();
        return view('staff.allocation.edit', compact('inmate','locations'));
    }

    public function update(Request $request, Inmate $inmate)
    {
        abort_unless($inmate->institution_id === auth()->user()->institution_id, 403);
        $data = $request->validate(['location_id' => 'nullable|exists:locations,id']);
        if(!empty($data['location_id'])){
            $location = Location::where('id',$data['location_id'])->where('institution_id',$inmate->institution_id)->firstOrFail();
            // Close current assignment
            $current = LocationAssignment::where('inmate_id',$inmate->id)->whereNull('end_date')->first();
            if($current){ $current->end_date = now(); $current->save(); }
            // Create new assignment
            LocationAssignment::create([
                'inmate_id' => $inmate->id,
                'location_id' => $location->id,
                'start_date' => now(),
                'end_date' => null,
            ]);
        } else {
            // Clear assignment: set end_date now
            $current = LocationAssignment::where('inmate_id',$inmate->id)->whereNull('end_date')->first();
            if($current){ $current->end_date = now(); $current->save(); }
        }
        return back()->with('status','Allocation updated.');
    }
}
