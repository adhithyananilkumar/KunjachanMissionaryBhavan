<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;

class ContactSubmissionController extends Controller
{
    public function index()
    {
        $submissions = ContactSubmission::latest()->paginate(20);
        return view('admin.contact_submissions.index', compact('submissions'));
    }

    public function show(ContactSubmission $submission)
    {
        return view('admin.contact_submissions.show', compact('submission'));
    }

    public function destroy(ContactSubmission $submission)
    {
        $submission->delete();
        return redirect()->route('admin.contact-submissions.index')->with('success', 'Submission deleted.');
    }
}
