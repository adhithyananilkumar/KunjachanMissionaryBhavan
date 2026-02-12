@extends('pdf.layouts.base')

@php($generatedAt = $generatedAt ?? now())
@php($generatedBy = $generatedBy ?? (auth()->user()?->name ?? 'System'))

@section('content')
<header class="mb-3">
    <h1 class="mb-1">Payments report</h1>
    <p class="small mb-0">
        Period:
        @if($summary['from_date'] && $summary['to_date'])
            {{ $summary['from_date']->format('Y-m-d') }} to {{ $summary['to_date']->format('Y-m-d') }}
        @elseif($summary['from_date'])
            From {{ $summary['from_date']->format('Y-m-d') }}
        @elseif($summary['to_date'])
            Up to {{ $summary['to_date']->format('Y-m-d') }}
        @else
            All time
        @endif
        @if(!empty($summary['method']) && $summary['method'] !== 'all')
            | Method: {{ ucfirst(str_replace('_',' ',$summary['method'])) }}
        @endif
        @if(!empty($summary['institution_name']))
            | Institution: {{ $summary['institution_name'] }}
        @endif
        @if(!empty($summary['status']) && $summary['status'] !== 'all')
            | Status: {{ ucfirst($summary['status']) }}
        @endif
        @if(!empty($summary['search']))
            | Search: {{ $summary['search'] }}
        @endif
    </p>
</header>

@if($mode !== 'summary')
    <div class="section-title">DETAILED PAYMENTS</div>
    <table class="data-table">
        <tr>
            <td class="label">Date</td>
            <td class="label">Inmate</td>
            <td class="label">Institution</td>
            <td class="label">Amount</td>
            <td class="label">Status</td>
            <td class="label">Period</td>
            <td class="label">Method</td>
            <td class="label">Reference</td>
        </tr>
        @forelse($payments as $p)
            <tr>
                <td class="value">{{ $p->payment_date?->format('Y-m-d') }}</td>
                <td class="value">{{ $p->inmate?->full_name }}</td>
                <td class="value">{{ $p->institution?->name }}</td>
                <td class="value">Rs. {{ number_format($p->amount, 2) }}</td>
                <td class="value">{{ ucfirst($p->status) }}</td>
                <td class="value">{{ $p->period_label ?: '—' }}</td>
                <td class="value">{{ $p->method ?: '—' }}</td>
                <td class="value">{{ $p->reference ?: '—' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="value">No payments in selected filters.</td>
            </tr>
        @endforelse
    </table>
@endif

<div class="section-title">TOTALS & STATISTICS</div>
<table class="data-table">
    <tr>
        <td class="label">Total amount (paid)</td>
        <td class="value">Rs. {{ number_format($summary['paid_total_amount'] ?? ($summary['total_amount'] ?? 0), 2) }}</td>
    </tr>
    <tr>
        <td class="label">Payments count (all)</td>
        <td class="value">{{ $summary['total_count'] ?? ($summary['count'] ?? 0) }}</td>
    </tr>
    @if(isset($summary['paid_count']))
        <tr>
            <td class="label">Paid payments</td>
            <td class="value">{{ $summary['paid_count'] }}</td>
        </tr>
    @endif
    @if(isset($summary['pending_count']))
        <tr>
            <td class="label">Pending payments</td>
            <td class="value">{{ $summary['pending_count'] }}</td>
        </tr>
    @endif
    @if(isset($summary['failed_count']))
        <tr>
            <td class="label">Failed payments</td>
            <td class="value">{{ $summary['failed_count'] }}</td>
        </tr>
    @endif
    @if(isset($summary['refunded_count']))
        <tr>
            <td class="label">Refunded payments</td>
            <td class="value">{{ $summary['refunded_count'] }}</td>
        </tr>
    @endif
</table>

<section class="signature-block">
    <div class="small" style="color: #666;">This report is generated from the institutional case management system for internal administrative use.</div>
    <div class="signature-line"></div>
    <div class="small mt-1">Authorised signatory</div>
</section>
@endsection
