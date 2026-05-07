<?php
namespace App\Http\Controllers\Developer;
use App\Http\Controllers\Controller; use App\Models\Institution; use Illuminate\Http\Request;
class InstitutionController extends Controller
{
    public function index(Request $request){
        $query = Institution::query();
        $search = $request->get('search');
        $sort = $request->get('sort','name_asc');
        if($search){ $query->where('name','like','%'.trim($search).'%'); }
        match($sort){
            'name_desc' => $query->orderBy('name','desc'),
            'created_desc' => $query->orderBy('id','desc'),
            'created_asc' => $query->orderBy('id','asc'),
            default => $query->orderBy('name','asc'),
        };
        $institutions = $query->paginate(20)->appends($request->only('search','sort'));
        return view('developer.institutions.index', compact('institutions','search','sort'));
    }
    public function create(){ return view('developer.institutions.create'); }
    public function store(Request $request){ $validated=$request->validate(['name'=>'required|string|max:255','address'=>'required|string|max:255','phone'=>'nullable|string|max:20','email'=>'nullable|email|max:255',]); Institution::create($validated); return redirect()->route('developer.institutions.index')->with('status','Institution created successfully.'); }
    public function edit(Institution $institution){ return view('developer.institutions.edit', compact('institution')); }
    public function update(Request $request, Institution $institution){
        $validated=$request->validate([
            'name'=>'required|string|max:255',
            'address'=>'required|string|max:255',
            'phone'=>'nullable|string|max:20',
            'email'=>'nullable|email|max:255',
            'features'=>'nullable|array',
            'features.*'=>'string',
            'doctor_assignment_enabled' => 'nullable|boolean',
        ]);
        $validated['enabled_features']=$validated['features'] ?? [];
        unset($validated['features']);
        $validated['doctor_assignment_enabled'] = (bool)($validated['doctor_assignment_enabled'] ?? false);
        $institution->update($validated);
        return redirect()->route('developer.institutions.index')->with('status','Institution updated successfully.');
    }
    public function destroy(Request $request, Institution $institution){
        $cascade = $request->boolean('delete_users');
        try {
            \DB::transaction(function() use ($institution,$cascade){
                if($cascade){
                    // delete users linked to this institution (excluding developers as safety)
                    \App\Models\User::where('institution_id',$institution->id)->where('role','!=','developer')->delete();
                } else {
                    // detach users from institution to allow deletion constraint
                    \App\Models\User::where('institution_id',$institution->id)->update(['institution_id'=>null]);
                }
                $institution->delete();
            });
            return redirect()->route('developer.institutions.index')->with('status','Institution deleted successfully'.($cascade?' with users':'').'.');
        } catch(\Throwable $e){
            return redirect()->route('developer.institutions.index')->with('error','Unable to delete institution: '.$e->getMessage());
        }
    }
}
