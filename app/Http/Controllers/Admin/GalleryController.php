<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $images = GalleryImage::latest()->paginate(12);
        return view('admin.gallery.index', compact('images'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('assets/gallery'), $imageName);

            GalleryImage::create([
                'title' => $request->title,
                'image_path' => 'assets/gallery/' . $imageName,
                'is_featured' => $request->has('is_featured'),
            ]);
        }

        return back()->with('success', 'Image uploaded successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GalleryImage $galleryImage)
    {
         // Delete file if it exists
         if(file_exists(public_path($galleryImage->image_path))){
            unlink(public_path($galleryImage->image_path));
        }

        $galleryImage->delete();

        return back()->with('success', 'Image deleted successfully.');
    }
}
