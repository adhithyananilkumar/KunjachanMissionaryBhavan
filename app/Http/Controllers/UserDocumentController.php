<?php

namespace App\Http\Controllers;

use App\Models\UserDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class UserDocumentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'document' => 'required|file|max:10240', // 10MB max
        ]);

        $file = $request->file('document');
        $path = $file->store('user_documents', 'private');

        Auth::user()->documents()->create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function download(UserDocument $document)
    {
        // Access control: User owns document OR is Admin/System Admin
        if (Auth::id() !== $document->user_id && !Auth::user()->hasAnyRole(['admin', 'system_admin'])) {
            abort(403);
        }

        return Storage::disk('private')->download($document->file_path, $document->file_name);
    }
}
