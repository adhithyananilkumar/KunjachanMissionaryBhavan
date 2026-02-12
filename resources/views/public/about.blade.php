@extends('layouts.public')
@section('title', 'About')
@section('content')
    <section class="hero hero-about">
        <div class="container">
            <div class="about-hero fade-in">
                <div class="tagline mb-3">About Us</div>
                <h1 class="about-title">Kunjachan Missionary Bhavan</h1>
                <p class="about-subtitle mt-3">
                    A faith inspired home committed to holistic care for physical, emotional, and spiritual well being.
                    We serve with compassion and dignity, supporting residents and families through every season.
                </p>
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <a href="{{ route('institutions.index') }}" class="btn btn-outline-secondary rounded-pill px-3">Explore our institutions</a>
                    <a href="{{ route('donate') }}" class="btn btn-kb rounded-pill px-3">Support our mission</a>
                </div>
            </div>

            <div class="about-metrics mt-4">
                <div class="metric fade-in" style="animation-delay:.05s">
                    <div class="metric-kicker">Established</div>
                    <div class="metric-value">2001</div>
                    <div class="metric-note muted">October 16</div>
                </div>
                <div class="metric fade-in" style="animation-delay:.10s">
                    <div class="metric-kicker">Focus</div>
                    <div class="metric-value">Holistic care</div>
                    <div class="metric-note muted">Physical, emotional, spiritual</div>
                </div>
                <div class="metric fade-in" style="animation-delay:.15s">
                    <div class="metric-kicker">Guided by</div>
                    <div class="metric-value">Compassion</div>
                    <div class="metric-note muted">Dignity & Community</div>
                </div>
            </div>

            <div class="about-split mt-5">
                <div class="fade-in" style="animation-delay:.05s">
                    <div class="section-heading">What We Do</div>
                    <h2 class="h3 mb-3">Care, rehabilitation, and belonging</h2>
                    <p class="muted mb-4">
                        We aim to provide a safe, supportive environment and help individuals move toward greater independence,
                        social integration, and well-being.
                    </p>

                    <div class="about-pillars">
                        <div class="pillar">
                            <div class="pillar-icon"><i class="bi bi-heart-pulse"></i></div>
                            <div>
                                <div class="pillar-title">Health & daily support</div>
                                <div class="muted">Medication support, nutrition, clothing, and a homely environment.</div>
                            </div>
                        </div>
                        <div class="pillar">
                            <div class="pillar-icon"><i class="bi bi-chat-heart"></i></div>
                            <div>
                                <div class="pillar-title">Counseling & psycho-education</div>
                                <div class="muted">Counseling, crisis support, and practical guidance for growth.</div>
                            </div>
                        </div>
                        <div class="pillar">
                            <div class="pillar-icon"><i class="bi bi-briefcase"></i></div>
                            <div>
                                <div class="pillar-title">Training & rehabilitation</div>
                                <div class="muted">Skill-building, vocational support, and multi-disciplinary intervention.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fade-in" style="animation-delay:.10s">
                    <div class="section-heading">Vision & Values</div>
                    <div class="surface about-values">
                        <ul class="list-check mb-0">
                            <li>Compassion in action</li>
                            <li>Respect for every person</li>
                            <li>Transparency and stewardship</li>
                            <li>Community and belonging</li>
                        </ul>
                    </div>

                    <div class="surface mt-3 about-cta">
                        <div class="section-heading mb-2">Get Involved</div>
                        <p class="muted mb-3">Your support strengthens our care and programs.</p>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('contact') }}" class="btn btn-outline-secondary rounded-pill px-3">Contact us</a>
                            <a href="{{ route('donate') }}" class="btn btn-kb rounded-pill px-3">Donate</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="about-divider my-5" aria-hidden="true"></div>

            <div class="surface about-background fade-in" style="animation-delay:.05s">
                <div class="section-heading">Our Background</div>
                <h2 class="h4 mb-3">Rooted in service, committed to dignity</h2>
                <div class="muted">
                    <p>
                        Kunjachan Missionary Bhavan, Psycho-social Rehabilitation Centre was established on October 16, 2001
                        in Ramapuram, Idiyanal. It is a Vincentian Project, registered under Board of Control for Orphanages
                        and Other Charitable Homes, Kerala (Recognition No. 872).
                    </p>
                    <p>
                        Our mission includes protection and rehabilitation, helping individuals develop independence and social integration
                        through education, training, and multi-disciplinary support. Our work also includes psycho-education, counseling,
                        crisis support, and vocational training.
                    </p>
                    <p class="mb-0">
                        We strive to ensure quality care and a peaceful, homely environment — supporting residents with medication,
                        food, clothing, and ongoing guidance.
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection