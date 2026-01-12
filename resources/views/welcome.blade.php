@extends('layouts.public')
@section('title','Home')
@section('content')
<section class="hero position-relative py-5">
    <div class="container position-relative">
        <div class="row g-5 align-items-center">
            <div class="col-lg-8">
                <div class="tagline mb-3">FOLLOW JESUS</div>
                <h1 class="display-5 display-title mb-3">KUNJACHAN MISSIONARY BHAVAN</h1>
                <p class="lead mb-4">Psycho-social rehabilitation & compassionate care fostering dignity, healing and reintegration.</p>
                <ul class="list-check mb-4">
                    <li>Holistic spiritual, medical & social support</li>
                    <li>Structured vocational & therapeutic programs</li>
                    <li>Safe, dignified and growth‑oriented environment</li>
                    <li>Community reintegration focus</li>
                    <li>Faith‑centred accompaniment</li>
                    <li>Medication & crisis management</li>
                </ul>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-kb btn-lg rounded-pill px-4"><i class="bi bi-speedometer2 me-1"></i> Open Dashboard</a>
                @endauth
            </div>
        </div>
    </div>
</section>
<section id="background" class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-7">
                <h3 class="h6 section-heading mb-2">Our Background</h3>
                <div class="surface">
                    <p class="mb-3">Kunjachan Missionary Bhavan, Psycho-social Rehabilitation Centre was established on 16 October 2001 in Ramapuram, Idiyanal. It is a Vincentian project, registered under the Board of Control for Orphanages and Other Charitable Homes, Kerala (Recognition No. 872).</p>
                    <p class="mb-3">We protect and rehabilitate mentally challenged and destitute individuals—helping each person develop independence, social integration and renewed connection to the wider community through education, training, employment pathways and multi‑disciplinary intervention.</p>
                    <p class="mb-0">We foster spiritual growth, wellbeing, psycho‑education, counselling, crisis management, drug therapy and vocational training. Through these, inner peace is nurtured and a homely environment sustains lasting recovery.</p>
                </div>
            </div>
            <div class="col-lg-5">
                <h3 class="h6 section-heading mb-2">Key Focus Areas</h3>
                <div class="surface h-100">
                    <ul class="list-check mb-0" style="columns:1;">
                        <li>Spiritual & emotional support</li>
                        <li>Psycho‑education & counselling</li>
                        <li>Crisis & medication management</li>
                        <li>Vocational & skills training</li>
                        <li>Community reintegration</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
<section id="services" class="py-5" style="background:var(--kb-bg-alt);">
    <div class="container">
        <h3 class="h6 section-heading mb-3">Services</h3>
        <div class="row g-4">
            <div class="col-md-4"><div class="surface h-100"><div class="fw-semibold mb-2"><i class="bi bi-people me-2 text-muted"></i>Rehabilitation</div><p class="small muted mb-0">Structured programs building life skills, responsibility and social confidence.</p></div></div>
            <div class="col-md-4"><div class="surface h-100"><div class="fw-semibold mb-2"><i class="bi bi-capsule-pill me-2 text-muted"></i>Medication Support</div><p class="small muted mb-0">Clinical oversight and adherence monitoring within a caring environment.</p></div></div>
            <div class="col-md-4"><div class="surface h-100"><div class="fw-semibold mb-2"><i class="bi bi-flower3 me-2 text-muted"></i>Spiritual Care</div><p class="small muted mb-0">Faith‑centred accompaniment encouraging inner healing and meaning.</p></div></div>
            <div class="col-md-4"><div class="surface h-100"><div class="fw-semibold mb-2"><i class="bi bi-tools me-2 text-muted"></i>Vocational Training</div><p class="small muted mb-0">Skill development paving pathways toward greater independence.</p></div></div>
            <div class="col-md-4"><div class="surface h-100"><div class="fw-semibold mb-2"><i class="bi bi-shield-check me-2 text-muted"></i>Safe Environment</div><p class="small muted mb-0">Dignified, protective setting with compassionate supervision.</p></div></div>
            <div class="col-md-4"><div class="surface h-100"><div class="fw-semibold mb-2"><i class="bi bi-heart-pulse me-2 text-muted"></i>Holistic Wellbeing</div><p class="small muted mb-0">Integrated physical, emotional, social and spiritual care pathways.</p></div></div>
        </div>
    </div>
</section>
<section id="contact" class="py-5">
    <div class="container">
        <div class="surface p-4 p-md-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h3 class="h6 section-heading mb-2">Contact</h3>
                    <p class="mb-2">Kunjachan Missionary Bhavan is committed to protection, rehabilitation, medication, nourishment and clothing for those entrusted to our care.</p>
                    <p class="mb-0 small muted">Authorized staff may log in for internal access.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-kb rounded-pill px-4"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-kb rounded-pill px-4"><i class="bi bi-box-arrow-in-right me-1"></i> Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
