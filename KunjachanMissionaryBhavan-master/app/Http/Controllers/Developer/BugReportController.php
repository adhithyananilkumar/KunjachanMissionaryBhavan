<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BugReport;

class BugReportController extends Controller
{
    public function index(Request $request)
    {
        $query = BugReport::with('user')->latest();
        if($request->filled('status')) $query->where('status',$request->status);
        if($request->filled('user')) $query->whereHas('user', fn($q)=>$q->where('name','like','%'.$request->user.'%'));
        $bugs = $query->paginate(30)->withQueryString();
        return view('developer.bugs.index', compact('bugs'));
    }

    public function update(Request $request, BugReport $bugReport)
    {
        $data = $request->validate([
            'status' => 'required|string|max:50',
            'developer_reply' => 'nullable|string',
            'developer_attachment' => 'nullable|file|max:5120'
        ]);
        if($request->hasFile('developer_attachment')){
            $path = $request->file('developer_attachment')->store('bug_responses');
            $data['developer_attachment_path'] = $path;
        }
        $data['reply_seen'] = false; // reset so user sees new reply
        $bugReport->update($data);
        return back()->with('status','Bug updated');
    }
}
