<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GalleryImage;
use Illuminate\Support\Facades\File;

class GallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // specific images known to exist
        $images = [
            'chappel.jpg',
            'output_1.jpg',
            'img4.jpeg',
            'img5.jpeg',
            'img6.jpeg',
            'img7.jpeg',
            'img8.jpeg',
            'img9.jpeg',
            'img10.jpeg',
            'img11.jpeg',
            'img12.jpeg'
        ];

        foreach ($images as $image) {
            if (File::exists(public_path('assets/gallery/' . $image))) {
                GalleryImage::firstOrCreate([
                    'image_path' => 'assets/gallery/' . $image
                ], [
                    'title' => pathinfo($image, PATHINFO_FILENAME),
                    'is_featured' => false
                ]);
            }
        }
    }
}
