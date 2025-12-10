@extends('pdf.layouts.base')

@php($generatedAt = $generatedAt ?? now())
@php($generatedBy = $generatedBy ?? (auth()->user()?->name ?? 'System'))

@section('content')
    <header class="mb-3">
        <div class="section-title">INMATE PROFILE</div>
        <h1 class="mb-1">Inmate profile report</h1>
        <p class="small mb-0">Admission reference: {{ $inmate->admission_number }}</p>
    </header>

    <section class="mt-2">
        <div class="section-title">IDENTITY</div>
        <div class="divider"></div>

        <div class="kv-row">
            <div class="kv-key">Full name</div>
            <div class="kv-value">{{ $inmate->full_name }}</div>
        </div>

        @if($inmate->registration_number)
            <div class="kv-row">
                <div class="kv-key">Registration no.</div>
                <div class="kv-value">{{ $inmate->registration_number }}</div>
            </div>
        @endif

        @if($inmate->institution?->name)
            <div class="kv-row">
                <div class="kv-key">Institution</div>
                <div class="kv-value">{{ $inmate->institution->name }}</div>
            </div>
        @endif

        @php($typeLabel = $inmate->type ? ucfirst(str_replace('_',' ',$inmate->type)) : null)
        @if($typeLabel)
            <div class="kv-row">
                <div class="kv-key">Category</div>
                <div class="kv-value">{{ $typeLabel }}</div>
            </div>
        @endif

        @if($inmate->date_of_birth)
            <div class="kv-row">
                <div class="kv-key">Date of birth</div>
                <div class="kv-value">{{ $inmate->date_of_birth->format('Y-m-d') }}</div>
            </div>
        @endif

        @if($inmate->age)
            <div class="kv-row">
                <div class="kv-key">Age (years)</div>
                <div class="kv-value">{{ $inmate->age }}</div>
            </div>
        @endif
    </section>

    @php($currentLocationName = optional($inmate->currentLocation?->location)->name)
    @if($currentLocationName)
        <section class="mt-2">
            <div class="section-title">CURRENT ALLOCATION</div>
            <div class="divider"></div>
            <div class="kv-row">
                <div class="kv-key">Location</div>
                <div class="kv-value">{{ $currentLocationName }}</div>
            </div>
        </section>
    @endif

    @php($hasCritical = filled($inmate->critical_alert))
    @php($geriatric = $inmate->geriatricCarePlan)
    @php($mental = $inmate->mentalHealthPlan)
    @php($rehab = $inmate->rehabilitationPlan)

    @if($hasCritical || $geriatric || $mental || $rehab)
        <section class="mt-2">
            <div class="section-title">HEALTH AND CARE SUMMARY</div>
            <div class="divider"></div>

            @if($hasCritical)
                <div class="kv-row">
                    <div class="kv-key">Critical notes</div>
                    <div class="kv-value">{{ trim(preg_replace('/\s+/', ' ', strip_tags($inmate->critical_alert))) }}</div>
                </div>
            @endif

            @if($geriatric && ($geriatric->mobility_status || $geriatric->dietary_needs))
                @if($geriatric->mobility_status)
                    <div class="kv-row">
                        <div class="kv-key">Mobility</div>
                        <div class="kv-value">{{ $geriatric->mobility_status }}</div>
                    </div>
                @endif
                @if($geriatric->dietary_needs)
                    <div class="kv-row">
                        <div class="kv-key">Dietary</div>
                        <div class="kv-value">{{ $geriatric->dietary_needs }}</div>
                    </div>
                @endif
            @endif

            @if($mental && $mental->diagnosis)
                <div class="kv-row">
                    <div class="kv-key">Mental health diagnosis</div>
                    <div class="kv-value">{{ $mental->diagnosis }}</div>
                </div>
            @endif

            @if($rehab && ($rehab->primary_issue || $rehab->program_phase))
                @if($rehab->primary_issue)
                    <div class="kv-row">
                        <div class="kv-key">Rehabilitation focus</div>
                        <div class="kv-value">{{ $rehab->primary_issue }}</div>
                    </div>
                @endif
                @if($rehab->program_phase)
                    <div class="kv-row">
                        <div class="kv-key">Programme phase</div>
                        <div class="kv-value">{{ $rehab->program_phase }}</div>
                    </div>
                @endif
            @endif
        </section>
    @endif

    <section class="signature-block">
        <div class="small muted">This document is generated from the institutional case management system for internal administrative use.</div>
        <div class="signature-line"></div>
        <div class="small mt-1">Authorised signatory</div>
    </section>
@endsection
