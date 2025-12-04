@extends('layouts.public')
@section('title', $institution->name)
@section('content')
<section class="hero">
    <div class="container">
        <div class="surface p-0 overflow-hidden mb-3">
            <div class="position-relative" style="height:240px;">
                @if($institution->logo)
                    <img src="{{ asset('storage/'.$institution->logo) }}" alt="{{ $institution->name }}" class="w-100 h-100" style="object-fit:cover;">
                @else
                    <div class="w-100 h-100 bg-secondary d-flex align-items-center justify-content-center">
                        <span class="bi bi-building text-white h1"></span>
                    </div>
                @endif
                <div class="position-absolute bottom-0 start-0 p-3" style="background:linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,.45) 80%); width:100%;">
                    <h1 class="h3 text-white mb-0">{{ $institution->name }}</h1>
                    <div class="text-white-50 small">{{ $institution->address }}</div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-7">
                <div class="surface h-100">
                    <div class="section-heading">About</div>
                    <p class="muted">{{ $institution->description ?? 'No description available.' }}</p>
                    <ul class="list-check">
                        <li>Resident-centered programs</li>
                        <li>Qualified staff and volunteers</li>
                        <li>Faith and community engagement</li>
                        <li>Safe, homely environment</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="surface h-100">
                    <div class="section-heading">Donate a Meal</div>
                    <p class="muted">Support {{ $institution->name }} with a meal donation. Choose a plan below.</p>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="surface text-center">
                                <div class="h4 mb-0">₹150</div>
                                <div class="small muted">Breakfast</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="surface text-center">
                                <div class="h4 mb-0">₹250</div>
                                <div class="small muted">Lunch</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="surface text-center">
                                <div class="h4 mb-0">₹200</div>
                                <div class="small muted">Dinner</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="surface text-center">
                                <div class="h4 mb-0">₹500</div>
                                <div class="small muted">Full day</div>
                            </div>
                        </div>
                    </div>
                    <div class="small text-muted mt-2">Demo only — hook this to your donation flow later.</div>
                </div>
            </div>
        </div>

        <div class="surface mt-3">
            <div class="d-flex align-items-baseline justify-content-between mb-2">
                <div>
                    <div class="section-heading">Gallery</div>
                    <h2 class="h5 mb-0">From {{ $institution->name }}</h2>
                </div>
            </div>
            <div class="gallery-grid">
                @foreach(range(1,8) as $i)
                <figure class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1524504388940-b1c1722653e1?q=80&w=800&auto=format&fit=crop" alt="Photo {{ $i }}" loading="lazy">
                </figure>
                @endforeach
            </div>
        </div>

        <div class="surface mt-3">
            <div class="d-flex align-items-baseline justify-content-between mb-2">
                <div>
                    <div class="section-heading">Blog</div>
                    <h2 class="h5 mb-0">Stories & Updates</h2>
                </div>
                <a href="{{ route('blog.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Go to Blog</a>
            </div>
            <div class="row g-3">
                @foreach(range(1,3) as $i)
                <div class="col-md-4">
                    <div class="surface h-100">
                        <h3 class="h6 mb-1">Sample Post {{ $i }}</h3>
                        <div class="small muted mb-2">{{ now()->subDays($i*3)->toFormattedDateString() }}</div>
                        <p class="small muted mb-2">A short teaser about the work and life at {{ $institution->name }}...</p>
                        <a href="{{ route('blog.index') }}" class="small">Read more</a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</section>
@endsection