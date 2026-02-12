<?php

namespace App\Http\Controllers\SystemAdmin;

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

        if (str_contains($imagePath, '/')) {
            try {
                Storage::delete($imagePath);
                return;
            } catch (\Throwable $e) {
                // continue
            }
        }

        $legacyPath = public_path('assets/gallery/' . $imagePath);
        if (file_exists($legacyPath) && is_file($legacyPath)) {
            @unlink($legacyPath);
        }
    }

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
            $dir = StoragePath::galleryImageDir();
            $this->ensureMediaDir($dir);
            $filename = StoragePath::uniqueName($file);
            $path = Storage::putFileAs($dir, $file, $filename);

            GalleryImage::create([
                'image_path' => $path,
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
        $this->safeDeleteGalleryPath($gallery->image_path);

        $gallery->delete();

        if ($institutionId) {
            return redirect()->to(route('system_admin.institutions.show', $institutionId) . '#gallery')->with('success', 'Image deleted successfully.');
        }

        return redirect()->route('system_admin.gallery.index')->with('success', 'Image deleted successfully.');
    }
}
