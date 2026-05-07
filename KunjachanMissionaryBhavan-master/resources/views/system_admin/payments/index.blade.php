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
			<form method="GET" class="row g-2 align-items-end" id="paymentsFilterForm">
				<div class="col-md-5">
					<label class="form-label small mb-1">Search inmate</label>
					<input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="name or admission number" />
				</div>
				<div class="col-md-4">
					<label class="form-label small mb-1">Institution</label>
					<select name="institution_id" class="form-select form-select-sm">
						<option value="">All institutions</option>
						@foreach($institutions as $inst)
							<option value="{{ $inst->id }}" @selected((int)($institutionId ?? 0) === (int)$inst->id)>{{ $inst->name }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-3">
					<label class="form-label small mb-1">Status</label>
					<select name="status" class="form-select form-select-sm">
						<option value="">All</option>
						@foreach($statuses as $s)
							<option value="{{ $s }}" @selected($status===$s)>{{ ucfirst($s) }}</option>
						@endforeach
					</select>
				</div>

				<div class="col-md-3">
					<label class="form-label small mb-1">Date filter</label>
					<select name="date_mode" class="form-select form-select-sm" id="pmDateMode">
						<option value="all" @selected(($dateMode ?? 'all')==='all')>All time</option>
						<option value="month" @selected(($dateMode ?? '')==='month')>Month</option>
						<option value="range" @selected(($dateMode ?? '')==='range')>Date range</option>
					</select>
				</div>
				<div class="col-md-3" id="pmMonthWrap" style="display:none;">
					<label class="form-label small mb-1">Month</label>
					<input type="month" name="month" value="{{ $month ?? '' }}" class="form-control form-control-sm" />
				</div>
				<div class="col-md-3" id="pmRangeWrap" style="display:none;">
					<label class="form-label small mb-1">From</label>
					<input type="date" name="from_date" value="{{ $fromDate ?? '' }}" class="form-control form-control-sm" />
				</div>
				<div class="col-md-3" id="pmRangeToWrap" style="display:none;">
					<label class="form-label small mb-1">To</label>
					<input type="date" name="to_date" value="{{ $toDate ?? '' }}" class="form-control form-control-sm" />
				</div>

				<div class="col-12 d-flex justify-content-end gap-2 mt-1">
					<button class="btn btn-primary btn-sm" type="submit">Apply</button>
					<a href="{{ route('system_admin.payments.index') }}" class="btn btn-outline-secondary btn-sm" id="paymentsResetBtn">Reset</a>
				</div>
			</form>
		</div>
	</div>

	<div id="paymentsResults">
		@include('system_admin.payments._results', ['payments' => $payments, 'summary' => $summary, 'filters' => $filters ?? []])
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
						<input type="text" class="form-control form-control-sm" id="paySearch" placeholder="name or admission number" autocomplete="off">
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
			const filterForm = document.getElementById('paymentsFilterForm');
			const resultsWrap = document.getElementById('paymentsResults');
			const resetBtn = document.getElementById('paymentsResetBtn');
			const dateModeSelect = document.getElementById('pmDateMode');
			const monthWrap = document.getElementById('pmMonthWrap');
			const rangeWrap = document.getElementById('pmRangeWrap');
			const rangeToWrap = document.getElementById('pmRangeToWrap');
			let filterTimeout = null;

			function syncDateUI(){
				if(!filterForm) return;
				const mode = (dateModeSelect?.value || 'all');
				if(monthWrap) monthWrap.style.display = mode==='month' ? '' : 'none';
				if(rangeWrap) rangeWrap.style.display = mode==='range' ? '' : 'none';
				if(rangeToWrap) rangeToWrap.style.display = mode==='range' ? '' : 'none';
				// Disable inactive fields so they don't submit
				const month = filterForm.querySelector('input[name="month"]');
				const from = filterForm.querySelector('input[name="from_date"]');
				const to = filterForm.querySelector('input[name="to_date"]');
				if(month) month.disabled = mode !== 'month';
				if(from) from.disabled = mode !== 'range';
				if(to) to.disabled = mode !== 'range';
			}

			async function fetchResults(url){
				if(!filterForm || !resultsWrap) return;
				const params = new URLSearchParams(new FormData(filterForm));
				// prune empties
				for(const [k,v] of Array.from(params.entries())){
					if(String(v).trim()==='') params.delete(k);
				}
				const finalUrl = url || (`{{ route('system_admin.payments.index') }}` + (params.toString() ? ('?' + params.toString()) : ''));
				resultsWrap.style.opacity = '0.65';
				try{
					const res = await fetch(finalUrl, {headers:{'X-Requested-With':'XMLHttpRequest'}});
					if(!res.ok) throw new Error('Failed');
					const html = await res.text();
					resultsWrap.innerHTML = html;
					history.replaceState({},'', finalUrl);
				}catch(e){
					if(window.toastr) toastr.error('Failed to load payments');
				}finally{
					resultsWrap.style.opacity = '';
				}
			}

			function scheduleFetch(immediate){
				clearTimeout(filterTimeout);
				if(immediate){ fetchResults(); return; }
				filterTimeout = setTimeout(()=> fetchResults(), 280);
			}

			if(filterForm && resultsWrap){
				syncDateUI();
				dateModeSelect?.addEventListener('change', ()=>{ syncDateUI(); scheduleFetch(true); });
				filterForm.addEventListener('input', (e)=>{
					const t = e.target;
					if(!(t instanceof HTMLElement)) return;
					if(t.matches('input[name="search"]')){ scheduleFetch(false); return; }
					if(t.matches('input[name="month"], input[name="from_date"], input[name="to_date"]')){ scheduleFetch(true); return; }
				});
				filterForm.addEventListener('change', (e)=>{
					const t = e.target;
					if(!(t instanceof HTMLElement)) return;
					if(t.matches('select, input[type="radio"]')) scheduleFetch(true);
				});
				filterForm.addEventListener('submit', (e)=>{ e.preventDefault(); scheduleFetch(true); });
				resetBtn?.addEventListener('click', (e)=>{
					e.preventDefault();
					filterForm.reset();
					// Reset should go back to the default view: current month
					if(dateModeSelect) dateModeSelect.value = 'month';
					const monthInput = filterForm.querySelector('input[name="month"]');
					if(monthInput){
						const now = new Date();
						const mm = String(now.getMonth()+1).padStart(2,'0');
						monthInput.value = `${now.getFullYear()}-${mm}`;
					}
					syncDateUI();
					scheduleFetch(true);
				});
				resultsWrap.addEventListener('click', (e)=>{
					const a = e.target.closest('a');
					if(!a) return;
					const href = a.getAttribute('href');
					if(!href) return;
					if(href.includes('page=')){
						e.preventDefault();
						fetchResults(href);
					}
				});
			}

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
					el.innerHTML = '<span>' + it.name + '<div class="text-muted small">Admission No : ' + (it.admission_number || '—') + '</div></span>' +
						'<span class="badge bg-light text-dark">' + (it.institution || '—') + '</span>';
					el.addEventListener('click', function(){
						inmateIdField.value = it.id;
						selectedLabel.textContent = 'Selected: ' + it.name + ' (Admission No : ' + (it.admission_number || '—') + ')';
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
				form.action = "{{ url('system-admin/inmates') }}" + '/' + inmateIdField.value + '/payments';
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
