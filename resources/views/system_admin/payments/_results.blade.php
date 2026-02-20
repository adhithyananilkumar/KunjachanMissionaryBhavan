@php
  $filters = $filters ?? [];
@endphp

<div class="row g-3 mb-3">
  <div class="col-md-4">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <div class="text-muted small mb-1">Total collected (paid)</div>
        <div class="h5 mb-1">₹ {{ number_format($summary['paid_total'] ?? 0, 2) }}</div>
        <div class="small text-muted">Based on current filters.</div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <div class="text-muted small mb-1">Payments (paid)</div>
        <div class="h5 mb-1">{{ $summary['paid_count'] ?? 0 }}</div>
        <div class="small text-muted">Out of {{ $summary['total_count'] ?? 0 }} payments.</div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card shadow-sm h-100">
      <div class="card-body d-flex flex-column">
        <div class="text-muted small mb-2">Custom report</div>
        <div class="small text-muted mb-3">Download an enterprise report for the current filters.</div>
        <div class="mt-auto">
          <a
            href="{{ route('system_admin.payments.report', array_merge($filters, ['mode' => 'detailed'])) }}"
            class="btn btn-primary btn-lg w-100 py-2">
            <span class="bi bi-download me-2"></span>Download custom report
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-sm table-hover align-middle mb-0">
      <thead class="table-light small">
        <tr>
          <th>Date</th>
          <th>Inmate</th>
          <th>Institution</th>
          <th class="text-end">Amount</th>
          <th>Period</th>
          <th>Status</th>
          <th>Method</th>
          <th>Reference</th>
          <th>Bill</th>
        </tr>
      </thead>
      <tbody class="small">
        @forelse($payments as $p)
          <tr>
            <td>{{ $p->payment_date?->format('d M Y') }}</td>
            <td>
              @if($p->inmate)
                <a href="{{ route('system_admin.inmates.show',$p->inmate) }}" class="text-decoration-none">{{ $p->inmate->full_name }}</a>
                <div class="text-muted">Admission No : {{ $p->inmate->admission_number ?: '—' }}</div>
              @endif
            </td>
            <td>{{ $p->institution?->name ?? '—' }}</td>
            <td class="text-end">₹ {{ number_format($p->amount, 2) }}</td>
            <td>{{ $p->period_label ?: '—' }}</td>
            <td>
              <span class="badge bg-{{ $p->status === 'paid' ? 'success' : ($p->status === 'pending' ? 'warning text-dark' : 'secondary') }}">{{ ucfirst($p->status) }}</span>
            </td>
            <td>{{ $p->method ?: '—' }}</td>
            <td>{{ $p->reference ?: '—' }}</td>
            <td>
              <a href="{{ route('system_admin.payments.receipt', $p) }}" class="btn btn-outline-secondary btn-sm">Download</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="9" class="text-center text-muted py-4">No payments found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($payments->hasPages())
    <div class="card-footer small">{{ $payments->links() }}</div>
  @endif
</div>
