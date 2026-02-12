<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use App\Support\StoragePath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    private function ensureMediaDir(string $dir): void
    {
        try {
            Storage::makeDirectory($dir);
        } catch (\Throwable $e) {
            // Ignore directory marker failures (e.g. S3 compatibility)
        }
    }

    private function safeDeleteGalleryPath(?string $imagePath): void
    {
        $imagePath = (string) $imagePath;
        if ($imagePath === '') {
            return;
        }

        // New style: stored on default disk with prefix
        if (str_contains($imagePath, '/')) {
            try {
                Storage::delete($imagePath);
                return;
            } catch (\Throwable $e) {
                // continue
            }
        }

        // Legacy style: public/assets/gallery/<filename>
        $legacyPath = public_path('assets/gallery/' . $imagePath);
        if (file_exists($legacyPath) && is_file($legacyPath)) {
            @unlink($legacyPath);
        }
    }

    public function index()
    {
        $query = GalleryImage::query();
        
        // If admin role, only show their institution's images
        if (auth()->user()->role === 'admin') {
            $query->where('institution_id', auth()->user()->institution_id);
        }
        
        $images = $query->latest()->paginate(24);
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
            $dir = StoragePath::galleryImageDir();
            $this->ensureMediaDir($dir);
            $filename = StoragePath::uniqueName($file);
            $path = Storage::putFileAs($dir, $file, $filename);

            $data = [
                'image_path' => $path,
                'caption' => $request->caption,
            ];
            
            // Assign institution_id for admin users
            if (auth()->user()->role === 'admin') {
                $data['institution_id'] = auth()->user()->institution_id;
            }

            GalleryImage::create($data);

            return redirect()->route('admin.gallery.index')->with('success', 'Image uploaded successfully.');
        }

        return back()->with('error', 'No image file provided.');
    }

    public function destroy(GalleryImage $gallery)
    {
        // For admin, we should make sure they can delete (logic is same as system admin)
        $this->safeDeleteGalleryPath($gallery->image_path);

        $gallery->delete();

        return redirect()->route('admin.gallery.index')->with('success', 'Image deleted successfully.');
    }
}
