@extends('layouts.public')
@section('title', \Illuminate\Support\Str::headline($slug))
@section('content')
<section class="hero">
    <div class="container">
        <article class="surface">
            <div class="section-heading">Blog</div>
            <h1 class="h3 mb-1">{{ \Illuminate\Support\Str::headline($slug) }}</h1>
            <div class="small muted mb-3">{{ now()->toFormattedDateString() }} · by Admin</div>
            <p class="muted">This is a static demo post showing how a blog article might look. You can later power this with a System Admin “Webpage” module to create and manage posts.</p>
            <p>Paragraph of placeholder text. Share updates, stories, or reflections from your institutions and community outreach. Include photos, quotes, and links as needed.</p>
            <div class="gallery-grid mt-3">
                <figure class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1519681393784-d120267933ba?q=80&w=1200&auto=format&fit=crop" alt="Post image" loading="lazy">
                </figure>
                <figure class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1524504388940-b1c1722653e1?q=80&w=1200&auto=format&fit=crop" alt="Post image" loading="lazy">
                </figure>
                <figure class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?q=80&w=1200&auto=format&fit=crop" alt="Post image" loading="lazy">
                </figure>
            </div>
        </article>
    </div>
</section>
@endsection