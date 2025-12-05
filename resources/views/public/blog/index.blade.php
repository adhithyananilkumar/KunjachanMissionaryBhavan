@extends('layouts.public')
@section('title','Blog')
@section('content')
<section class="hero">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div>
                <div class="section-heading">Blog</div>
                <h1 class="h3 mb-0">Stories & Updates</h1>
            </div>
        </div>

        <form action="{{ route('blog.index') }}" method="GET" class="feed-search mb-3">
            <i class="bi bi-search"></i>
            <input id="feedSearch" name="search" type="search" placeholder="Search posts..." aria-label="Search posts" value="{{ request('search') }}">
        </form>

        <div id="feed" class="feed">
            @forelse($blogs as $blog)
            <article class="feed-item fade-in">
                <div class="feed-avatar">
                    @if($blog->author && $blog->author->profile_picture_path)
                        <img src="{{ asset('storage/' . $blog->author->profile_picture_path) }}" alt="{{ $blog->author->name }}" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        {{ substr($blog->author->name ?? 'Admin', 0, 2) }}
                    @endif
                </div>
                <div class="feed-body">
                    <div class="d-flex justify-content-between">
                        <div class="feed-meta">
                            <strong>{{ $blog->author->name ?? 'Admin' }}</strong>
                            <span>·</span>
                            <time datetime="{{ $blog->published_at->toIso8601String() }}">{{ $blog->published_at->diffForHumans() }}</time>
                            @if($blog->updated_at > $blog->published_at)
                                <span class="text-muted small ms-1">(Edited)</span>
                            @endif
                        </div>
                        <div class="text-muted"><i class="bi bi-three-dots"></i></div>
                    </div>
                    <a href="{{ route('blog.show', $blog->slug) }}" class="text-decoration-none text-dark">
                        <h5 class="mt-2 fw-bold">{{ $blog->title }}</h5>
                        <div class="mt-1">{{ Str::limit($blog->short_description, 150) }}</div>
                    </a>
                    @if($blog->featured_image)
                        <div class="feed-media mt-3">
                            <a href="{{ route('blog.show', $blog->slug) }}">
                                <img src="{{ asset('storage/' . $blog->featured_image) }}" alt="{{ $blog->title }}">
                            </a>
                        </div>
                    @endif
                    <div class="feed-actions mt-3">
                        <a href="{{ route('blog.show', $blog->slug) }}" class="btn btn-sm btn-outline-primary rounded-pill">Read More</a>
                    </div>
                </div>
            </article>
            @empty
            <div class="text-center py-5">
                <p class="text-muted">No blog posts found.</p>
            </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $blogs->links() }}
        </div>
    </div>
</section>
@endsection