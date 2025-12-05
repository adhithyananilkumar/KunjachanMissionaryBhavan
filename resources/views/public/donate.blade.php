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
                        'pricing' => [
                            ['₹'.number_format($breakfast), 'Breakfast'],
                            ['₹'.number_format($lunch), 'Lunch'],
                            ['₹'.number_format($dinner), 'Dinner'],
                            ['₹'.number_format($fullDay), 'Full day']
                        ],
                    ])
                    @if($other)
                         <div class="mt-3 text-center">
                            <p class="text-muted small">Or donate a custom amount (Default: ₹{{ number_format($other) }})</p>
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