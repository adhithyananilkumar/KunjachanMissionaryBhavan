<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        // Update the password (let cast handle hashing or hash explicitly)
        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Invalidate other sessions and rotate remember token
        try {
            Auth::logoutOtherDevices($validated['password']);
        } catch (\Throwable $e) {
            // ignore but continue with local session rotation
        }

        // If using database sessions, purge other sessions for this user
        if (config('session.driver') === 'database') {
            try {
                DB::table(config('session.table', 'sessions'))
                    ->where('user_id', $request->user()->getAuthIdentifier())
                    ->where('id', '!=', $request->session()->getId())
                    ->delete();
            } catch (\Throwable $e) { /* no-op */ }
        }

        // Regenerate current session ID
        $request->session()->migrate(true);

        return back()->with('status', 'password-updated');
    }
}
