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
                    <div class="timeline-center-line" aria-hidden="true"></div>

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
                            <div class="timeline-icon">
                                <i class="fas fa-comments" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="timeline-col timeline-col-right"></div>
                    </div>

                    {{-- Week 2 --}}
                    <div class="timeline-row">
                        <div class="timeline-col timeline-col-left"></div>
                        <div class="timeline-col timeline-col-icon timeline-col-icon--shift-right">
                            <div class="timeline-icon">
                                <i class="fas fa-question-circle" aria-hidden="true"></i>
                            </div>
                            <div class="timeline-connector timeline-connector-horizontal" aria-hidden="true"></div>
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
                            <div class="timeline-icon">
                                <i class="fas fa-lightbulb" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="timeline-col timeline-col-right"></div>
                    </div>

                    {{-- Week 4 --}}
                    <div class="timeline-row">
                        <div class="timeline-col timeline-col-left"></div>
                        <div class="timeline-col timeline-col-icon timeline-col-icon--shift-right">
                            <div class="timeline-icon">
                                <i class="fas fa-pencil-ruler" aria-hidden="true"></i>
                            </div>
                            <div class="timeline-connector timeline-connector-horizontal" aria-hidden="true"></div>
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
                            <div class="timeline-icon">
                                <i class="fas fa-paper-plane" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="timeline-col timeline-col-right"></div>
                    </div>

                    {{-- Week 6 --}}
                    <div class="timeline-row">
                        <div class="timeline-col timeline-col-left"></div>
                        <div class="timeline-col timeline-col-icon timeline-col-icon--shift-right">
                            <div class="timeline-icon">
                                <i class="fas fa-chalkboard" aria-hidden="true"></i>
                            </div>
                            <div class="timeline-connector timeline-connector-horizontal" aria-hidden="true"></div>
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
                            <div class="timeline-icon">
                                <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="timeline-col timeline-col-right"></div>
                    </div>

                    {{-- Week 8 --}}
                    <div class="timeline-row">
                        <div class="timeline-col timeline-col-left"></div>
                        <div class="timeline-col timeline-col-icon timeline-col-icon--shift-right">
                            <div class="timeline-icon">
                                <i class="fas fa-tools" aria-hidden="true"></i>
                            </div>
                            <div class="timeline-connector timeline-connector-horizontal" aria-hidden="true"></div>
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
                            <div class="timeline-icon">
                                <i class="fas fa-check-circle" aria-hidden="true"></i>
                            </div>
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
    }

    .timeline-center-line {
        position: relative;
        width: 100%;
        border-top: 2px solid rgba(15, 23, 42, 0.06);
        margin-bottom: 2rem;
    }

    .timeline-row {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        gap: 1.25rem;
        margin-bottom: 2.5rem;
    }

    .timeline-row:last-child {
        margin-bottom: 0;
    }

    .timeline-col {
        min-height: 3.5rem;
    }

    .timeline-card {
        background: var(--kb-surface, #ffffff);
        border-radius: 0.75rem;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 0.25rem 0.9rem rgba(15, 23, 42, 0.08);
    }

    .timeline-icon {
        position: relative;
        z-index: 1;
        width: 3.25rem;
        height: 3.25rem;
        border-radius: 50%;
        background: var(--kb-primary-soft);
        color: var(--kb-primary-dark);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0.4rem 1rem rgba(15, 23, 42, 0.16);
        border: 3px solid #ffffff;
    }

    .timeline-icon i {
        font-size: 1.4rem;
    }

    .timeline-connector-horizontal {
        position: absolute;
        left: 50%;
        width: 90px;
        height: 0;
        border-top: 2px dashed rgba(15, 23, 42, 0.18);
        transform: translateX(-50%);
        z-index: 0;
    }

    .timeline-col-icon--shift-right .timeline-connector-horizontal {
        right: auto;
    }

    .timeline-week {
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: var(--kb-accent);
        margin-bottom: 0.15rem;
    }

    .timeline-col-icon {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .timeline-col-icon--shift-left {
        transform: translateX(-45px);
    }

    .timeline-col-icon--shift-right {
        transform: translateX(45px);
    }

    @media (min-width: 992px) {
        .timeline-vertical {
            padding: 3rem 3rem;
        }
    }

    @media (max-width: 767.98px) {
        .timeline-vertical {
            padding: 2rem 1rem;
        }

        .timeline-center-line {
            display: none;
        }

        .timeline-row {
            grid-template-columns: auto 1fr;
            grid-template-areas:
                "icon content";
            margin-bottom: 1.75rem;
        }

        .timeline-col-left,
        .timeline-col-right {
            display: none;
        }

        .timeline-col-icon {
            grid-area: icon;
            margin-right: 0.75rem;
            transform: none;
        }

        .timeline-connector {
            display: none;
        }

        .timeline-card {
            grid-area: content;
        }
    }
</style>
@endpush
