<?php
namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\Inmate;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $institutionId = $request->get('institution_id');
        $query = User::query()->where('role','doctor')->with('institution');
        if ($institutionId) { $query->where('institution_id', $institutionId); }
        $doctors = $query->orderBy('name')->paginate(20)->appends($request->only('institution_id'));
        $institutions = Institution::orderBy('name')->get(['id','name']);
        return view('developer.doctors.index', compact('doctors','institutions','institutionId'));
    }

    public function show(User $doctor)
    {
        abort_unless($doctor->role === 'doctor', 403);
        $inmates = Inmate::where('institution_id', $doctor->institution_id)->orderBy('first_name')->orderBy('last_name')->get();
        $assignedIds = $doctor->assignedInmates()->pluck('inmate_id')->toArray();
        return view('developer.doctors.show', compact('doctor','inmates','assignedIds'));
    }

    public function saveAssignments(User $doctor, Request $request)
    {
        abort_unless($doctor->role === 'doctor', 403);
        $data = $request->validate(['inmate_ids'=>'array','inmate_ids.*'=>'integer|exists:inmates,id']);
        $ids = collect($data['inmate_ids'] ?? [])->unique();
        $validIds = Inmate::where('institution_id',$doctor->institution_id)->whereIn('id',$ids)->pluck('id');
        $doctor->assignedInmates()->sync($validIds);
        return back()->with('success','Assignments updated.');
    }
}
