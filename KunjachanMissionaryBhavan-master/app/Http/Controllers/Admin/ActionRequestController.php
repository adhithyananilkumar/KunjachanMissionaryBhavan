<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActionRequest;
use Illuminate\Support\Facades\Auth;

class ActionRequestController extends Controller
{
    public function index()
    {
        $requests = ActionRequest::where('admin_id', Auth::id())->latest()->paginate(15);
        return view('admin.requests.index', compact('requests'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'note' => 'required|string',
        ]);
        $data['admin_id'] = Auth::id();
        ActionRequest::create($data);
        return back()->with('status','Request submitted');
    }
}
