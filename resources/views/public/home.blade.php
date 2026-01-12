@extends('layouts.public')

<<<<<<< HEAD
@section('title', 'Home')

@section('content')
    <section class="hero position-relative">
        <div class="vector-blob" aria-hidden="true">
            <svg viewBox="0 0 800 400" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                <defs>
                    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="var(--kb-accent)" stop-opacity=".12" />
                        <stop offset="100%" stop-color="var(--kb-primary-soft)" stop-opacity=".12" />
                    </linearGradient>
                </defs>
                <path d="M0,300 C150,350 250,150 400,220 C550,290 650,120 800,180 L800,400 L0,400 Z" fill="url(#g)" />
            </svg>
        </div>
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
					<div class="surface hero-card">
                        <div class="section-heading">Welcome</div>
                        <h1 class="display-5 fw-semibold mb-3">Compassion. Dignity. Community.</h1>
                        <p class="lead muted">Kunjachan Missionary Bhavan is a place of care and belonging. We serve with
                            faith and love, supporting residents and families through every season.</p>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <a href="{{ route('about') }}" class="btn btn-kb rounded-pill px-3">Learn more</a>
                            <a href="{{ route('donate') }}" class="btn btn-outline-secondary rounded-pill px-3">Support our
                                mission</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                        <div class="surface hero-highlights h-100">
                        <div class="section-heading mb-2">Highlights</div>
                        <ul class="list-check">
                            <li>Resident-centered care</li>
                            <li>Qualified, caring staff</li>
                            <li>Spiritual support and guidance</li>
                            <li>Safe, peaceful environment</li>
                            <li>Community outreach</li>
                            <li>Transparent governance</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row mt-4 g-3">
                <div class="col-md-4">
                    <div class="surface h-100">
                        <div class="section-heading mb-2">Our Purpose</div>
                        <h3 class="h5">Serving with love</h3>
                        <p class="muted mb-0">Rooted in faith, we create a home where dignity and compassion guide every
                            interaction.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="surface h-100">
                        <div class="section-heading mb-2">Programs</div>
                        <h3 class="h5">Care and activities</h3>
                        <p class="muted mb-0">From daily care to community events, our programs foster connection and
                            well-being.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="surface h-100">
                        <div class="section-heading mb-2">Get Involved</div>
                        <h3 class="h5">Volunteer & donate</h3>
                        <p class="muted mb-0">Your support strengthens our mission. Join as a volunteer or make a
                            contribution.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-4">
        <div class="container">
            <div class="d-flex align-items-baseline justify-content-between mb-3">
                <div>
                    <div class="section-heading">Gallery</div>
                    <h2 class="h4 mb-0">Moments from our home</h2>
                </div>
                <a href="{{ route('gallery') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">View all</a>
            </div>
            <div class="marquee surface" onmouseenter="this.classList.add('paused')"
                onmouseleave="this.classList.remove('paused')">
                <div class="marquee__track">
                    @if(isset($galleryImages) && $galleryImages->count() > 0)
                        {{-- Loop twice for marquee effect --}}
                        @foreach($galleryImages as $image)
                            <div class="marquee__item">
                                <img src="{{ asset('assets/gallery/' . $image->image_path) }}"
                                    alt="{{ $image->caption ?? 'Moments from our home' }}" loading="lazy">
                            </div>
                        @endforeach
                        @foreach($galleryImages as $image)
                            <div class="marquee__item">
                                <img src="{{ asset('assets/gallery/' . $image->image_path) }}"
                                    alt="{{ $image->caption ?? 'Moments from our home' }}" loading="lazy">
                            </div>
                        @endforeach
                    @else
                        <div class="marquee__item">
                            <p class="text-white">No images in gallery</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="py-4">
        <div class="container">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="surface text-center">
                        <div class="display-6 fw-bold">24+</div>
                        <div class="muted">Years serving</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="surface text-center">
                        <div class="display-6 fw-bold">100%</div>
                        <div class="muted">Resident-first care</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="surface text-center">
                        <div class="display-6 fw-bold">7d</div>
                        <div class="muted">Care all week</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="surface text-center">
                        <div class="display-6 fw-bold">∞</div>
                        <div class="muted">Compassion</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
