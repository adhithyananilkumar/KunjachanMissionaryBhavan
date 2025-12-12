<?php

namespace App\Http\Controllers\Admin;

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
        return view('admin.gallery.index', compact('images'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240',
            'caption' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'gallery_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            
            $file->move(public_path('assets/gallery'), $filename);

            GalleryImage::create([
                'image_path' => $filename,
                'caption' => $request->caption,
            ]);

            return redirect()->route('admin.gallery.index')->with('success', 'Image uploaded successfully.');
        }

        return back()->with('error', 'No image file provided.');
    }

    public function destroy(GalleryImage $gallery)
    {
        // For admin, we should make sure they can delete (logic is same as system admin)
        $path = public_path('assets/gallery/' . $gallery->image_path);
        
        if (!empty($gallery->image_path) && file_exists($path) && is_file($path)) {
            unlink($path);
        }

        $gallery->delete();

        return redirect()->route('admin.gallery.index')->with('success', 'Image deleted successfully.');
    }
}
