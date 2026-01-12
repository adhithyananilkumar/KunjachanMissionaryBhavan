<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Models\User;
use App\Notifications\NewContactSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ContactController extends Controller
{
    public function index()
    {
        return view('public.contact');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        $submission = ContactSubmission::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            // 'phone' => $request->input('phone'), // Add input to form if needed/desired
            'message' => $validated['message'],
            'ip_address' => $request->ip(),
        ]);

        // Notify System Admins
        $admins = User::where('role', 'system_admin')->get();
        Notification::send($admins, new NewContactSubmission($submission));

        return back()->with('success', 'Thanks for reaching out! We will get back to you shortly.');
    }
}
