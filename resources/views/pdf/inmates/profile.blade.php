@extends('pdf.layouts.base')

@php($generatedAt = $generatedAt ?? now())
@php($generatedBy = $generatedBy ?? (auth()->user()?->name ?? 'System'))

@section('content')
<header class="mb-3">
    <div class="section-title" style="margin-top: 0; border: none;">INMATE PROFILE</div>
    <h1 class="mb-1">Inmate profile report</h1>
    <p class="small mb-0">Admission reference: {{ $inmate->admission_number }}</p>
</header>

<!-- IDENTITY SECTION -->
<div class="section-title">IDENTITY</div>
<table class="data-table">
    <tr>
        <td class="label">Full name</td>
        <td class="value">{{ $inmate->full_name }}</td>
    </tr>

    <tr>
        <td class="label">Admission no.</td>
        <td class="value">{{ $inmate->admission_number ?: '—' }}</td>
    </tr>

    @if($inmate->institution?->name)
        <tr>
            <td class="label">Institution</td>
            <td class="value">{{ $inmate->institution->name }}</td>
        </tr>
    @endif

    @php($typeLabel = $inmate->type ? ucfirst(str_replace('_', ' ', $inmate->type)) : null)
    @if($typeLabel)
        <tr>
            <td class="label">Category</td>
            <td class="value">{{ $typeLabel }}</td>
        </tr>
    @endif

    @if($inmate->date_of_birth)
        <tr>
            <td class="label">Date of birth</td>
            <td class="value">{{ $inmate->date_of_birth->format('Y-m-d') }}</td>
        </tr>
    @endif

    @if($inmate->age)
        <tr>
            <td class="label">Age (years)</td>
            <td class="value">{{ $inmate->age }}</td>
        </tr>
    @endif
</table>

<!-- ALLOCATION SECTION -->
@php($currentLocationName = optional($inmate->currentLocation?->location)->name)
@if($currentLocationName)
    <div class="section-title">CURRENT ALLOCATION</div>
    <table class="data-table">
        <tr>
            <td class="label">Location</td>
            <td class="value">{{ $currentLocationName }}</td>
        </tr>
    </table>
@endif

<!-- HEATLH SECTION -->
@php($hasCritical = filled($inmate->critical_alert))
@php($geriatric = $inmate->geriatricCarePlan)
@php($mental = $inmate->mentalHealthPlan)
@php($rehab = $inmate->rehabilitationPlan)

@if($hasCritical || $geriatric || $mental || $rehab)
    <div class="section-title">HEALTH AND CARE SUMMARY</div>
    <table class="data-table">
        @if($hasCritical)
            <tr>
                <td class="label" style="color:#d32f2f;">Critical notes</td>
                <td class="value" style="color:#d32f2f;">
                    {{ trim(preg_replace('/\s+/', ' ', strip_tags($inmate->critical_alert))) }}</td>
            </tr>
        @endif

        @if($geriatric)
            @if($geriatric->mobility_status)
                <tr>
                    <td class="label">Mobility</td>
                    <td class="value">{{ $geriatric->mobility_status }}</td>
                </tr>
            @endif
            @if($geriatric->dietary_needs)
                <tr>
                    <td class="label">Dietary</td>
                    <td class="value">{{ $geriatric->dietary_needs }}</td>
                </tr>
            @endif
        @endif

        @if($mental && $mental->diagnosis)
            <tr>
                <td class="label">Mental health diagnosis</td>
                <td class="value">{{ $mental->diagnosis }}</td>
            </tr>
        @endif

        @if($rehab)
            @if($rehab->primary_issue)
                <tr>
                    <td class="label">Rehabilitation focus</td>
                    <td class="value">{{ $rehab->primary_issue }}</td>
                </tr>
            @endif
            @if($rehab->program_phase)
                <tr>
                    <td class="label">Programme phase</td>
                    <td class="value">{{ $rehab->program_phase }}</td>
                </tr>
            @endif
        @endif
    </table>
@endif

<section class="signature-block">
    <div class="small" style="color: #666;">This document is generated from the institutional case management system for
        administrative use.</div>
    <div class="signature-line"></div>
    <div class="small mt-1">Authorised signatory</div>
</section>
@endsection