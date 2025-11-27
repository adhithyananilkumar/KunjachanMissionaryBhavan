@extends('layouts.public')

@section('title','Project Timeline')

@section('content')
<section class="py-4 hero">
    <div class="container">
        <div class="row mb-4">
            <div class="col-lg-8 mx-auto text-center">
                <div class="surface">
                    <div class="section-heading">Project Timeline</div>
                    <h1 class="h3 mb-2">Step-by-step journey</h1>
                    <p class="muted mb-0">Follow the weekly milestones from initial consultation to final review.</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="surface timeline-vertical">

                    {{-- Week 1 --}}
                    <div class="timeline-row">
                        <div class="timeline-col timeline-col-left">
                            <div class="timeline-card">
                                <div class="timeline-week">Week 1</div>
                                <h2 class="h5 mb-1">Initial Consult</h2>
                                <p class="muted mb-0">Review terms and clarify project scope with the client.</p>
                            </div>
                        </div>
                        <div class="timeline-col timeline-col-icon timeline-col-icon--shift-left">
                            <div class="timeline-icon"><i class="fas fa-comments"></i></div>
                        </div>
                        <div class="timeline-col timeline-col-right"></div>
                    </div>

                    {{-- Week 2 --}}
                    <div class="timeline-row">
                        <div class="timeline-col timeline-col-left"></div>
                        <div class="timeline-col timeline-col-icon timeline-col-icon--shift-right">
                            <div class="timeline-icon"><i class="fas fa-question-circle"></i></div>
                        </div>
                        <div class="timeline-col timeline-col-right">
                            <div class="timeline-card">
                                <div class="timeline-week">Week 2</div>
                                <h2 class="h5 mb-1">Design Inquiry</h2>
                                <p class="muted mb-0">Discuss preferred styles, needs, and gather detailed design requirements.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Week 3 --}}
                    <div class="timeline-row">
                        <div class="timeline-col timeline-col-left">
                            <div class="timeline-card">
                                <div class="timeline-week">Week 3</div>
                                <h2 class="h5 mb-1">Initial Design Concept</h2>
                                <p class="muted mb-0">Present the first concept with layout and key visual directions.</p>
                            </div>
                        </div>
                        <div class="timeline-col timeline-col-icon timeline-col-icon--shift-left">
                            <div class="timeline-icon"><i class="fas fa-lightbulb"></i></div>
                        </div>
                        <div class="timeline-col timeline-col-right"></div>
                    </div>

                    {{-- Week 4 --}}
                    <div class="timeline-row">
                        <div class="timeline-col timeline-col-left"></div>
                        <div class="timeline-col timeline-col-icon timeline-col-icon--shift-right">
                            <div class="timeline-icon"><i class="fas fa-pencil-ruler"></i></div>
                        </div>
                        <div class="timeline-col timeline-col-right">
                            <div class="timeline-card">
                                <div class="timeline-week">Week 4</div>
                                <h2 class="h5 mb-1">Second Design Concept</h2>
                                <p class="muted mb-0">Refine the concept, finalize fixtures, materials, and layout options.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Week 5 --}}
                    <div class="timeline-row">
                        <div class="timeline-col timeline-col-left">
                            <div class="timeline-card">
                                <div class="timeline-week">Week 5</div>
                                <h2 class="h5 mb-1">Send Out Design Concept</h2>
                                <p class="muted mb-0">Share the approved concept and supporting documents with stakeholders.</p>
                            </div>
                        </div>
                        <div class="timeline-col timeline-col-icon timeline-col-icon--shift-left">
                            <div class="timeline-icon"><i class="fas fa-paper-plane"></i></div>
                        </div>
                        <div class="timeline-col timeline-col-right"></div>
                    </div>

                    {{-- Week 6 --}}
                    <div class="timeline-row">
                        <div class="timeline-col timeline-col-left"></div>
                        <div class="timeline-col timeline-col-icon timeline-col-icon--shift-right">
                            <div class="timeline-icon"><i class="fas fa-chalkboard"></i></div>
                        </div>
                        <div class="timeline-col timeline-col-right">
                            <div class="timeline-card">
                                <div class="timeline-week">Week 6</div>
                                <h2 class="h5 mb-1">Finalize Sales Presentation</h2>
                                <p class="muted mb-0">Prepare the full presentation, confirm scope, and review final details.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Week 7 --}}
                    <div class="timeline-row">
                        <div class="timeline-col timeline-col-left">
                            <div class="timeline-card">
                                <div class="timeline-week">Week 7</div>
                                <h2 class="h5 mb-1">Purchasing of Materials</h2>
                                <p class="muted mb-0">Order and coordinate delivery of all approved project materials.</p>
                            </div>
                        </div>
                        <div class="timeline-col timeline-col-icon timeline-col-icon--shift-left">
                            <div class="timeline-icon"><i class="fas fa-shopping-cart"></i></div>
                        </div>
                        <div class="timeline-col timeline-col-right"></div>
                    </div>

                    {{-- Week 8 --}}
                    <div class="timeline-row">
                        <div class="timeline-col timeline-col-left"></div>
                        <div class="timeline-col timeline-col-icon timeline-col-icon--shift-right">
                            <div class="timeline-icon"><i class="fas fa-tools"></i></div>
                        </div>
                        <div class="timeline-col timeline-col-right">
                            <div class="timeline-card">
                                <div class="timeline-week">Week 8</div>
                                <h2 class="h5 mb-1">Start Initial Installation</h2>
                                <p class="muted mb-0">Begin on-site installation in line with the agreed project scope.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Week 9 --}}
                    <div class="timeline-row">
                        <div class="timeline-col timeline-col-left">
                            <div class="timeline-card">
                                <div class="timeline-week">Week 9</div>
                                <h2 class="h5 mb-1">Review</h2>
                                <p class="muted mb-0">Walk through the completed work, collect feedback, and close out.</p>
                            </div>
                        </div>
                        <div class="timeline-col timeline-col-icon timeline-col-icon--shift-left">
                            <div class="timeline-icon"><i class="fas fa-check-circle"></i></div>
                        </div>
                        <div class="timeline-col timeline-col-right"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
