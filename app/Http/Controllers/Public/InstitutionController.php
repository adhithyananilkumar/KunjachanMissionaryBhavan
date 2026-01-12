<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    public function index()
    {
        $institutions = Institution::where('status', 'active')->get();
        return view('public.institutions.index', compact('institutions'));
    }

    public function show($id)
    {
        $institution = Institution::where('status', 'active')->findOrFail($id);
        return view('public.institutions.show', compact('institution'));
    }
}
