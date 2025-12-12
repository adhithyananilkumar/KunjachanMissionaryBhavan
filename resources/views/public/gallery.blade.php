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
                $galleryImages = [
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
            @endphp
            @foreach($galleryImages as $image)
                <figure class="gallery-item">
                    <img src="{{ asset('assets/gallery/' . $image) }}" alt="Gallery image" loading="lazy">
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endsection