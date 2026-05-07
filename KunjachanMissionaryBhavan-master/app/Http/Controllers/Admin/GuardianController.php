<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\Inmate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class GuardianController extends Controller
{
    protected function scopedGuardianQuery()
    {
        $institutionId = Auth::user()->institution_id;
        return Guardian::whereHas('inmate', function($q) use ($institutionId){
            $q->where('institution_id', $institutionId);
        });
    }

    public function index()
    {
    $guardians = $this->scopedGuardianQuery()->orderBy('full_name')->paginate(20); // guardian still has full_name column
        return view('admin.guardians.index', compact('guardians'));
    }

    public function create()
    {
    $inmates = Inmate::where('institution_id', Auth::user()->institution_id)->orderBy('first_name')->orderBy('last_name')->get();
        return view('admin.guardians.create', compact('inmates'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required','string','max:255'],
            'phone_number' => ['nullable','string','max:50'],
            'address' => ['nullable','string','max:255'],
            'inmate_id' => ['required','exists:inmates,id'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','confirmed', Password::defaults()],
        ]);
        abort_unless(Inmate::where('id',$data['inmate_id'])->where('institution_id', Auth::user()->institution_id)->exists(), 403);

        try {
            DB::transaction(function() use ($data) {
                $guardian = Guardian::create([
                    'full_name' => $data['full_name'],
                    'phone_number' => $data['phone_number'] ?? null,
                    'address' => $data['address'] ?? null,
                ]);

                Inmate::where('id',$data['inmate_id'])->update(['guardian_id'=>$guardian->id]);

                User::create([
                    'name' => $guardian->full_name,
                    'email' => $data['email'],
                    // hashed cast on User model
                    'password' => $data['password'],
                    'role' => 'guardian',
                    'guardian_id' => $guardian->id,
                ]);
            });
        } catch(\Exception $e) {
            return back()->withInput()->with('error','Failed to create guardian: '.$e->getMessage());
        }

        return redirect()->route('admin.guardians.index')->with('status','Guardian and user account created and linked.');
    }

    public function edit(Guardian $guardian)
    {
        abort_unless($this->scopedGuardianQuery()->where('id',$guardian->id)->exists(), 403);
    $inmates = Inmate::where('institution_id', Auth::user()->institution_id)->orderBy('first_name')->orderBy('last_name')->get();
        return view('admin.guardians.edit', compact('guardian','inmates'));
    }

    public function update(Request $request, Guardian $guardian)
    {
        abort_unless($this->scopedGuardianQuery()->where('id',$guardian->id)->exists(), 403);
        $currentUserId = optional($guardian->user)->id;
        $data = $request->validate([
            'full_name' => ['required','string','max:255'],
            'phone_number' => ['nullable','string','max:50'],
            'address' => ['nullable','string','max:255'],
            'inmate_id' => ['nullable','exists:inmates,id'],
            'email' => ['nullable','email','max:255', Rule::unique('users','email')->ignore($currentUserId)],
        ]);

        try {
            DB::transaction(function() use ($guardian,$data) {
                $guardian->update([
                    'full_name' => $data['full_name'],
                    'phone_number' => $data['phone_number'] ?? null,
                    'address' => $data['address'] ?? null,
                ]);

                if(!empty($data['inmate_id'])){
                    Inmate::where('id',$data['inmate_id'])->where('institution_id', Auth::user()->institution_id)->update(['guardian_id'=>$guardian->id]);
                }

                $user = $guardian->user;
                if($user){
                    if(!empty($data['email'])){
                        $user->email = $data['email'];
                        $user->save();
                    }
                } else {
                    if(!empty($data['email'])){
                        User::create([
                            'name' => $guardian->full_name,
                            'email' => $data['email'],
                            'password' => Hash::make(Str::random(12)),
                            'role' => 'guardian',
                            'guardian_id' => $guardian->id,
                        ]);
                    }
                }
            });
        } catch(\Exception $e) {
            return back()->withInput()->with('error','Failed to update guardian: '.$e->getMessage());
        }

        return redirect()->route('admin.guardians.index')->with('status','Guardian (and user if provided) updated.');
    }

    public function destroy(Guardian $guardian)
    {
        abort_unless($this->scopedGuardianQuery()->where('id',$guardian->id)->exists(), 403);
        // detach from inmate(s)
        Inmate::where('guardian_id',$guardian->id)->update(['guardian_id'=>null]);
        $guardian->delete();
        return redirect()->route('admin.guardians.index')->with('status','Guardian deleted.');
    }
}
