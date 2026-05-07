@extends('layouts.public')
@section('title','Services')
@section('content')
<section class="py-5 position-relative">
    <div class="container">
        <div class="row align-items-end mb-4 g-3">
            <div class="col-lg-8">
                <h1 class="display-6 mb-2" style="color:var(--kb-primary);">Our Services</h1>
                <p class="lead muted mb-0">Integrated supports addressing body, mind, spirit and social reintegration—crafted with compassion and structure.</p>
            </div>
        </div>
        <div class="row g-4">
            @php($services = [
                ['icon'=>'people','title'=>'Residential Rehabilitation','text'=>'Structured, nurturing environment fostering daily rhythm, stability and social re-engagement.'],
                ['icon'=>'capsule-pill','title'=>'Medication Coordination','text'=>'Supervised medication adherence, review coordination and health monitoring.'],
                ['icon'=>'flower3','title'=>'Spiritual Accompaniment','text'=>'Faith practices and reflective guidance promoting inner resilience.'],
                ['icon'=>'tools','title'=>'Vocational Skill Training','text'=>'Daily purposeful tasks and incremental skill development for dignity.'],
                ['icon'=>'chat-dots','title'=>'Counselling & Psycho‑education','text'=>'Supportive therapeutic interventions and mental health literacy.'],
                ['icon'=>'shield-check','title'=>'Safety & Protection','text'=>'Supportive supervision ensuring a dignified, secure environment.'],
                ['icon'=>'heart-pulse','title'=>'Holistic Wellbeing','text'=>'Emotional, social and spiritual harmony through integrated supports.'],
                ['icon'=>'arrow-repeat','title'=>'Reintegration Pathways','text'=>'Gradual exposure, confidence building and societal preparation.'],
                ['icon'=>'activity','title'=>'Progress Monitoring','text'=>'Structured tracking of health, skill, engagement and stability metrics.'],
            ])
            @foreach($services as $service)
            <div class="col-md-6 col-lg-4">
                <div class="surface h-100 d-flex flex-column">
                    <div class="d-flex align-items-start mb-2">
                        <div class="me-3" style="width:44px;height:44px;display:flex;align-items:center;justify-content:center;border-radius:14px;background:linear-gradient(135deg,var(--kb-accent),var(--kb-accent-soft));color:#fff;">
                            <i class="bi bi-{{ $service['icon'] }}" style="font-size:1.25rem;"></i>
                        </div>
                        <div class="fw-semibold" style="line-height:1.15;">{{ $service['title'] }}</div>
                    </div>
                    <p class="small muted mb-0">{{ $service['text'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
        <div class="surface mt-5">
            <h2 class="h6 section-heading mb-3">Program Philosophy</h2>
            <p class="mb-0 small muted">Services are delivered through structured daily living, collaborative planning, evidence-informed support and spiritual grounding—building sustainable resilience rather than short-term dependence.</p>
        </div>
    </div>
</section>
@endsection
