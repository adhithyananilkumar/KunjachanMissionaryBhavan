@extends('layouts.public')
@section('title','Gallery')
@section('content')
<section class="hero">
    <div class="container">
        <div class="d-flex align-items-baseline justify-content-between mb-3">
            <div>
                <div class="section-heading">Gallery</div>
                <h1 class="h2">Glimpses of life at Bhavan</h1>
                <p class="muted mb-0">A few moments from our community and everyday care.</p>
            </div>
        </div>
        <div class="gallery-grid">
            @php
                $galleryImages = \App\Models\GalleryImage::latest()->get();
            @endphp
            @forelse($galleryImages as $image)
                <figure class="gallery-item">
                    <img src="{{ asset($image->image_path) }}" alt="{{ $image->title ?? 'Gallery image' }}" loading="lazy">
                </figure>
            @empty
                <!-- Fallback to static if no images in DB yet, or just show empty -->
                <p class="text-muted col-12 text-center">No images uploaded yet.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection