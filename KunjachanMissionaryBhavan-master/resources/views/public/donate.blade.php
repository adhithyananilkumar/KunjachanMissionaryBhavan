@extends('layouts.public')
@section('title','Donate')
@section('content')
<section class="hero">
    <div class="container">
        <div class="surface mb-3">
            <div class="section-heading">Donate</div>
            <h1 class="h3 mb-1">Support our mission</h1>
            <p class="muted mb-0">Each institution can have a unique “Donate a Meal” menu and pricing. Choose an institution below.</p>
            @if(session('success'))
                <div class="alert alert-success mt-3 mb-0">{{ session('success') }}</div>
            @endif
        </div>

        <div class="row g-4">
            @foreach($institutions as $inst)
                @php
                    $settings = $inst->donationSetting;
                    $breakfast = $settings ? $settings->breakfast_amount : 0;
                    $lunch = $settings ? $settings->lunch_amount : 0;
                    $dinner = $settings ? $settings->dinner_amount : 0;
                    $fullDay = $breakfast + $lunch + $dinner;
                    $otherAmount = $settings ? $settings->other_amount : 1000;
                @endphp
                <div class="col-12">
                    <div class="surface shadow-sm">
                        <div class="row align-items-center mb-3">
                            <div class="col-md-8">
                                <h2 class="h5 mb-1">{{ $inst->name }}</h2>
                                <p class="small text-muted mb-0">{{ $inst->address }}</p>
                            </div>
                            <div class="col-md-4 text-md-end mt-2 mt-md-0">
                                <a href="{{ route('institutions.show', $inst->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill">View Profile</a>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-6 col-md-3">
                                <div class="surface border text-center p-3 h-100">
                                    <div class="h5 mb-0">₹{{ number_format($breakfast) }}</div>
                                    <div class="small text-muted mb-2">Breakfast</div>
                                    <button class="btn btn-kb btn-sm rounded-pill w-100" onclick="openDonateModal('{{ $inst->id }}', '{{ $breakfast }}', 'Breakfast', '{{ $inst->name }}')">Donate</button>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="surface border text-center p-3 h-100">
                                    <div class="h5 mb-0">₹{{ number_format($lunch) }}</div>
                                    <div class="small text-muted mb-2">Lunch</div>
                                    <button class="btn btn-kb btn-sm rounded-pill w-100" onclick="openDonateModal('{{ $inst->id }}', '{{ $lunch }}', 'Lunch', '{{ $inst->name }}')">Donate</button>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="surface border text-center p-3 h-100">
                                    <div class="h5 mb-0">₹{{ number_format($dinner) }}</div>
                                    <div class="small text-muted mb-2">Dinner</div>
                                    <button class="btn btn-kb btn-sm rounded-pill w-100" onclick="openDonateModal('{{ $inst->id }}', '{{ $dinner }}', 'Dinner', '{{ $inst->name }}')">Donate</button>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="surface border text-center p-3 h-100">
                                    <div class="h5 mb-0">₹{{ number_format($fullDay) }}</div>
                                    <div class="small text-muted mb-2">Full Day</div>
                                    <button class="btn btn-kb btn-sm rounded-pill w-100" onclick="openDonateModal('{{ $inst->id }}', '{{ $fullDay }}', 'Full day', '{{ $inst->name }}')">Donate</button>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 text-center">
                            <span class="text-muted small me-2">Or contribute what you can</span>
                            <button class="btn btn-link btn-sm text-kb text-decoration-none fw-bold" onclick="openDonateModal('{{ $inst->id }}', '{{ $otherAmount }}', 'Custom Amount', '{{ $inst->name }}')">Custom Amount</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row g-3 mt-4">
            <div class="col-md-6">
                <div class="surface h-100 border-0 shadow-sm">
                    <h3 class="h6 fw-bold mb-3"><i class="bi bi-bank me-2"></i>Bank Transfer</h3>
                    <ul class="list-plain small muted">
                        <li><strong>Account:</strong> Missionary Bhavan</li>
                        <li><strong>IFSC:</strong> ABCD000000</li>
                        <li><strong>Branch:</strong> Kochi</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="surface h-100 border-0 shadow-sm">
                    <h3 class="h6 fw-bold mb-3"><i class="bi bi-gift me-2"></i>In-Kind Donations</h3>
                    <p class="small muted mb-3">We also accept food supplies, clothes, medicines, and professional services.</p>
                    <a href="{{ route('contact') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4">Contact for In-Kind</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Donation Modal -->
<div class="modal fade" id="donateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Donate Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-4" id="modalIntro">Please provide your details so we can contact you regarding your donation.</p>
                <form action="{{ route('donate.store') }}" method="POST" id="donateForm">
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
                        <textarea name="message" class="form-control" rows="2" placeholder="Any specific requests or preferences?"></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-kb rounded-pill py-2 fw-bold">Submit Donation Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openDonateModal(instId, amount, label, instName) {
        const numericAmount = String(amount).replace(/[^0-9.]/g, '');
        
        document.getElementById('modalInstitutionId').value = instId;
        document.getElementById('modalAmount').value = numericAmount;
        document.getElementById('modalMealType').value = label;
        
        if (instName) {
            document.getElementById('modalIntro').innerHTML = `Please provide your details so we can contact you regarding your <strong>${label}</strong> donation request for <strong>${instName}</strong>.`;
        }

        var myModal = new bootstrap.Modal(document.getElementById('donateModal'));
        myModal.show();
    }
</script>
@endsection