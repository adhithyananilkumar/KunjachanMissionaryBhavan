@extends('layouts.public')
@section('title','Mission')
@section('content')
<section class="py-5 position-relative overflow-hidden">
    <div class="vector-blob" aria-hidden="true">
        <svg preserveAspectRatio="xMidYMid slice" viewBox="0 0 800 600" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="mG" x1="0" x2="1" y1="0" y2="1">
                    <stop offset="0%" stop-color="#a0522d" stop-opacity="0.30"/>
                    <stop offset="100%" stop-color="#c77952" stop-opacity="0.05"/>
                </linearGradient>
            </defs>
            <path d="M120 180Q90 120 150 80T300 55Q360 40 410 70T510 80Q590 70 640 120T700 250Q720 330 680 400T560 520Q470 565 360 560T210 500Q140 460 120 400T110 300Q110 240 120 180Z" fill="url(#mG)"/>
        </svg>
    </div>
    <div class="container position-relative">
        <div class="row g-4 align-items-center mb-5">
            <div class="col-lg-8">
                <h1 class="display-6 mb-3" style="color:var(--kb-primary);">Our Mission</h1>
                <p class="lead muted mb-0">To restore dignity, purpose and inner peace for vulnerable individuals through faith‑rooted, structured and compassionate rehabilitation.</p>
            </div>
        </div>
        <div class="row g-4 mb-2">
            <div class="col-md-6">
                <div class="surface h-100">
                    <h2 class="h6 section-heading mb-3">Core Pillars</h2>
                    <ul class="list-check" style="columns:1;">
                        <li>Faith‑centred hope and resilience</li>
                        <li>Dignified therapeutic structure</li>
                        <li>Holistic healing pathways</li>
                        <li>Skill formation & responsibility</li>
                        <li>Community reconnection</li>
                        <li>Continuity & accountable care</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="surface h-100">
                    <h2 class="h6 section-heading mb-3">Values in Action</h2>
                    <div class="row g-3 small">
                        <div class="col-6"><strong>Compassion</strong><br><span class="muted">Meeting each person with patience.</span></div>
                        <div class="col-6"><strong>Integrity</strong><br><span class="muted">Transparent, accountable stewardship.</span></div>
                        <div class="col-6"><strong>Growth</strong><br><span class="muted">Daily progress over perfection.</span></div>
                        <div class="col-6"><strong>Belonging</strong><br><span class="muted">Reweaving social connection.</span></div>
                        <div class="col-6"><strong>Hope</strong><br><span class="muted">Shaping a meaningful future.</span></div>
                        <div class="col-6"><strong>Collaboration</strong><br><span class="muted">Interdisciplinary unity in care.</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="surface mt-3">
            <h2 class="h6 section-heading mb-3">Long-Term Vision</h2>
            <p class="mb-0 small muted">A community where restored individuals flourish with sustained self‑worth, relational stability and purposeful contribution—supported by an adaptive model that remains faithful to its founding spiritual ethos.</p>
        </div>
    </div>
</section>
@endsection
