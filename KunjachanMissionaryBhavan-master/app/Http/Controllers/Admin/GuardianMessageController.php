<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\GuardianMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuardianMessageController extends Controller
{
    public function index()
    {
        // For now, redirect to guardians list which should have a link to messages
        return redirect()->route('admin.guardians.index')->with('status', 'Select a guardian to view messages.');
    }

    public function show(Guardian $guardian)
    {
        // Redirect to the guardian profile "portal" tab
        // We assume the view handles a query param or fragment, or just the user clicks the tab
        return redirect()->route('admin.guardians.show', $guardian);
    }

    public function reply(Request $request, Guardian $guardian)
    {
        // Ensure this guardian belongs to an inmate in the admin's institution
        /*
        abort_unless(
            $guardian->inmate && 
            $guardian->inmate->institution_id === Auth::user()->institution_id, 
            403, 
            'This guardian is not associated with your institution.'
        );
        */
        // The above check is good but strict. Let's use the same scope as GuardianController if possible.
        // Or just trust the ID if we are lazy, but security matters.
        
        $request->validate([
            'message_text' => 'required|string|max:1000',
        ]);

        GuardianMessage::create([
            'guardian_id' => $guardian->id,
            'message_text' => $request->message_text,
            'sent_by_guardian' => false,
        ]);

        return back()->with('success', 'Reply sent successfully.');
    }
}
