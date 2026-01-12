<x-app-layout>
	<x-slot name="header">
		<div class="d-flex justify-content-between align-items-center">
			<h2 class="h5 mb-0">Payments</h2>
			<button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#payModal">
				<span class="bi bi-cash-coin me-1"></span> Pay
			</button>
		</div>
	</x-slot>

	<div class="card shadow-sm mb-3">
		<div class="card-body">
			<form method="GET" class="row g-2 align-items-end">
				<div class="col-md-3">
					<label class="form-label small mb-1">Inmate (by name)</label>
					<select name="inmate_id" class="form-select form-select-sm">
						<option value="">All inmates</option>
						@foreach($inmatesForSelect as $i)
							<option value="{{ $i->id }}" @selected((int)($inmateId ?? 0) === (int)$i->id)>
								{{ $i->first_name }} {{ $i->last_name }}
								@isset($i->admission_number)
									- {{ $i->admission_number }}
								@endisset
							</option>
						@endforeach
					</select>
				</div>
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
<<<<<<< HEAD
					<div class="text-muted small mb-1">This month collected (paid)</div>
					<div class="h5 mb-1">₹ {{ number_format($summary['total_amount'] ?? 0, 2) }}</div>
					<div class="small text-muted">Current month based on payment date.</div>
=======
					<div class="text-muted small mb-1">Total collected (paid)</div>
					<div class="h5 mb-0">₹ {{ number_format($summary['total_amount'] ?? 0, 2) }}</div>
>>>>>>> 3e03daa29128f97355c96e657850f19885d91155
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="card shadow-sm h-100">
				<div class="card-body">
<<<<<<< HEAD
					<div class="text-muted small mb-1">Payments this month</div>
					<div class="h5 mb-1">{{ $summary['count'] ?? 0 }}</div>
					<div class="small text-muted">Number of payments in the current month.</div>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="card shadow-sm h-100">
				<div class="card-body d-flex flex-column">
					<div class="text-muted small mb-1">All-time summary</div>
					<div class="small mb-1">Total collected: ₹ {{ number_format($summary['all_time_total'] ?? 0, 2) }}</div>
					<div class="small mb-3">Payments count: {{ $summary['all_time_count'] ?? 0 }}</div>
					<div class="mt-auto d-flex gap-2">
						<button type="button" id="paymentsSummaryToggle" class="btn btn-outline-secondary btn-sm flex-grow-1">Summary details</button>
						<a href="{{ route('system_admin.payments.report', ['mode' => 'detailed']) }}" class="btn btn-primary btn-sm">Download report</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="card shadow-sm mb-3" id="paymentsSummaryDetailsCard" style="display:none;">
		<div class="card-body small text-muted">
			<strong>Summary details:</strong> Total collected across all time is ₹ {{ number_format($summary['all_time_total'] ?? 0, 2) }}, with {{ $summary['all_time_count'] ?? 0 }} payments recorded (respecting current filters).
		</div>
=======
					<div class="text-muted small mb-1">Payments count</div>
					<div class="h5 mb-0">{{ $summary['count'] ?? 0 }}</div>
				</div>
			</div>
		</div>
>>>>>>> 3e03daa29128f97355c96e657850f19885d91155
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
<<<<<<< HEAD
							<td>
								<a href="{{ route('system_admin.payments.receipt', $p) }}" class="btn btn-outline-secondary btn-sm">Download</a>
							</td>
						</tr>
					@empty
						<tr><td colspan="9" class="text-center text-muted py-4">No payments found.</td></tr>
=======
						</tr>
					@empty
						<tr><td colspan="8" class="text-center text-muted py-4">No payments found.</td></tr>
