@php
	$payments = $inmate->payments()->orderByDesc('payment_date')->orderByDesc('id')->get();
	$total = $payments->where('status','paid')->sum('amount');
@endphp

<div class="row g-3 mb-3">
	<div class="col-md-4">
		<div class="card shadow-sm h-100">
			<div class="card-body">
				<div class="text-muted small mb-1">Total paid</div>
				<div class="h5 mb-0">₹ {{ number_format($total, 2) }}</div>
			</div>
		</div>
	</div>
</div>

<div class="card shadow-sm mb-3">
	<div class="card-header py-2 d-flex justify-content-between align-items-center">
		<span class="small fw-semibold">Add Payment</span>
	</div>
	<div class="card-body">
		<form method="POST" action="{{ route('system_admin.inmates.payments.store',$inmate) }}" class="row g-2 small">
			@csrf
			<div class="col-md-3">
				<label class="form-label mb-1">Amount (₹)</label>
				<input type="number" step="0.01" min="0" name="amount" class="form-control form-control-sm" required />
			</div>
			<div class="col-md-3">
				<label class="form-label mb-1">Payment date</label>
				<input type="date" name="payment_date" class="form-control form-control-sm" value="{{ now()->toDateString() }}" required />
			</div>
			<div class="col-md-3">
				<label class="form-label mb-1">Period label</label>
				<input type="text" name="period_label" class="form-control form-control-sm" placeholder="e.g. Nov 2025" />
			</div>
			<div class="col-md-3">
				<label class="form-label mb-1">Status</label>
				<select name="status" class="form-select form-select-sm">
					<option value="paid">Paid</option>
					<option value="pending">Pending</option>
					<option value="failed">Failed</option>
					<option value="refunded">Refunded</option>
				</select>
			</div>
			<div class="col-md-3">
				<label class="form-label mb-1">Method</label>
				<select name="method" class="form-select form-select-sm" id="inmatePayMethod">
					<option value="cash">Cash</option>
					<option value="upi">UPI</option>
					<option value="bank_transfer">Bank transfer</option>
					<option value="card">Card</option>
					<option value="other">Other</option>
				</select>
			</div>
			<div class="col-md-3" id="inmatePayReceiverGroup">
				<label class="form-label mb-1">Receiver name</label>
				<input type="text" name="notes" class="form-control form-control-sm" placeholder="Person who received cash" />
			</div>
			<div class="col-md-3 d-none" id="inmatePayReferenceGroup">
				<label class="form-label mb-1">Reference</label>
				<input type="text" name="reference" class="form-control form-control-sm" placeholder="Txn / reference ID" />
			</div>
			<div class="col-md-3 d-none" id="inmatePayNotesGroup">
				<label class="form-label mb-1">Notes</label>
				<textarea name="extra_notes" class="form-control form-control-sm" rows="1" placeholder="Optional notes"></textarea>
			</div>
			<div class="col-12 d-flex justify-content-end">
				<button class="btn btn-primary btn-sm" type="submit">Save payment</button>
			</div>
		</form>
	</div>
</div>

<div class="card shadow-sm">
	<div class="table-responsive">
		<table class="table table-sm table-hover align-middle mb-0">
			<thead class="table-light small">
				<tr>
					<th>Date</th>
					<th class="text-end">Amount</th>
					<th>Period</th>
					<th>Status</th>
					<th>Method</th>
					<th>Reference</th>
<<<<<<< HEAD
					<th>Bill</th>
=======
>>>>>>> 3e03daa29128f97355c96e657850f19885d91155
				</tr>
			</thead>
			<tbody class="small">
				@forelse($payments as $p)
					<tr>
						<td>{{ $p->payment_date?->format('d M Y') }}</td>
						<td class="text-end">₹ {{ number_format($p->amount, 2) }}</td>
						<td>{{ $p->period_label ?: '—' }}</td>
						<td><span class="badge bg-{{ $p->status === 'paid' ? 'success' : ($p->status === 'pending' ? 'warning text-dark' : 'secondary') }}">{{ ucfirst($p->status) }}</span></td>
						<td>{{ $p->method ?: '—' }}</td>
						<td>{{ $p->reference ?: '—' }}</td>
<<<<<<< HEAD
						<td>
							<a href="{{ route('system_admin.payments.receipt', $p) }}" class="btn btn-outline-secondary btn-sm">Download</a>
						</td>
=======
>>>>>>> 3e03daa29128f97355c96e657850f19885d91155
					</tr>
				@empty
					<tr><td colspan="6" class="text-center text-muted py-4">No payments recorded.</td></tr>
				@endforelse
			</tbody>
		</table>
	</div>
</div>
