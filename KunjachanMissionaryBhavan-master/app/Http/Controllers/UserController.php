<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = User::query();
        if (auth()->user()->role !== 'developer') {
            $query->where('institution_id', auth()->user()->institution_id);
        }
        $users = $query->paginate(15);
        return view('users.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $institutions = [];
        if ($user?->role === 'developer') {
            $institutions = Institution::orderBy('name')->get();
        }
    // Roles presented in the create view: admin, doctor, nurse, staff
    $roles = ['admin','doctor','nurse','staff'];
    return view('users.create', compact('institutions','roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $allowedRoles = ['admin','doctor','nurse','staff'];
        if ($user?->role === 'developer') {
            $allowedRoles[] = 'developer';
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string|in:' . implode(',', $allowedRoles),
            'password' => 'required|string|min:8|confirmed',
        ];
        if ($user?->role === 'developer') {
            $rules['institution_id'] = 'required|exists:institutions,id';
        }

        $validated = $request->validate($rules);

        if (Auth::user()->role === 'developer') {
            // developer sets institution from form (already validated)
            $validated['institution_id'] = $validated['institution_id'];
        } else {
            $validated['institution_id'] = Auth::user()->institution_id; // scope to creator's institution
        }

        $validated['password'] = bcrypt($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')->with('success','User created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $current = Auth::user();
        $institutions = [];
        if ($current?->role === 'developer') {
            $institutions = Institution::orderBy('name')->get();
        }
        $roles = ['admin','doctor','nurse','staff'];
        return view('users.edit', compact('user','institutions','roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Base validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:admin,doctor,nurse,staff,developer',
        ];

        // Add institution validation only for the developer performing update
        if (auth()->user()->role === 'developer') {
            $rules['institution_id'] = 'nullable|exists:institutions,id';
        }

        $validated = $request->validate($rules);

        // Update main fields
        $user->name  = $validated['name'];
        $user->email = $validated['email'];
        $user->role  = $validated['role'];

        // Update institution only if developer updating
        if (auth()->user()->role === 'developer') {
            $user->institution_id = $validated['institution_id'] ?? null;
        }

        // Optional password update
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|min:8|confirmed',
            ]);
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
