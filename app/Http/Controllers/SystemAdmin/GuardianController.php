<?php
namespace App\Http\Controllers\SystemAdmin; use App\Http\Controllers\Controller; use App\Models\Guardian; use App\Models\User; use Illuminate\Http\Request; use Illuminate\Support\Facades\DB; use Illuminate\Validation\Rules\Password; use Illuminate\Validation\Rule;
class GuardianController extends Controller
{
    public function index(Request $request){
        $query = Guardian::query()->with(['inmate.institution']);
        $search = trim((string)$request->get('search'));
        $sort = $request->get('sort','name_asc');
        $institutionId = $request->get('institution_id');
        if($search){
            $query->where(function($q) use ($search){
                $q->where('full_name','like','%'.$search.'%')
                  ->orWhere('phone_number','like','%'.$search.'%');
            });
        }
        if($institutionId){
            $query->whereHas('inmate', fn($q)=>$q->where('institution_id',$institutionId));
        }
        match($sort){
            'name_desc' => $query->orderBy('full_name','desc'),
            'created_desc' => $query->orderBy('id','desc'),
            'created_asc' => $query->orderBy('id','asc'),
            default => $query->orderBy('full_name','asc'),
        };
        $guardians = $query->paginate(20)->appends($request->only('search','sort','institution_id'));
        $institutions = \App\Models\Institution::orderBy('name')->get(['id','name']);
        return view('system_admin.guardians.index', compact('guardians','search','sort','institutions','institutionId'));
    }
    public function create(){
        $inmates = \App\Models\Inmate::with('institution:id,name')
            ->orderBy('first_name')->orderBy('last_name')
            ->get(['id','first_name','last_name','institution_id']);
        return view('system_admin.guardians.create', compact('inmates'));
    }
    public function store(Request $request){
        $data = $request->validate([
            'full_name' => ['required','string','max:255'],
            'phone_number' => ['nullable','string','max:50'],
            'address' => ['nullable','string','max:255'],
            'inmate_id' => ['nullable','exists:inmates,id'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','confirmed', Password::defaults()],
        ]);
        try {
            DB::transaction(function() use ($data) {
                $guardian = Guardian::create([
                    'full_name' => $data['full_name'],
                    'phone_number' => $data['phone_number'] ?? null,
                    'address' => $data['address'] ?? null,
                ]);

                if(!empty($data['inmate_id'])){
                    \App\Models\Inmate::where('id',$data['inmate_id'])->update(['guardian_id'=>$guardian->id]);
                }

                User::create([
                    'name' => $guardian->full_name,
                    'email' => $data['email'],
                    'password' => $data['password'],
                    'role' => 'guardian',
                    'guardian_id' => $guardian->id,
                ]);
            });
        } catch(\Exception $e) {
            return back()->withInput()->with('error','Failed to create guardian: '.$e->getMessage());
        }
        return redirect()->route('system_admin.guardians.index')->with('success','Guardian and user account created.');
    }
    public function edit(Guardian $guardian){
        $inmates = \App\Models\Inmate::with('institution:id,name')
            ->orderBy('first_name')->orderBy('last_name')
            ->get(['id','first_name','last_name','institution_id']);
        return view('system_admin.guardians.edit', ['guardian'=>$guardian,'standalone'=>true,'inmates'=>$inmates]);
    }
    public function show(Guardian $guardian){
        $guardian->load(['inmate.institution','user']);
        return view('system_admin.guardians.show', [
            'guardian' => $guardian,
            'inmate' => $guardian->inmate,
            'user' => $guardian->user,
        ]);
    }
    public function update(Request $request, Guardian $guardian){ $currentUserId = optional($guardian->user)->id; $data = $request->validate(['full_name' => ['required','string','max:255'],'phone_number' => ['nullable','string','max:50'],'address' => ['nullable','string','max:255'],'inmate_id' => ['nullable','exists:inmates,id'],'email' => ['nullable','email','max:255', Rule::unique('users','email')->ignore($currentUserId)],'password' => ['nullable','confirmed', Password::defaults()],]); try { DB::transaction(function() use ($guardian,$data) { $guardian->update(['full_name' => $data['full_name'],'phone_number' => $data['phone_number'] ?? null,'address' => $data['address'] ?? null,]); if(!empty($data['inmate_id'])){ \App\Models\Inmate::where('id',$data['inmate_id'])->update(['guardian_id'=>$guardian->id]); } $user = $guardian->user; if($user){ if(!empty($data['email'])){ $user->email = $data['email']; } if(!empty($data['password'])){ $user->password = $data['password']; } $user->save(); } else { if(!empty($data['email']) && !empty($data['password'])){ User::create(['name' => $guardian->full_name,'email' => $data['email'],'password' => $data['password'],'role' => 'guardian','guardian_id' => $guardian->id,]); } } }); } catch(\Exception $e) { if($request->wantsJson()){ return response()->json(['ok'=>false,'message'=>$e->getMessage()],422); } return back()->withInput()->with('error','Failed to update guardian: '.$e->getMessage()); } if($request->wantsJson()){ $guardian->load('user'); return response()->json(['ok'=>true,'guardian'=>$guardian]); } return redirect()->route('system_admin.guardians.show',$guardian)->with('success','Guardian (and user if provided) updated.'); }
    public function destroy(Guardian $guardian){ $guardian->delete(); return redirect()->route('system_admin.guardians.index')->with('success','Guardian deleted.'); }
}
