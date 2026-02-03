@extends('layouts.public')
@section('title','Donate')
@section('content')
<section class="hero">
    <div class="container">
        <div class="surface mb-3">
            <div class="section-heading">Donate</div>
            <h1 class="h3 mb-1">Support our mission</h1>
            <p class="muted mb-0">Each institution can have a unique “Donate a Meal” menu and pricing. Choose an institution below.</p>
        </div>

        <ul class="nav nav-pills mb-3" id="donate-tabs" role="tablist">
            @foreach($institutions as $index => $inst)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $index === 0 ? 'active' : '' }}" id="pills-{{ $inst->id }}-tab" data-bs-toggle="pill" data-bs-target="#pills-{{ $inst->id }}" type="button" role="tab" aria-controls="pills-{{ $inst->id }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">{{ $inst->name }}</button>
                </li>
            @endforeach
        </ul>
        <div class="tab-content" id="donate-tabsContent">
            @foreach($institutions as $index => $inst)
                @php
                    $settings = $inst->donationSetting;
                    $breakfast = $settings ? $settings->breakfast_amount : 0;
                    $lunch = $settings ? $settings->lunch_amount : 0;
                    $dinner = $settings ? $settings->dinner_amount : 0;
                    $fullDay = $breakfast + $lunch + $dinner;
                    $other = $settings ? $settings->other_amount : null;
                @endphp
                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="pills-{{ $inst->id }}" role="tabpanel" aria-labelledby="pills-{{ $inst->id }}-tab">
                    @include('public.partials.donate-menu', [
                        'title' => $inst->name . " — Donate a Meal",
                        'inst_id' => $inst->id,
                        'inst_name' => $inst->name,
                        'pricing' => [
                            ['₹'.number_format($breakfast), 'Breakfast'],
                            ['₹'.number_format($lunch), 'Lunch'],
                            ['₹'.number_format($dinner), 'Dinner'],
                            ['₹'.number_format($fullDay), 'Full day']
                        ],
                    ])
                    @if($other)
                         <div class="mt-3 text-center">
                            <button class="btn btn-link text-muted small text-decoration-none" 
                                data-bs-toggle="modal" 
                                data-bs-target="#donationModal"
                                data-inst-id="{{ $inst->id }}"
                                data-inst-name="{{ $inst->name }}"
                                data-amount="{{ $other }}"
                                data-meal="Custom Amount"
                            >Or donate a custom amount (Default: ₹{{ number_format($other) }})</button>
                         </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="row g-3 mt-1">
            <div class="col-md-6">
                <div class="surface h-100">
                    <h3 class="h6">Bank Transfer</h3>
                    <ul class="list-plain small muted">
                        <li><strong>Account:</strong> Missionary Bhavan</li>
                        <li><strong>IFSC:</strong> ABCD000000</li>
                        <li><strong>Branch:</strong> Kochi</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="surface h-100">
                    <h3 class="h6">In-Kind</h3>
                    <p class="small muted mb-2">We also accept supplies and services.</p>
                    <a href="{{ route('contact') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Contact us</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .modal-backdrop.show { opacity: 0.5; }
</style>
@endpush

@push('scripts')
<div class="modal fade" id="donationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">Make a Donation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="modal-desc" class="text-muted small mb-4">You are donating to <strong id="modal-inst-name"></strong>.</p>
                <form action="{{ route('donate.store') }}" method="post" id="donation-form">
                    @csrf
                    <input type="hidden" name="institution_id" id="modal-inst-id">
                    <input type="hidden" name="details[meal_type]" id="modal-meal-type">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Amount (₹)</label>
                        <input type="number" name="amount" id="modal-amount" class="form-control" placeholder="Enter amount" required min="1">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Your Name</label>
                        <input type="text" name="donor_name" class="form-control" placeholder="Full Name" required>
                    </div>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Phone</label>
                            <input type="tel" name="donor_phone" class="form-control" placeholder="Mobile Number" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Email (Optional)</label>
                            <input type="email" name="donor_email" class="form-control" placeholder="Email Address">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Message (Optional)</label>
                        <textarea name="message" class="form-control" rows="2" placeholder="Any specific instructions or prayers?"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-kb w-100 rounded-pill">Confirm Donation Promise</button>
                    <div class="text-center mt-2">
                        <small class="text-muted" style="font-size: 0.75rem;">We will contact you to arrange the payment.</small>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg text-center p-4">
            <div class="modal-body">
                <div class="mb-3 text-success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                    </svg>
                </div>
                <h4 class="h5 mb-2">Thank You!</h4>
                <p class="text-muted small mb-4">{{ session('success') }}</p>
                <button type="button" class="btn btn-kb rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var donationModal = document.getElementById('donationModal');
        donationModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var instId = button.getAttribute('data-inst-id');
            var instName = button.getAttribute('data-inst-name');
            var amount = button.getAttribute('data-amount');
            var meal = button.getAttribute('data-meal');
            
            // Clean amount string (remove non-numeric)
            var cleanAmount = amount ? amount.replace(/[^0-9.]/g, '') : '';
            
            document.getElementById('modal-inst-id').value = instId;
            document.getElementById('modal-inst-name').textContent = instName;
            document.getElementById('modal-amount').value = cleanAmount;
            document.getElementById('modal-meal-type').value = meal || 'Custom';
            
            // Update description based on meal
            var desc = 'You are donating to <strong>' + instName + '</strong>';
            if(meal && meal !== 'Custom') {
                desc += ' for <strong>' + meal + '</strong>.';
            }
            document.getElementById('modal-desc').innerHTML = desc;
        });
        
        // Show success modal if session has success
        @if(session('success'))
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        @endif
    });
</script>
@endpush
