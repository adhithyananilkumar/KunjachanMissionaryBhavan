<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Models\DonationRequest;
use Illuminate\Http\Request;

class DonationRequestController extends Controller
{
    public function index()
    {
        $requests = DonationRequest::with('institution')->latest()->paginate(20);
        return view('system_admin.donation_requests.index', compact('requests'));
    }

    public function show(DonationRequest $donationRequest)
    {
        return view('system_admin.donation_requests.show', compact('donationRequest'));
    }

    public function update(Request $request, DonationRequest $donationRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,contacted,completed,cancelled',
        ]);
        
        $donationRequest->update($validated);
        return back()->with('success', 'Status updated successfully.');
    }

    public function destroy(DonationRequest $donationRequest)
    {
        $donationRequest->delete();
        return redirect()->route('system_admin.donation-requests.index')->with('success', 'Request deleted.');
    }
}
