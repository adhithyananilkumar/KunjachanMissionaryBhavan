<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;

class ContactSubmissionController extends Controller
{
    public function index()
    {
        $submissions = ContactSubmission::latest()->paginate(20);
        return view('system_admin.contact_submissions.index', compact('submissions'));
    }

    public function show(ContactSubmission $contact_submission)
    {
        return view('system_admin.contact_submissions.show', ['submission' => $contact_submission]);
    }

    public function destroy(ContactSubmission $contact_submission)
    {
        $contact_submission->delete();
        if (request()->ajax()) {
            return response()->json(['ok' => true, 'id' => $contact_submission->id, 'message' => 'Submission deleted.']);
        }
        return redirect()->route('system_admin.contact-submissions.index')->with('success', 'Submission deleted.');
    }
}
