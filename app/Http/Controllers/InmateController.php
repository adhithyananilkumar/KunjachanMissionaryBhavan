<?php

namespace App\Http\Controllers;

use App\Models\Inmate;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InmateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Inmate::with('institution');

        if (auth()->user()->role !== 'developer') {
            $query->where('institution_id', auth()->user()->institution_id);
        }

        $inmates = $query->paginate(15);
        return view('inmates.index', ['inmates' => $inmates]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $institutions = [];
        if ($user?->role === 'developer') {
            $institutions = Institution::orderBy('name')->get();
        }
        return view('inmates.create', compact('institutions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'admission_date' => 'required|date',
            'notes' => 'nullable|string',
        ];

        if ($user?->role === 'developer') {
            $rules['institution_id'] = 'required|exists:institutions,id';
        }

        $validated = $request->validate($rules);

        if ($user?->role !== 'developer') {
            $validated['institution_id'] = $user?->institution_id; // force scope
        }

        Inmate::create($validated);

    return redirect()->route('inmates.index')->with('success', 'Inmate created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inmate $inmate)
    {
        return view('inmates.edit', compact('inmate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inmate $inmate)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'admission_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $inmate->update($validated);

    return redirect()->route('inmates.index')->with('success', 'Inmate updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inmate $inmate)
    {
        $inmate->delete();
    return redirect()->route('inmates.index')->with('success', 'Inmate deleted successfully.');
    }
}
