<?php

use App\Models\GalleryImage;
use Illuminate\Support\Facades\File;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Cleaning up gallery images...\n";

// Get all images
$images = GalleryImage::all();

foreach ($images as $img) {
    echo "Deleting record ID: {$img->id}\n";
    $path = public_path('assets/gallery/' . $img->image_path);
    if (file_exists($path) && is_file($path)) {
        unlink($path);
        echo "Deleted file: {$path}\n";
    }
    $img->delete();
}

// Also scan directory for left-over files that might not be in DB (optional, but good for total cleanup)
// Pattern: gallery_*.{jpg,jpeg,png,webp}
$files = glob(public_path('assets/gallery/gallery_*'));
foreach ($files as $file) {
    if (is_file($file)) {
        unlink($file);
        echo "Deleted orphaned file: {$file}\n";
    }
}

echo "Cleanup complete.\n";
