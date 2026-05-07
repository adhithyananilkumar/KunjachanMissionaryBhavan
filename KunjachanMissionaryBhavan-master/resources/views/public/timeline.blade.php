@extends('layouts.public')

@section('title','Project Timeline')

@section('content')
<section class="py-5 hero" style="background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-5 fw-bold mb-3">Timeline</h1>
                <p class="text-muted lead mx-auto" style="max-width: 700px;">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Duis aute irure dolor in reprehenderit.
                </p>
            </div>
        </div>

        <div class="timeline-vertical-wrapper">
            <div class="timeline-vertical">
                
                {{-- Item 1: 2019 (Red) - Left: Year, Right: Content --}}
                <div class="timeline-item">
                    <div class="timeline-side left-side text-end">
                        <div class="timeline-year text-red">2019</div>
                    </div>
                    <div class="timeline-marker-wrapper">
                        <div class="timeline-marker bg-red"></div>
                    </div>
                    <div class="timeline-side right-side text-start">
                        <div class="timeline-content">
                            <h3 class="timeline-point text-red">Points 01</h3>
                            <p class="timeline-desc">
                                Elements in the subjects that have some purposes and goals for the business or company organization
                            </p>
                            <div class="timeline-footer-line bg-red ms-0"></div>
                        </div>
                    </div>
                </div>

                {{-- Item 2: 2020 (Orange) - Left: Content, Right: Year --}}
                <div class="timeline-item">
                    <div class="timeline-side left-side text-end">
                        <div class="timeline-content">
                            <h3 class="timeline-point text-orange">Points 02</h3>
                            <p class="timeline-desc">
                                Elements in the subjects that have some purposes and goals for the business or company organization
                            </p>
                            <div class="timeline-footer-line bg-orange ms-auto"></div>
                        </div>
                    </div>
                    <div class="timeline-marker-wrapper">
                        <div class="timeline-marker bg-orange"></div>
                    </div>
                    <div class="timeline-side right-side text-start">
                        <div class="timeline-year text-orange">2020</div>
                    </div>
                </div>

                {{-- Item 3: 2021 (Yellow) - Left: Year, Right: Content --}}
                <div class="timeline-item">
                    <div class="timeline-side left-side text-end">
                        <div class="timeline-year text-yellow">2021</div>
                    </div>
                    <div class="timeline-marker-wrapper">
                        <div class="timeline-marker bg-yellow"></div>
                    </div>
                    <div class="timeline-side right-side text-start">
                        <div class="timeline-content">
                            <h3 class="timeline-point text-yellow">Points 03</h3>
                            <p class="timeline-desc">
                                Elements in the subjects that have some purposes and goals for the business or company organization
                            </p>
                            <div class="timeline-footer-line bg-yellow ms-0"></div>
                        </div>
                    </div>
                </div>

                {{-- Item 4: 2022 (Teal) - Left: Content, Right: Year --}}
                <div class="timeline-item">
                    <div class="timeline-side left-side text-end">
                        <div class="timeline-content">
                            <h3 class="timeline-point text-teal">Points 04</h3>
                            <p class="timeline-desc">
                                Elements in the subjects that have some purposes and goals for the business or company organization
                            </p>
                            <div class="timeline-footer-line bg-teal ms-auto"></div>
                        </div>
                    </div>
                    <div class="timeline-marker-wrapper">
                        <div class="timeline-marker bg-teal"></div>
                    </div>
                    <div class="timeline-side right-side text-start">
                        <div class="timeline-year text-teal">2022</div>
                    </div>
                </div>

                {{-- Item 5: 2023 (Purple) - Left: Year, Right: Content --}}
                <div class="timeline-item">
                    <div class="timeline-side left-side text-end">
                        <div class="timeline-year text-purple">2023</div>
                    </div>
                    <div class="timeline-marker-wrapper">
                        <div class="timeline-marker bg-purple"></div>
                    </div>
                    <div class="timeline-side right-side text-start">
                        <div class="timeline-content">
                            <h3 class="timeline-point text-purple">Points 05</h3>
                            <p class="timeline-desc">
                                Elements in the subjects that have some purposes and goals for the business or company organization
                            </p>
                            <div class="timeline-footer-line bg-purple ms-0"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    /* Colors - Softer Palette */
    .text-red { color: #f87171; }
    .bg-red { background-color: #f87171; }
    
    .text-orange { color: #fb923c; }
    .bg-orange { background-color: #fb923c; }
    
    .text-yellow { color: #facc15; }
    .bg-yellow { background-color: #facc15; }
    
    .text-teal { color: #2dd4bf; }
    .bg-teal { background-color: #2dd4bf; }
    
    .text-purple { color: #a78bfa; }
    .bg-purple { background-color: #a78bfa; }

    .timeline-vertical-wrapper {
        padding: 2rem 0;
        position: relative;
    }

    .timeline-vertical {
        position: relative;
        max-width: 1000px;
        margin: 0 auto;
    }

    /* The main vertical connecting line */
    .timeline-vertical::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 50%;
        width: 6px;
        transform: translateX(-3px);
        background: linear-gradient(to bottom, 
            #f87171 0%, #f87171 20%, 
            #fb923c 20%, #fb923c 40%, 
            #facc15 40%, #facc15 60%, 
            #2dd4bf 60%, #2dd4bf 80%, 
            #a78bfa 80%, #a78bfa 100%
        );
        z-index: 0;
        border-radius: 4px;
    }

    .timeline-item {
        display: flex;
        align-items: center;
        margin-bottom: 4rem;
        position: relative;
        z-index: 1;
    }

    .timeline-side {
        flex: 1;
        padding: 0 3rem;
    }

    .timeline-marker-wrapper {
        flex: 0 0 60px; /* Space for the marker */
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
    }

    .timeline-marker {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 4px solid #fff;
        box-shadow: 0 0 0 2px currentColor;
        background-color: currentColor;
        transform: scale(1.5);
    }

    .timeline-year {
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1;
    }

    .timeline-point {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
    }

    .timeline-desc {
        font-size: 1rem;
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 1rem;
    }

    .timeline-footer-line {
        width: 50px;
        height: 4px;
        border-radius: 2px;
    }

    /* Responsive adjustments */
    @media (max-width: 767px) {
        .timeline-vertical::before {
            left: 20px; /* Move line to the left */
        }

        .timeline-item {
            flex-direction: column;
            align-items: flex-start;
            margin-bottom: 3rem;
        }

        .timeline-marker-wrapper {
            position: absolute;
            left: 0;
            top: 0;
            width: 40px;
            height: auto;
            justify-content: center;
            padding-top: 5px; /* Align with top text roughly */
        }
        
        .timeline-marker {
            transform: scale(1.2);
        }

        .timeline-side {
            padding: 0 0 0 3.5rem; /* Indent everything to right of line */
            width: 100%;
            text-align: left !important; /* Force left align on mobile */
        }

        .timeline-side.left-side {
            margin-bottom: 0.5rem;
        }
        
        .timeline-footer-line {
            margin-left: 0 !important; /* Force left align footer line */
        }
        
        .timeline-year {
            font-size: 1.75rem;
        }
    }
</style>
@endpush
