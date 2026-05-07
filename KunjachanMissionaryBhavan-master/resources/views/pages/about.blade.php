@extends('layouts.public')
@section('title','About')
@section('content')
<section class="py-5 position-relative overflow-hidden">
    <div class="vector-blob" aria-hidden="true">
        <svg preserveAspectRatio="xMidYMid slice" viewBox="0 0 800 600" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="g1" x1="0" x2="1" y1="0" y2="1">
                    <stop offset="0%" stop-color="#c77952" stop-opacity="0.35"/>
                    <stop offset="100%" stop-color="#a0522d" stop-opacity="0.05"/>
                </linearGradient>
            </defs>
            <path d="M680 90Q640 20 545 55T392 90Q289 70 212 110T91 240Q40 330 110 410T276 515Q344 548 420 553T562 520Q650 470 690 390T702 250Q720 160 680 90Z" fill="url(#g1)"/>
        </svg>
    </div>
    <div class="container position-relative">
        <div class="row g-5 align-items-center mb-4">
            <div class="col-lg-7">
                <h1 class="display-6 mb-3" style="color:var(--kb-primary);">About Our Centre</h1>
                <p class="lead muted">Rooted in faith and compassion, Kunjachan Missionary Bhavan provides structured psycho‑social rehabilitation fostering dignity, inner healing and societal reintegration.</p>
            </div>
            <div class="col-lg-5">
                <div class="surface">
                    <h2 class="h6 section-heading mb-3">At a Glance</h2>
                    <ul class="list-unstyled small m-0">
                        <li class="mb-2"><i class="bi bi-calendar3 me-2 text-muted"></i>Established: 16 Oct 2001</li>
                        <li class="mb-2"><i class="bi bi-geo-alt me-2 text-muted"></i>Location: Ramapuram, Idiyanal</li>
                        <li class="mb-2"><i class="bi bi-building me-2 text-muted"></i>Recognition No: 872</li>
                        <li class="mb-0"><i class="bi bi-cross me-2 text-muted"></i>Vincentian Project</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="surface h-100">
                    <h2 class="h6 section-heading mb-3">Background</h2>
                    <p class="mb-3">Founded to respond to the needs of vulnerable individuals experiencing mental health challenges and social displacement, our centre integrates spiritual care with evidence‑informed psychosocial interventions.</p>
                    <p class="mb-3">Registration under the Board of Control for Orphanages and Other Charitable Homes in Kerala ensures governance, accountability and adherence to welfare standards.</p>
                    <p class="mb-0">Our approach emphasizes stability, trust, routine and relational support—critical pillars enabling sustained recovery and growth.</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="surface h-100">
                    <h2 class="h6 section-heading mb-3">What We Do</h2>
                    <ul class="list-check" style="columns:1;">
                        <li>Faith-integrated emotional support</li>
                        <li>Medication & health coordination</li>
                        <li>Vocational skill cultivation</li>
                        <li>Psycho‑education & counselling</li>
                        <li>Therapeutic & structured routines</li>
                        <li>Community re‑engagement pathways</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row g-4 mt-1">
            <div class="col-12">
                <div class="surface">
                    <h2 class="h6 section-heading mb-3">Our Distinct Approach</h2>
                    <div class="row g-4">
                        <div class="col-md-4"><div class="d-flex"><div class="me-3 text-accent"><i class="bi bi-heart-pulse" style="font-size:1.7rem;color:var(--kb-accent);"></i></div><div class="small"><strong>Person‑Centric</strong><br>Individual care journeys are crafted collaboratively.</div></div></div>
                        <div class="col-md-4"><div class="d-flex"><div class="me-3"><i class="bi bi-flower3" style="font-size:1.7rem;color:var(--kb-accent);"></i></div><div class="small"><strong>Spiritual Integration</strong><br>Faith practices nurture inner resilience and meaning.</div></div></div>
                        <div class="col-md-4"><div class="d-flex"><div class="me-3"><i class="bi bi-tools" style="font-size:1.7rem;color:var(--kb-accent);"></i></div><div class="small"><strong>Skill Building</strong><br>Purposeful training fosters confidence and autonomy.</div></div></div>
                        <div class="col-md-4"><div class="d-flex"><div class="me-3"><i class="bi bi-shield-check" style="font-size:1.7rem;color:var(--kb-accent);"></i></div><div class="small"><strong>Safety & Dignity</strong><br>A homely, respectful setting encourages stability.</div></div></div>
                        <div class="col-md-4"><div class="d-flex"><div class="me-3"><i class="bi bi-layers" style="font-size:1.7rem;color:var(--kb-accent);"></i></div><div class="small"><strong>Multi‑Disciplinary</strong><br>Integrated spiritual, therapeutic and medical care.</div></div></div>
                        <div class="col-md-4"><div class="d-flex"><div class="me-3"><i class="bi bi-arrow-repeat" style="font-size:1.7rem;color:var(--kb-accent);"></i></div><div class="small"><strong>Sustainable Reintegration</strong><br>Gradual pathways reduce relapse and isolation.</div></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
