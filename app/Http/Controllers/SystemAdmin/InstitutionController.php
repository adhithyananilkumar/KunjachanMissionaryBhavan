<?php
namespace App\Http\Controllers\SystemAdmin;
use App\Http\Controllers\Controller; use App\Models\Institution; use Illuminate\Http\Request; use App\Models\User; use Illuminate\Support\Facades\DB;
class InstitutionController extends Controller
{
    public function index(Request $request){
        $query = Institution::query()->withCount(['users','inmates']);
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
        return view('system_admin.institutions.index', compact('institutions','search','sort'));
    }
    public function show(Institution $institution){
        // lightweight counts for header chips
        $institution->loadCount(['users','inmates']);
        if (request()->ajax()) {
            return view('system_admin.institutions.tabs.overview', compact('institution'));
        }
        return view('system_admin.institutions.show', compact('institution'));
    }
    public function create(){ return view('system_admin.institutions.create'); }
    public function store(Request $request){ $validated=$request->validate(['name'=>'required|string|max:255','address'=>'required|string|max:255','phone'=>'nullable|string|max:20','email'=>'nullable|email|max:255',]); Institution::create($validated); return redirect()->route('system_admin.institutions.index')->with('success','Institution created successfully.'); }
    public function edit(Institution $institution){ return view('system_admin.institutions.edit', compact('institution')); }
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
        if ($request->wantsJson()) {
            return response()->json(['ok'=>true,'message'=>'Institution updated successfully.']);
        }
        return redirect()->route('system_admin.institutions.index')->with('success','Institution updated successfully.');
    }
    public function destroy(Request $request, Institution $institution){ $cascade = $request->boolean('delete_users'); try { DB::transaction(function() use ($institution,$cascade){ if($cascade){ User::where('institution_id',$institution->id)->where('role','!=','developer')->delete(); } else { User::where('institution_id',$institution->id)->update(['institution_id'=>null]); } $institution->delete(); }); return redirect()->route('system_admin.institutions.index')->with('success','Institution deleted successfully'.($cascade?' with users':'').'.'); } catch(\Throwable $e){ return redirect()->route('system_admin.institutions.index')->with('error','Unable to delete institution: '.$e->getMessage()); } }

    // AJAX tabs
    public function tabUsers(Request $request, Institution $institution){
        $q = trim((string)$request->get('q'));
        $users = User::where('institution_id',$institution->id)
            ->when($q, fn($qry)=>$qry->where(function($qq) use($q){ $qq->where('name','like','%'.$q.'%')->orWhere('email','like','%'.$q.'%'); }))
            ->orderBy('name')
            ->paginate(10);
        return view('system_admin.institutions.tabs.users', compact('institution','users','q'));
    }
    public function tabInmates(Request $request, Institution $institution){
        $q = trim((string)$request->get('q'));
        $inmates = \App\Models\Inmate::where('institution_id',$institution->id)
            ->when($q, fn($qry)=>$qry->where(function($qq) use($q){ $qq->where('first_name','like','%'.$q.'%')->orWhere('last_name','like','%'.$q.'%'); }))
            ->orderBy('last_name')
            ->paginate(10);
        return view('system_admin.institutions.tabs.inmates', compact('institution','inmates','q'));
    }
    public function tabSettings(Institution $institution){
        return view('system_admin.institutions.tabs.settings', compact('institution'));
    }
}
