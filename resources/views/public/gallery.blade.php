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
            @foreach(range(1,12) as $i)
                <figure class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1519681393784-d120267933ba?q=80&w=800&auto=format&fit=crop" alt="Gallery image {{ $i }}" loading="lazy">
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endsection