<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function index()
    {
        $images = GalleryImage::latest()->paginate(24);
        return view('system_admin.gallery.index', compact('images'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240', // 10MB max
            'caption' => 'nullable|string|max:255',
            'institution_id' => 'nullable|exists:institutions,id',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'gallery_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/gallery'), $filename);

            GalleryImage::create([
                'image_path' => $filename,
                'caption' => $request->caption,
                'institution_id' => $request->institution_id,
            ]);

            if ($request->institution_id) {
                return redirect()->to(route('system_admin.institutions.show', $request->institution_id) . '#gallery')->with('success', 'Image uploaded successfully.');
            }

            return redirect()->route('system_admin.gallery.index')->with('success', 'Image uploaded successfully.');
        }

        return back()->with('error', 'No image file provided.');
    }

    public function destroy(GalleryImage $gallery)
    {
        $institutionId = $gallery->institution_id;
        $path = public_path('assets/gallery/' . $gallery->image_path);
        
        if (!empty($gallery->image_path) && file_exists($path) && is_file($path)) {
            unlink($path);
        }

        $gallery->delete();

        if ($institutionId) {
            return redirect()->to(route('system_admin.institutions.show', $institutionId) . '#gallery')->with('success', 'Image deleted successfully.');
        }

        return redirect()->route('system_admin.gallery.index')->with('success', 'Image deleted successfully.');
    }
}
