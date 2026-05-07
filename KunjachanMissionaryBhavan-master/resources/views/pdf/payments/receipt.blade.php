@extends('pdf.layouts.base')

@php($generatedAt = $generatedAt ?? now())
@php($generatedBy = $generatedBy ?? (auth()->user()?->name ?? 'System'))

@section('content')
<header class="mb-3">
    <h1 class="mb-1">Payment receipt</h1>
    <p class="small mb-0">Receipt for inmate payment</p>
</header>

@php($inmate = $payment->inmate)
@php($institution = $payment->institution ?? $inmate?->institution)

<div class="section-title">PAYMENT DETAILS</div>
<table class="data-table">
    @if($payment->payment_date)
        <tr>
            <td class="label">Payment date</td>
            <td class="value">{{ $payment->payment_date->format('Y-m-d') }}</td>
        </tr>
    @endif
    <tr>
        <td class="label">Amount</td>
        <td class="value">Rs. {{ number_format($payment->amount, 2) }}</td>
    </tr>
    <tr>
        <td class="label">Status</td>
        <td class="value">{{ ucfirst($payment->status) }}</td>
    </tr>
    <tr>
        <td class="label">Method</td>
        <td class="value">{{ $payment->method ?: '—' }}</td>
    </tr>
    <tr>
        <td class="label">Reference</td>
        <td class="value">{{ $payment->reference ?: '—' }}</td>
    </tr>
    <tr>
        <td class="label">Period</td>
        <td class="value">{{ $payment->period_label ?: '—' }}</td>
    </tr>
</table>

@if($inmate)
    <div class="section-title">INMATE</div>
    <table class="data-table">
        <tr>
            <td class="label">Full name</td>
            <td class="value">{{ $inmate->full_name }}</td>
        </tr>
        <tr>
            <td class="label">Admission no.</td>
            <td class="value">{{ $inmate->admission_number ?: '—' }}</td>
        </tr>
    </table>
@endif

@if($institution)
    <div class="section-title">INSTITUTION</div>
    <table class="data-table">
        <tr>
            <td class="label">Name</td>
            <td class="value">{{ $institution->name }}</td>
        </tr>
    </table>
@endif

<section class="signature-block">
    <div class="small" style="color: #666;">This receipt is generated from the institutional case management system.</div>
    <div class="signature-line"></div>
    <div class="small mt-1">Authorised signatory</div>
</section>
@endsection
