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

    public function show(ContactSubmission $submission)
    {
        return view('system_admin.contact_submissions.show', compact('submission'));
    }

    public function destroy(ContactSubmission $submission)
    {
        $submission->delete();
        return redirect()->route('system_admin.contact-submissions.index')->with('success', 'Submission deleted.');
    }
}