=======
@section('title','Home')

@section('content')
<section class="hero position-relative">
    <div class="vector-blob" aria-hidden="true">
        <svg viewBox="0 0 800 400" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <defs>
                <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="var(--kb-accent)" stop-opacity=".12"/>
                    <stop offset="100%" stop-color="var(--kb-primary-soft)" stop-opacity=".12"/>
                </linearGradient>
            </defs>
            <path d="M0,300 C150,350 250,150 400,220 C550,290 650,120 800,180 L800,400 L0,400 Z" fill="url(#g)"/>
        </svg>
    </div>
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="surface">
                    <div class="section-heading">Welcome</div>
                    <h1 class="display-5 fw-semibold mb-3">Compassion. Dignity. Community.</h1>
                    <p class="lead muted">Kunjachan Missionary Bhavan is a place of care and belonging. We serve with faith and love, supporting residents and families through every season.</p>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <a href="{{ route('about') }}" class="btn btn-kb rounded-pill px-3">Learn more</a>
                        <a href="{{ route('donate') }}" class="btn btn-outline-secondary rounded-pill px-3">Support our mission</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="surface h-100">
                    <div class="section-heading mb-2">Highlights</div>
                    <ul class="list-check">
                        <li>Resident-centered care</li>
                        <li>Qualified, caring staff</li>
                        <li>Spiritual support and guidance</li>
                        <li>Safe, peaceful environment</li>
                        <li>Community outreach</li>
                        <li>Transparent governance</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row mt-4 g-3">
            <div class="col-md-4">
                <div class="surface h-100">
                    <div class="section-heading mb-2">Our Purpose</div>
                    <h3 class="h5">Serving with love</h3>
                    <p class="muted mb-0">Rooted in faith, we create a home where dignity and compassion guide every interaction.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="surface h-100">
                    <div class="section-heading mb-2">Programs</div>
                    <h3 class="h5">Care and activities</h3>
                    <p class="muted mb-0">From daily care to community events, our programs foster connection and well-being.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="surface h-100">
                    <div class="section-heading mb-2">Get Involved</div>
                    <h3 class="h5">Volunteer & donate</h3>
                    <p class="muted mb-0">Your support strengthens our mission. Join as a volunteer or make a contribution.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-4">
    <div class="container">
        <div class="d-flex align-items-baseline justify-content-between mb-3">
            <div>
                <div class="section-heading">Gallery</div>
                <h2 class="h4 mb-0">Moments from our home</h2>
            </div>
            <a href="{{ route('gallery') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">View all</a>
        </div>
        <div class="marquee surface" onmouseenter="this.classList.add('paused')" onmouseleave="this.classList.remove('paused')">
            <div class="marquee__track">
                @if(isset($galleryImages) && $galleryImages->count() > 0)
                    {{-- Loop twice for marquee effect --}}
                    @foreach($galleryImages as $image)
                    <div class="marquee__item">
                        <img src="{{ asset('assets/gallery/' . $image->image_path) }}" alt="{{ $image->caption ?? 'Moments from our home' }}" loading="lazy">
                    </div>
                    @endforeach
                    @foreach($galleryImages as $image)
                    <div class="marquee__item">
                        <img src="{{ asset('assets/gallery/' . $image->image_path) }}" alt="{{ $image->caption ?? 'Moments from our home' }}" loading="lazy">
                    </div>
                    @endforeach
                @else
                    <div class="marquee__item">
                        <p class="text-white">No images in gallery</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<section class="py-4">
    <div class="container">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="surface text-center">
                    <div class="display-6 fw-bold">24+</div>
                    <div class="muted">Years serving</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="surface text-center">
                    <div class="display-6 fw-bold">100%</div>
                    <div class="muted">Resident-first care</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="surface text-center">
                    <div class="display-6 fw-bold">7d</div>
                    <div class="muted">Care all week</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="surface text-center">
                    <div class="display-6 fw-bold">∞</div>
                    <div class="muted">Compassion</div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
>>>>>>> 3e03daa29128f97355c96e657850f19885d91155
