<?php
namespace App\Http\Controllers\Developer;
use App\Http\Controllers\Controller; use App\Models\User; use App\Models\Institution; use Illuminate\Http\Request;
class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->with('institution');
        $institutionId = $request->get('institution_id');
        $role = $request->get('role');
        $sort = $request->get('sort','name_asc');
        if ($institutionId) { $query->where('institution_id', $institutionId); }
        if ($role) { $query->where('role',$role); }
        match($sort){
            'name_desc' => $query->orderBy('name','desc'),
            'created_asc' => $query->orderBy('id','asc'),
            'created_desc' => $query->orderBy('id','desc'),
            default => $query->orderBy('name','asc'),
        };
        $users = $query->paginate(15)->appends($request->only('institution_id','role','sort'));
        $institutions = Institution::orderBy('name')->get(['id', 'name']);
        $roles = ['developer','system_admin','admin','doctor','nurse','staff'];
        return view('developer.users.index', compact('users', 'institutions', 'institutionId','roles','role','sort'));
    }

    public function create()
    {
        $institutions = Institution::orderBy('name')->get();
        $roles = ['developer', 'system_admin', 'admin', 'doctor', 'nurse', 'staff'];
        return view('developer.users.create', compact('institutions', 'roles'));
    }

    public function store(Request $request)
    {
        $allowedRoles = ['developer', 'system_admin', 'admin', 'doctor', 'nurse', 'staff'];

        // Base validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string|in:' . implode(',', $allowedRoles),
            'password' => 'required|string|min:8|confirmed',
        ];

        // Only require institution_id if NOT creating a system_admin
        if ($request->input('role') === 'system_admin') {
            $rules['institution_id'] = 'nullable';
        } else {
            $rules['institution_id'] = 'required|exists:institutions,id';
        }

        $validated = $request->validate($rules);
        $validated['password'] = bcrypt($validated['password']);
        // Ensure system_admin users have no institution association
        if ($validated['role'] === 'system_admin') {
            $validated['institution_id'] = null;
        }
        User::create($validated);
        return redirect()->route('developer.users.index')->with('success', 'User created successfully!');
    }

    public function edit(User $user)
    {
        $institutions = Institution::orderBy('name')->get();
        $roles = ['developer', 'system_admin', 'admin', 'doctor', 'nurse', 'staff'];
        return view('developer.users.edit', compact('user', 'institutions', 'roles'));
    }

    public function show(User $user)
    {
        return view('developer.users.show', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:developer,system_admin,admin,doctor,nurse,staff',
            'institution_id' => 'nullable|exists:institutions,id'
        ]);
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->institution_id = $validated['institution_id'] ?? null;
        if ($request->filled('password')) {
            $request->validate(['password' => 'required|min:8|confirmed']);
            $user->password = bcrypt($request->password);
        }
        // If user is being updated to system_admin, ensure institution cleared
        if ($user->role === 'system_admin') {
            $user->institution_id = null;
        }
        $user->save();
        return redirect()->route('developer.users.index')->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        if ($user->role === 'developer') {
            return redirect()->route('developer.users.index')->with('error', 'Cannot delete developer accounts.');
        }
        $user->delete();
        return redirect()->route('developer.users.index')->with('success', 'User deleted successfully.');
    }

    public function toggleBugReporting(User $user)
    {
        $user->can_report_bugs = !$user->can_report_bugs;
        $user->save();
        return response()->json(['ok' => true, 'can_report_bugs' => $user->can_report_bugs]);
    }

    public function promoteToSystemAdmin(User $user)
    {
        if (in_array($user->role, ['developer', 'system_admin'])) {
            return redirect()->route('developer.users.index')->with('error', 'Cannot promote this account.');
        }
        $user->role = 'system_admin';
        $user->institution_id = null; // ensure cleared
        $user->save();
        return redirect()->route('developer.users.index')->with('success', 'User ' . $user->name . ' has been promoted to System Admin.');
    }
}
