<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class SettingsController extends Controller
{
    public function bugAccess()
    {
        $users = User::orderBy('name')->paginate(25);
        return view('developer.settings.bug-access', compact('users'));
    }

    public function updateBugAccess(Request $request, User $user)
    {
        $request->validate([
            'bug_report_enabled' => 'required|boolean'
        ]);
        $user->update(['bug_report_enabled' => $request->bug_report_enabled]);
        return back()->with('status','Updated');
    }
}
