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
}
