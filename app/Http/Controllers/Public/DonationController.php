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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'amount' => 'required|numeric|min:1',
            'donor_name' => 'required|string|max:255',
            'donor_email' => 'nullable|email|max:255',
            'donor_phone' => 'nullable|string|max:20',
            'message' => 'nullable|string',
            'meal_type' => 'nullable|string',
        ]);

        $donationRequest = \App\Models\DonationRequest::create([
            'institution_id' => $validated['institution_id'],
            'amount' => $validated['amount'],
            'donor_name' => $validated['donor_name'],
            'donor_email' => $validated['donor_email'],
            'donor_phone' => $validated['donor_phone'],
            'message' => $validated['message'],
            'details' => ['meal_type' => $validated['meal_type'] ?? 'Custom'],
            'status' => 'pending'
        ]);

        // Notify Admins
        $admins = \App\Models\User::where('institution_id', $validated['institution_id'])->whereIn('role', ['admin'])->get();
        // Also notify System Admins
        $systemAdmins = \App\Models\User::where('role', 'system_admin')->get();
        
        $recipients = $admins->merge($systemAdmins);
        \Illuminate\Support\Facades\Notification::send($recipients, new \App\Notifications\NewDonationRequest($donationRequest));

        return redirect()->back()->with('success', 'Thank you! Your donation request has been submitted. We will contact you shortly.');
    }
}
