<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function index()
    {
        $institutions = Institution::where('status', 'active')
            ->with('donationSetting')
            ->get();
        
        return view('public.donate', compact('institutions'));
    }
<<<<<<< HEAD

    public function store(Request $request)
    {
        $validated = $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'donor_name' => 'required|string|max:255',
            'donor_email' => 'nullable|email|max:255',
            'donor_phone' => 'required|string|max:20',
            'amount' => 'required|numeric|min:1',
            'details' => 'nullable|array', // For meal type (breakfast, lunch, etc)
        ]);

        $donationRequest = \App\Models\DonationRequest::create([
            'institution_id' => $validated['institution_id'],
            'donor_name' => $validated['donor_name'],
            'donor_email' => $validated['donor_email'],
            'donor_phone' => $validated['donor_phone'],
            'amount' => $validated['amount'],
            'status' => 'pending',
            'details' => $request->input('details'),
            'message' => $request->input('message'),
        ]);

        // Notify System Admins
        $admins = \App\Models\User::where('role', 'system_admin')->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\NewDonationRequest($donationRequest));

        return back()->with('success', 'Thank you for your generosity! We will contact you shortly to coordinate your donation.');
    }
=======
>>>>>>> 3e03daa29128f97355c96e657850f19885d91155
}
