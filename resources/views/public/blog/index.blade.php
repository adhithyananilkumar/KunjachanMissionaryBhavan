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

        <div class="feed-search mb-3">
            <i class="bi bi-search"></i>
            <input id="feedSearch" type="search" placeholder="Search posts..." aria-label="Search posts">
        </div>

        <div id="feed" class="feed">
            @foreach(range(1,8) as $i)
            <article class="feed-item fade-in" data-text="sample post {{ $i }} reflections community updates mission love care faith">
                <div class="feed-avatar">{{ substr('KM', 0, 2) }}</div>
                <div class="feed-body">
                    <div class="d-flex justify-content-between">
                        <div class="feed-meta">
                            <strong>Kunjachan Bhavan</strong>
                            <span>·</span>
                            <time datetime="{{ now()->subHours($i*3)->toIso8601String() }}">{{ now()->subHours($i*3)->diffForHumans() }}</time>
                        </div>
                        <div class="text-muted"><i class="bi bi-three-dots"></i></div>
                    </div>
                    <div class="mt-1">This is a short update from our community — placeholder text for the feed experience. Post {{ $i }}.</div>
                    @if($i % 2 === 0)
                        <div class="feed-media">
                            <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=1600&auto=format&fit=crop" alt="Post media">
                        </div>
                    @endif
                    <div class="feed-actions">
                        <span><i class="bi bi-heart"></i></span>
                        <span><i class="bi bi-chat"></i></span>
                        <span><i class="bi bi-share"></i></span>
                    </div>
                </div>
            </article>
            @endforeach
        </div>
    </div>
    </section>
@endsection