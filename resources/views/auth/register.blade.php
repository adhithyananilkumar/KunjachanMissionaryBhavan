@extends('layouts.guest')

@section('title','Registration disabled')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-9 col-lg-6 col-xl-5">
            <div class="kb-card p-4 p-md-5">
                <div class="text-center mb-3">
                    <img src="{{ asset('assets/kunjachanMissionaryLogo.png') }}" alt="Kunjachan Missionary Bhavan" style="height:64px;width:64px;border-radius:50%;box-shadow:var(--kb-shadow);object-fit:cover;">
                    <div class="mt-2 fw-semibold text-uppercase brand-title">KUNJACHAN MISSIONARY<br>BHAVAN</div>
                </div>
                <div class="section-heading mb-2">Notice</div>
                <h1 class="h3 mb-2 text-center" style="color: var(--kb-primary);">Registration is disabled</h1>
                <p class="muted mb-4 text-center">
                    Public account creation is not available. Accounts are created internally by authorized staff.
                </p>

                <div class="d-grid gap-2">
                    <a class="btn btn-outline-secondary rounded-pill py-2" href="{{ route('login') }}">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Go to Login
                    </a>
                    <a class="btn btn-link kb-link text-center" href="{{ url('/') }}">Back to Website</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
<div>&copy; {{ date('Y') }} AJCE24BCA</div>
@endsection

