<?php

namespace App\Http\Controllers;

use App\Models\GuardianMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuardianMessageController extends Controller
{
    public function send(Request $request)
    {
        $user = Auth::user();
        $guardian = $user?->guardian;

        abort_unless($guardian, 403);

        $validated = $request->validate([
            'message_text' => 'required|string|max:1000',
        ]);

        GuardianMessage::create([
            'guardian_id' => $guardian->id,
            'message_text' => $validated['message_text'],
            'sent_by_guardian' => true,
        ]);

        return back()->with('success', 'Message sent.');
    }
}
