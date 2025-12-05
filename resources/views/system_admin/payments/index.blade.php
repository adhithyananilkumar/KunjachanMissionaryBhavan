<x-app-layout>
	<x-slot name="header"><h2 class="h5 mb-0">Payments</h2></x-slot>

	<div class="card shadow-sm mb-3">
		<div class="card-body">
			<form method="GET" class="row g-2 align-items-end">
				<div class="col-md-3">
					<label class="form-label small mb-1">Institution</label>
					<select name="institution_id" class="form-select form-select-sm">
						<option value="">All institutions</option>
						@foreach($institutions as $inst)
							<option value="{{ $inst->id }}" @selected((int)($institutionId ?? 0) === (int)$inst->id)>{{ $inst->name }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-2">
					<label class="form-label small mb-1">Status</label>
					<select name="status" class="form-select form-select-sm">
						<option value="">All</option>
						@foreach($statuses as $s)
							<option value="{{ $s }}" @selected($status===$s)>{{ ucfirst($s) }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-2">
					<label class="form-label small mb-1">Period</label>
					<input type="text" name="period" value="{{ $period }}" class="form-control form-control-sm" placeholder="e.g. Nov 2025" />
				</div>
				<div class="col-md-3">
					<label class="form-label small mb-1">Search inmate</label>
					<input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Name / Admission # / Reg #" />
				</div>
				<div class="col-md-2 d-flex gap-2">
					<button class="btn btn-primary btn-sm flex-grow-1" type="submit">Filter</button>
					<a href="{{ route('system_admin.payments.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
				</div>
			</form>
		</div>
	</div>

	<div class="row g-3 mb-3">
		<div class="col-md-4">
			<div class="card shadow-sm h-100">
				<div class="card-body">
					<div class="text-muted small mb-1">Total collected (paid)</div>
					<div class="h5 mb-0">₹ {{ number_format($summary['total_amount'] ?? 0, 2) }}</div>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="card shadow-sm h-100">
				<div class="card-body">
					<div class="text-muted small mb-1">Payments count</div>
					<div class="h5 mb-0">{{ $summary['count'] ?? 0 }}</div>
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
					</tr>
				</thead>
				<tbody class="small">
					@forelse($payments as $p)
						<tr>
							<td>{{ $p->payment_date?->format('d M Y') }}</td>
							<td>
								@if($p->inmate)
									<a href="{{ route('system_admin.inmates.show',$p->inmate) }}" class="text-decoration-none">{{ $p->inmate->full_name }}</a>
									<div class="text-muted">Adm # {{ $p->inmate->admission_number }}</div>
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
						</tr>
					@empty
						<tr><td colspan="8" class="text-center text-muted py-4">No payments found.</td></tr>
					@endforelse
				</tbody>
			</table>
		</div>
		@if($payments->hasPages())
			<div class="card-footer small">{{ $payments->links() }}</div>
		@endif
	</div>
</x-app-layout>
