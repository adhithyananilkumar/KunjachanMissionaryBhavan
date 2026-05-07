<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActionRequest;

class ActionRequestController extends Controller
{
    public function index()
    {
        $requests = ActionRequest::with('admin')->latest()->paginate(25);
        return view('developer.requests.index', compact('requests'));
    }

    public function update(Request $request, ActionRequest $actionRequest)
    {
        $data = $request->validate([
            'status' => 'required|string|max:50',
            'developer_reply' => 'nullable|string'
        ]);
        $actionRequest->update($data);
        return back()->with('status','Request updated');
    }
}
