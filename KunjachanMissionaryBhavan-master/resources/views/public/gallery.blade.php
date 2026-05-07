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
            @if($images->isEmpty())
                <div class="text-center w-100 py-5 text-muted">No images available yet.</div>
            @endif
            @foreach($images as $image)
                <figure class="gallery-item">
                    <img src="{{ $image->image_url }}" alt="{{ $image->caption ?? 'Gallery image' }}" loading="lazy">
                    @if($image->caption)
                        <figcaption class="text-center mt-2 small text-muted">{{ $image->caption }}</figcaption>
                    @endif
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endsection