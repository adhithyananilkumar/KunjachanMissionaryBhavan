@extends('layouts.public')
@section('title', $blog->title)

@section('content')
<section class="hero">
    <div class="container">
        <div class="mb-4">
            <a href="{{ route('blog.index') }}" class="text-decoration-none text-muted">
                <i class="bi bi-arrow-left me-1"></i> Back to Blog
            </a>
        </div>

        <article class="bg-white rounded shadow-sm p-4 p-md-5">
            <header class="mb-4">
                <h1 class="fw-bold mb-2">{{ $blog->title }}</h1>
                <div class="text-muted small mb-3">
                    By <strong>{{ $blog->author->name ?? 'Admin' }}</strong>
                    <span class="mx-1">·</span>
                    <time datetime="{{ $blog->published_at->toIso8601String() }}">{{ $blog->published_at->format('F d, Y') }}</time>
                    @if($blog->updated_at > $blog->published_at)
                        <span class="text-muted fst-italic ms-1">(Edited)</span>
                    @endif
                </div>
                @if($blog->featured_image)
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $blog->featured_image) }}" alt="{{ $blog->title }}" class="img-fluid rounded w-100" style="max-height: 500px; object-fit: cover;">
                    </div>
                @endif
            </header>

            <div class="blog-content">
                {!! nl2br(e($blog->content)) !!}
            </div>
        </article>
    </div>
</section>
@endsection