>>>>>>> 3e03daa29128f97355c96e657850f19885d91155
					@endforelse
				</tbody>
			</table>
		</div>
		@if($payments->hasPages())
			<div class="card-footer small">{{ $payments->links() }}</div>
		@endif
	</div>

	<!-- Quick Pay Modal: search any inmate and record a payment -->
	<div class="modal fade" id="payModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Record Payment</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="mb-3">
						<label class="form-label small mb-1">Search inmate</label>
						<input type="text" class="form-control form-control-sm" id="paySearch" placeholder="Type name, admission # or registration #" autocomplete="off">
					</div>
					<div class="list-group small mb-3" id="payResults" style="max-height:200px; overflow:auto;"></div>
					<form method="POST" id="payForm" class="row g-2 small" action="" autocomplete="off">
						@csrf
						<input type="hidden" name="_inmate_id" id="payInmateId" />
						<div class="col-12 mb-2">
							<div class="text-muted" id="paySelectedLabel">No inmate selected.</div>
						</div>
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
							<select name="method" class="form-select form-select-sm" id="payMethod">
								<option value="cash">Cash</option>
								<option value="upi">UPI</option>
								<option value="bank_transfer">Bank transfer</option>
								<option value="card">Card</option>
								<option value="other">Other</option>
							</select>
						</div>
						<div class="col-md-3" id="payReceiverGroup">
							<label class="form-label mb-1">Receiver name</label>
							<input type="text" name="notes" class="form-control form-control-sm" placeholder="Person who received cash" />
						</div>
						<div class="col-md-3 d-none" id="payReferenceGroup">
							<label class="form-label mb-1">Reference</label>
							<input type="text" name="reference" class="form-control form-control-sm" placeholder="Txn / reference ID" />
						</div>
						<div class="col-md-3 d-none" id="payNotesGroup">
							<label class="form-label mb-1">Notes</label>
							<textarea name="extra_notes" class="form-control form-control-sm" rows="1" placeholder="Optional notes"></textarea>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary btn-sm" id="paySubmitBtn" disabled>Save payment</button>
				</div>
			</div>
		</div>
	</div>

	<script>
		document.addEventListener('DOMContentLoaded', function(){
			const searchInput = document.getElementById('paySearch');
			const results = document.getElementById('payResults');
			const inmateIdField = document.getElementById('payInmateId');
			const selectedLabel = document.getElementById('paySelectedLabel');
			const form = document.getElementById('payForm');
			const submitBtn = document.getElementById('paySubmitBtn');
			const methodSelect = document.getElementById('payMethod');
			const receiverGroup = document.getElementById('payReceiverGroup');
			const referenceGroup = document.getElementById('payReferenceGroup');
			const notesGroup = document.getElementById('payNotesGroup');
<<<<<<< HEAD
			const summaryToggle = document.getElementById('paymentsSummaryToggle');
			const summaryCard = document.getElementById('paymentsSummaryDetailsCard');

			if(summaryToggle && summaryCard){
				summaryToggle.addEventListener('click', function(e){
					e.preventDefault();
					if(summaryCard.style.display === 'none' || summaryCard.style.display === ''){
						summaryCard.style.display = 'block';
					} else {
						summaryCard.style.display = 'none';
					}
				});
			}
=======
>>>>>>> 3e03daa29128f97355c96e657850f19885d91155

			if(!searchInput || !results || !form || !submitBtn) return;

			let timeout = null;
			function renderResults(items){
				results.innerHTML = '';
				if(!items.length){
					results.innerHTML = '<div class="list-group-item text-muted">No inmates found.</div>';
					return;
				}
				items.forEach(function(it){
					const el = document.createElement('button');
					el.type = 'button';
					el.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
					el.innerHTML = '<span>' + it.name + '<div class="text-muted small">Admission # ' + (it.admission_number || '—') + '</div></span>' +
						'<span class="badge bg-light text-dark">' + (it.institution || '—') + '</span>';
					el.addEventListener('click', function(){
						inmateIdField.value = it.id;
						selectedLabel.textContent = 'Selected: ' + it.name + ' (Adm # ' + (it.admission_number || '—') + ')';
						submitBtn.disabled = false;
					});
					results.appendChild(el);
				});
			}

			async function searchInmates(term){
				term = term.trim();
				if(term.length < 2){
					results.innerHTML = '';
					return;
				}
				try{
					const res = await fetch("{{ route('system_admin.inmates.search') }}?q=" + encodeURIComponent(term), {headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}});
					if(!res.ok){ throw new Error('Search failed'); }
					const data = await res.json();
					renderResults(data.data || []);
				}catch(e){
					results.innerHTML = '<div class="list-group-item text-danger small">Search error.</div>';
				}
			}

			searchInput.addEventListener('input', function(){
				clearTimeout(timeout);
				timeout = setTimeout(function(){ searchInmates(searchInput.value); }, 300);
			});

			submitBtn.addEventListener('click', function(){
				if(!inmateIdField.value){ return; }
				// build action URL for selected inmate and submit
<<<<<<< HEAD
				form.action = "{{ url('system-admin/inmates') }}" + '/' + inmateIdField.value + '/payments';
=======
				form.action = "{{ url('system-admin/inmates') }}/" + inmateIdField.value + "/payments";
>>>>>>> 3e03daa29128f97355c96e657850f19885d91155
				form.submit();
			});

			function updateMethodUI(){
				const m = methodSelect ? methodSelect.value : 'cash';
				if(!receiverGroup || !referenceGroup || !notesGroup) return;
				if(m === 'cash'){
					receiverGroup.classList.remove('d-none');
					referenceGroup.classList.add('d-none');
					notesGroup.classList.add('d-none');
				}else{
					receiverGroup.classList.add('d-none');
					referenceGroup.classList.remove('d-none');
					notesGroup.classList.remove('d-none');
				}
			}
			if(methodSelect){
				methodSelect.addEventListener('change', updateMethodUI);
				updateMethodUI();
			}
		});
	</script>
</x-app-layout>
