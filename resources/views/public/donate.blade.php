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
                        'institution_id' => $inst->id,
                        'pricing' => [
                            ['amount' => $breakfast, 'label' => 'Breakfast'],
                            ['amount' => $lunch, 'label' => 'Lunch'],
                            ['amount' => $dinner, 'label' => 'Dinner'],
                            ['amount' => $fullDay, 'label' => 'Full day']
                        ],
                    ])
                    @if($other)
                         <div class="mt-3 text-center">
                            <p class="text-muted small">Or donate a custom amount (Default: ₹{{ number_format($other) }})</p>
                            <button class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="openDonateModal('{{ $inst->id }}', '{{ $other }}', 'Custom Amount')">Donate Custom Amount</button>
                         </div>
                    @else
                         <div class="mt-3 text-center">
                            <button class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="openDonateModal('{{ $inst->id }}', '1000', 'Custom Amount')">Donate Custom Amount</button>
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
    </div>
</section>

<!-- Donation Modal -->
<div class="modal fade" id="donateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">Donate Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">Please provide your details so we can contact you regarding your donation.</p>
                <form action="{{ route('donate.store') }}" method="POST" id="donateForm">
                    @csrf
                    <input type="hidden" name="institution_id" id="modalInstitutionId">
                    <input type="hidden" name="meal_type" id="modalMealType">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Amount (₹)</label>
                        <input type="number" name="amount" id="modalAmount" class="form-control" required min="1">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Name</label>
                        <input type="text" name="donor_name" class="form-control" required placeholder="Your Name">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" name="donor_email" class="form-control" placeholder="Email Address">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Phone</label>
                            <input type="tel" name="donor_phone" class="form-control" placeholder="Phone Number">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Message (Optional)</label>
                        <textarea name="message" class="form-control" rows="2" placeholder="Any specific requests?"></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-kb rounded-pill">Submit Donation Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openDonateModal(instId, amount, label) {
        // Strip non-numeric characters from amount if necessary
        const numericAmount = String(amount).replace(/[^0-9.]/g, '');
        
        document.getElementById('modalInstitutionId').value = instId;
        document.getElementById('modalAmount').value = numericAmount;
        document.getElementById('modalMealType').value = label;
        var myModal = new bootstrap.Modal(document.getElementById('donateModal'));
        myModal.show();
    }
</script>
@endsection