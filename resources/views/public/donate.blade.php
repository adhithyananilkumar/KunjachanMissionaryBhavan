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
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-josephs-tab" data-bs-toggle="pill" data-bs-target="#pills-josephs" type="button" role="tab" aria-controls="pills-josephs" aria-selected="true">St. Joseph’s</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-marys-tab" data-bs-toggle="pill" data-bs-target="#pills-marys" type="button" role="tab" aria-controls="pills-marys" aria-selected="false">St. Mary’s</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-flower-tab" data-bs-toggle="pill" data-bs-target="#pills-flower" type="button" role="tab" aria-controls="pills-flower" aria-selected="false">Little Flower</button>
            </li>
        </ul>
        <div class="tab-content" id="donate-tabsContent">
            <div class="tab-pane fade show active" id="pills-josephs" role="tabpanel" aria-labelledby="pills-josephs-tab">
                @include('public.partials.donate-menu', [
                    'title' => "St. Joseph’s — Donate a Meal",
                    'pricing' => [ ['₹150','Breakfast'], ['₹240','Lunch'], ['₹200','Dinner'], ['₹520','Full day'] ],
                ])
            </div>
            <div class="tab-pane fade" id="pills-marys" role="tabpanel" aria-labelledby="pills-marys-tab">
                @include('public.partials.donate-menu', [
                    'title' => "St. Mary’s — Donate a Meal",
                    'pricing' => [ ['₹140','Breakfast'], ['₹260','Lunch'], ['₹210','Dinner'], ['₹560','Full day'] ],
                ])
            </div>
            <div class="tab-pane fade" id="pills-flower" role="tabpanel" aria-labelledby="pills-flower-tab">
                @include('public.partials.donate-menu', [
                    'title' => "Little Flower — Donate a Meal",
                    'pricing' => [ ['₹130','Breakfast'], ['₹230','Lunch'], ['₹190','Dinner'], ['₹500','Full day'] ],
                ])
            </div>
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