@endsection


@push('styles')
<style>
.timeline-vertical {
    position: relative;
    padding: 2.5rem 1.5rem;
    overflow: hidden;
}

/* Perfectly centered curved dotted line */
..timeline-vertical::before {
    content: "";
    position: absolute;
    top: -80px;
    left: 50%;
    width: 240px;
    height: calc(100% + 160px);
    transform: translateX(-50%);
    pointer-events: none;

    background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 240 1800' preserveAspectRatio='xMidYMid meet'%3E%3Cpath d='M120 0 C 160 250 160 450 120 650 S 80 1100 120 1400 S 160 1650 120 1800' fill='none' stroke='%23b91c1c' stroke-width='3' stroke-dasharray='4 6' /%3E%3C/svg%3E")
        center/contain no-repeat;

    z-index: 0;
}


.timeline-row {
    display: grid;
    grid-template-columns: 1fr 120px 1fr;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 3rem;
    position: relative;
    z-index: 1;
}

.timeline-card {
    background: #fff;
    border-radius: 0.75rem;
    padding: 1.25rem 1.5rem;
    box-shadow: 0 0.25rem 0.9rem rgba(15,23,42,.08);
}

/* Icon stays exactly on the line */
.timeline-col-icon {
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
}

.timeline-icon {
    width: 3.25rem;
    height: 3.25rem;
    border-radius: 50%;
    background: #e9efff;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid #fff;
    position: relative;
    z-index: 2;
}

/* Controlled side shifting without touching line */
.timeline-col-icon--shift-left {
    margin-left: -50px;
}

.timeline-col-icon--shift-right {
    margin-left: 50px;
}

.timeline-col-left .timeline-card {
    margin-right: 2.5rem;
}

.timeline-col-right .timeline-card {
    margin-left: 2.5rem;
}

@media (max-width: 767px) {
    .timeline-vertical::before {
        display: none;
    }

    .timeline-row {
        grid-template-columns: 1fr;
    }

    .timeline-col-icon {
        margin: 1rem 0;
    }

    .timeline-col-icon--shift-left,
    .timeline-col-icon--shift-right {
        margin-left: 0;
    }

    .timeline-col-left .timeline-card,
    .timeline-col-right .timeline-card {
        margin: 0;
    }
}

</style>
@endpush
