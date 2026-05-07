<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationRequest;
use Illuminate\Http\Request;

class DonationRequestController extends Controller
{
    public function index()
    {
        $requests = DonationRequest::where('institution_id', auth()->user()->institution_id)
            ->latest()
            ->paginate(20);
        return view('admin.donation_requests.index', compact('requests'));
    }

    public function show(DonationRequest $donationRequest)
    {
        if ($donationRequest->institution_id !== auth()->user()->institution_id) {
            abort(403);
        }
        return view('admin.donation_requests.show', compact('donationRequest'));
    }

    public function update(Request $request, DonationRequest $donationRequest)
    {
        if ($donationRequest->institution_id !== auth()->user()->institution_id) {
            abort(403);
        }
        $validated = $request->validate([
            'status' => 'required|in:pending,contacted,completed,cancelled',
        ]);
        
        $donationRequest->update($validated);
        return back()->with('success', 'Status updated successfully.');
    }
}
