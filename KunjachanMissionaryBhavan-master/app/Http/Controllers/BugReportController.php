<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BugReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BugReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'screenshot' => 'nullable|image|max:4096'
        ]);

        $path = null;
        if ($request->hasFile('screenshot')) {
            $path = $request->file('screenshot')->store('bug_screenshots');
        }

        BugReport::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'screenshot_path' => $path,
        ]);

        return back()->with('status','Bug reported');
    }
}
