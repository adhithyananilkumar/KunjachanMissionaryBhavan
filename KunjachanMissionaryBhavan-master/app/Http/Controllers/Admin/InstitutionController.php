<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    public function index()
    {
        // Admins can only see their own institution
        $institution = Institution::findOrFail(auth()->user()->institution_id);
        return redirect()->route('admin.institutions.show', $institution);
    }

    public function show(Institution $institution)
    {
        $this->authorizeInstitution($institution);
        $institution->load('donationSetting');
        $settings = $institution->donationSetting ?? new \App\Models\DonationSetting(['institution_id'=>$institution->id]);
        return view('admin.institutions.show', compact('institution', 'settings'));
    }

    public function updateDonationSettings(Request $request, Institution $institution)
    {
        $this->authorizeInstitution($institution);

        $validated = $request->validate([
            'breakfast_amount' => 'required|numeric|min:0',
            'lunch_amount' => 'required|numeric|min:0',
            'dinner_amount' => 'required|numeric|min:0',
            'other_amount' => 'nullable|numeric|min:0',
        ]);

        $institution->donationSetting()->updateOrCreate(
            ['institution_id' => $institution->id],
            $validated
        );

        return back()->with('success', 'Donation settings updated successfully.');
    }

    private function authorizeInstitution(Institution $institution)
    {
        if ($institution->id !== auth()->user()->institution_id) {
            abort(403);
        }
    }
}
