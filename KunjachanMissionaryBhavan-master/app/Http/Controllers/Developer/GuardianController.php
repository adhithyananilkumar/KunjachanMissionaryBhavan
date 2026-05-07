<?php
namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class GuardianController extends Controller
{
    public function index(Request $request)
    {
        $query = Guardian::query();
        $search = $request->get('search');
        $sort = $request->get('sort','name_asc');
        if($search){ $query->where('full_name','like','%'.trim($search).'%'); }
        match($sort){
            'name_desc' => $query->orderBy('full_name','desc'),
            'created_desc' => $query->orderBy('id','desc'),
            'created_asc' => $query->orderBy('id','asc'),
            default => $query->orderBy('full_name','asc'),
        };
        $guardians = $query->paginate(20)->appends($request->only('search','sort'));
        return view('developer.guardians.index', compact('guardians','search','sort'));
    }

    public function create()
    {
        return view('developer.guardians.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required','string','max:255'],
            'phone_number' => ['nullable','string','max:50'],
            'address' => ['nullable','string','max:255'],
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

        return redirect()->route('developer.guardians.index')->with('status','Guardian and user account created.');
    }

    public function edit(Guardian $guardian)
    {
        return view('developer.guardians.edit', compact('guardian'));
    }

    public function update(Request $request, Guardian $guardian)
    {
        $currentUserId = optional($guardian->user)->id;
        $data = $request->validate([
            'full_name' => ['required','string','max:255'],
            'phone_number' => ['nullable','string','max:50'],
            'address' => ['nullable','string','max:255'],
            'email' => ['nullable','email','max:255', Rule::unique('users','email')->ignore($currentUserId)],
            'password' => ['nullable','confirmed', Password::defaults()],
        ]);

        try {
            DB::transaction(function() use ($guardian,$data) {
                $guardian->update([
                    'full_name' => $data['full_name'],
                    'phone_number' => $data['phone_number'] ?? null,
                    'address' => $data['address'] ?? null,
                ]);

                $user = $guardian->user;
                if($user){
                    if(!empty($data['email'])){
                        $user->email = $data['email'];
                    }
                    if(!empty($data['password'])){
                        $user->password = $data['password'];
                    }
                    $user->save();
                } else {
                    if(!empty($data['email']) && !empty($data['password'])){
                        User::create([
                            'name' => $guardian->full_name,
                            'email' => $data['email'],
                            'password' => $data['password'],
                            'role' => 'guardian',
                            'guardian_id' => $guardian->id,
                        ]);
                    }
                }
            });
        } catch(\Exception $e) {
            return back()->withInput()->with('error','Failed to update guardian: '.$e->getMessage());
        }

        return redirect()->route('developer.guardians.index')->with('status','Guardian (and user if provided) updated.');
    }

    public function destroy(Guardian $guardian)
    {
        $guardian->delete();
        return redirect()->route('developer.guardians.index')->with('status','Guardian deleted.');
    }
}
