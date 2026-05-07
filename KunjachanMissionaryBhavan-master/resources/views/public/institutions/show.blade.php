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

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-3">
            <div class="col-lg-7">
                <div class="surface h-100">
                    <div class="section-heading">About</div>
                    @if($institution->description)
                        <p class="muted">{{ $institution->description }}</p>
                    @else
                        <p class="muted small py-4">Information about {{ $institution->name }} will be updated soon.</p>
                    @endif
                </div>
            </div>
            <div class="col-lg-5">
                <div class="surface h-100">
                    <div class="section-heading">Donate a Meal</div>
                    <p class="muted">Support {{ $institution->name }} with a meal donation. Choose a plan below.</p>
                    @php
                        $settings = $institution->donationSetting;
                        $breakfast = $settings ? $settings->breakfast_amount : 0;
                        $lunch = $settings ? $settings->lunch_amount : 0;
                        $dinner = $settings ? $settings->dinner_amount : 0;
                        $fullDay = $breakfast + $lunch + $dinner;
                    @endphp
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="surface text-center">
                                <div class="h4 mb-0">₹{{ number_format($breakfast) }}</div>
                                <div class="small muted">Breakfast</div>
                                <button class="btn btn-kb btn-sm rounded-pill px-3 mt-2" onclick="openDonateModal('{{ $institution->id }}', '{{ $breakfast }}', 'Breakfast')">Donate</button>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="surface text-center">
                                <div class="h4 mb-0">₹{{ number_format($lunch) }}</div>
                                <div class="small muted">Lunch</div>
                                <button class="btn btn-kb btn-sm rounded-pill px-3 mt-2" onclick="openDonateModal('{{ $institution->id }}', '{{ $lunch }}', 'Lunch')">Donate</button>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="surface text-center">
                                <div class="h4 mb-0">₹{{ number_format($dinner) }}</div>
                                <div class="small muted">Dinner</div>
                                <button class="btn btn-kb btn-sm rounded-pill px-3 mt-2" onclick="openDonateModal('{{ $institution->id }}', '{{ $dinner }}', 'Dinner')">Donate</button>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="surface text-center">
                                <div class="h4 mb-0">₹{{ number_format($fullDay) }}</div>
                                <div class="small muted">Full day</div>
                                <button class="btn btn-kb btn-sm rounded-pill px-3 mt-2" onclick="openDonateModal('{{ $institution->id }}', '{{ $fullDay }}', 'Full day')">Donate</button>
                            </div>
                        </div>
                    </div>
                    @php
                        $otherAmount = $settings ? $settings->other_amount : 1000;
                    @endphp
                    <div class="mt-3 text-center">
                        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="openDonateModal('{{ $institution->id }}', '{{ $otherAmount }}', 'Custom Amount')">Donate Custom Amount</button>
                    </div>
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
                @forelse($institution->galleryImages as $image)
                <figure class="gallery-item">
                    <img src="{{ $image->image_url }}" alt="{{ $image->caption ?? $institution->name }}" loading="lazy">
                </figure>
                @empty
                <div class="muted small py-4">No gallery images available for this institution.</div>
                @endforelse
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
                @forelse($institution->blogs as $blog)
                <div class="col-md-4">
                    <div class="surface h-100">
                        <h3 class="h6 mb-1">{{ $blog->title }}</h3>
                        <div class="small muted mb-2">{{ $blog->published_at ? $blog->published_at->toFormattedDateString() : $blog->created_at->toFormattedDateString() }}</div>
                        <p class="small muted mb-2">{{ Str::limit($blog->short_description, 100) }}</p>
                        <a href="{{ route('blog.show', $blog->slug) }}" class="small">Read more</a>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <p class="muted small py-4">No blog posts available for this institution.</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>
</section>

<!-- Donation Modal -->
<div class="modal fade" id="donateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Donate Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-4">Please provide your details so we can contact you regarding your donation request for <strong>{{ $institution->name }}</strong>.</p>
                <form action="{{ route('donate.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="institution_id" id="modalInstitutionId">
                    <input type="hidden" name="meal_type" id="modalMealType">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Amount (₹)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">₹</span>
                            <input type="number" name="amount" id="modalAmount" class="form-control border-start-0" required min="1">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Name</label>
                        <input type="text" name="donor_name" class="form-control" required placeholder="Enter your full name">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" name="donor_email" class="form-control" placeholder="Email address">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Phone</label>
                            <input type="tel" name="donor_phone" class="form-control" placeholder="Contact number">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Message (Optional)</label>
                        <textarea name="message" class="form-control" rows="2" placeholder="Any special instructions or preferences?"></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-kb rounded-pill py-2 fw-bold">Confirm Donation Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openDonateModal(instId, amount, label) {
        const numericAmount = String(amount).replace(/[^0-9.]/g, '');
        document.getElementById('modalInstitutionId').value = instId;
        document.getElementById('modalAmount').value = numericAmount;
        document.getElementById('modalMealType').value = label;
        var myModal = new bootstrap.Modal(document.getElementById('donateModal'));
        myModal.show();
    }
</script>
@endsection