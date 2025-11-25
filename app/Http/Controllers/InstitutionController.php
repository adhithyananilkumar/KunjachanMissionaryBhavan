<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    $institutions = Institution::all(); // Fetch all institutions
    return view('institutions.index', ['institutions' => $institutions]); // Pass them to the view
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    return view('institutions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        Institution::create($validated);

        return redirect()->route('institutions.index')
            ->with('status', 'Institution created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Institution $institution)
    {
        return view('institutions.edit', ['institution' => $institution]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Institution $institution)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $institution->update($validated);

        return redirect()->route('institutions.index')
            ->with('status', 'Institution updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Institution $institution)
    {
        try {
            $institution->delete();
            return redirect()->route('institutions.index')
                ->with('status', 'Institution deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()->route('institutions.index')
                ->with('error', 'Unable to delete institution: ' . $e->getMessage());
        }
    }
}
