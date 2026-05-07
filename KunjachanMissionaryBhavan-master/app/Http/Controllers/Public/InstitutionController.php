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
        $institution = Institution::where('status', 'active')
            ->with(['donationSetting', 'blogs' => function($q) {
                $q->published()->latest()->take(3);
            }, 'galleryImages' => function($q) {
                $q->latest()->take(8);
            }])
            ->findOrFail($id);
            
        return view('public.institutions.show', compact('institution'));
    }
}
