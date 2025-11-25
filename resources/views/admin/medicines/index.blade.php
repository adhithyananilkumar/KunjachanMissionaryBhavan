<x-app-layout>
<x-slot name="header"></x-slot>

<div class="container py-3" data-host="admin-medicines-index">
  <div class="d-flex align-items-center gap-2 mb-3">
  <h1 class="h5 mb-0">Inventory</h1>
    <div class="ms-auto d-flex gap-2">
      <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCatalogModal"><span class="bi bi-plus-lg me-1"></span>Add to Catalog</button>
      <form method="get" action="{{ route('admin.medicines.index') }}" class="d-flex gap-2">
        <input type="text" class="form-control form-control-sm" name="q" value="{{ $q }}" placeholder="Search catalog...">
        <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
      </form>
    </div>
  </div>

  <ul class="nav nav-tabs mb-3" id="medTabs" role="tablist">
    <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabOverview" type="button" role="tab">Overview</button></li>
    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabCatalog" type="button" role="tab">Catalog</button></li>
    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabUsage" type="button" role="tab">Usage</button></li>
    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabLowStock" type="button" role="tab">Low Stock</button></li>
    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabHistory" type="button" role="tab">History</button></li>
    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabReports" type="button" role="tab">Reports</button></li>
  </ul>

  <div class="tab-content">
    <div class="tab-pane fade show active" id="tabOverview" role="tabpanel">
      <div class="row g-3">
        <div class="col-6 col-lg-3">
          <div class="card text-center"><div class="card-body"><div class="text-muted small">Active Prescriptions</div><div class="display-6">{{ $activePrescriptions ?? 0 }}</div></div></div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="card text-center"><div class="card-body"><div class="text-muted small">Low Stock</div><div class="display-6">{{ $lowStockCount ?? 0 }}</div></div></div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="card text-center"><div class="card-body"><div class="text-muted small">In Stock Items</div><div class="display-6">{{ $totalAvailable ?? 0 }}</div></div></div>
        </div>
        <div class="col-12 col-lg-3">
          <div class="card h-100"><div class="card-header fw-semibold">Recently Added</div>
            <ul class="list-group list-group-flush">
              @forelse(($recentlyAdded ?? collect()) as $ra)
              <li class="list-group-item">{{ $ra->medicine->name ?? '' }} <span class="text-muted small">{{ $ra->created_at->diffForHumans() }}</span></li>
              @empty <li class="list-group-item text-muted small">No recent items</li> @endforelse
            </ul>
          </div>
        </div>
      </div>
      <div class="card mt-3">
        <div class="card-header fw-semibold">Recently Prescribed (30 days)</div>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead><tr><th>Medicine</th><th>Count</th><th>Stock</th><th class="text-end">Assignees</th></tr></thead>
            <tbody>
            @if(isset($recent) && $recent->count())
              @foreach($recent as $r)
                @php $inv=$r['inventory'] ?? null; $low = $inv && $inv->quantity <= $inv->threshold; @endphp
                <tr class="{{ $inv ? ($low ? 'table-danger' : 'table-success-subtle') : 'table-warning' }}">
                  <td class="fw-semibold"><span class="bi bi-capsule me-1"></span>{{ $r['name'] }}</td>
                  <td>{{ $r['count'] }}</td>
                  <td>{{ $inv ? $inv->quantity : '—' }}</td>
                  <td class="text-end"><button class="btn btn-sm btn-outline-secondary" data-action="assignees" data-name="{{ $r['name'] }}">View</button></td>
                </tr>
              @endforeach
            @else
              <tr><td colspan="4" class="text-center text-muted py-3">No recent prescriptions</td></tr>
            @endif
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="tab-pane fade" id="tabCatalog" role="tabpanel">
      <div class="card h-100">
        <div class="card-header fw-semibold">Catalog</div>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0" id="catalogTable">
            <thead><tr><th>Name</th><th>Form</th><th>Strength</th><th>Unit</th><th class="text-end">Add</th></tr></thead>
            <tbody>
              @forelse($medicines as $m)
              @php $existing = $inventories[$m->id] ?? null; @endphp
              <tr data-medicine-id="{{ $m->id }}">
                <td class="fw-semibold">{{ $m->name }}</td>
                <td>{{ $m->form }}</td>
                <td>{{ $m->strength }}</td>
                <td>{{ $m->unit }}</td>
                <td class="text-end">
                  @if($existing)
                    <span class="badge text-bg-light">Added</span>
                  @else
                    <div class="input-group input-group-sm" style="max-width:220px; float:right">
                      <input type="number" class="form-control" placeholder="Qty" min="0" data-field="quantity">
                      <input type="number" class="form-control" placeholder="Min" min="0" data-field="threshold">
                      <button class="btn btn-outline-primary" data-action="add"><i class="bi bi-plus-lg"></i></button>
                  @endif
                </td>
              </tr>
              @empty
              <tr><td colspan="5" class="text-center text-muted py-3">No catalog items</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="tab-pane fade" id="tabUsage" role="tabpanel">
      <div class="card">
        <div class="card-header d-flex align-items-center gap-2">
          <div class="fw-semibold">Usage</div>
          <div class="ms-auto d-flex gap-2">
            <input type="text" id="usageSearch" class="form-control form-control-sm" placeholder="Search medicine or inmate">
            <select id="usageDays" class="form-select form-select-sm" style="width:auto">
              <option value="7">7d</option>
              <option value="30" selected>30d</option>
              <option value="90">90d</option>
            </select>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0" id="usageTable">
            <thead><tr><th>Medicine</th><th>Inmate</th><th>Doctor</th><th>Status</th><th>Start</th><th>End</th></tr></thead>
            <tbody><tr><td colspan="6" class="text-center text-muted py-4">Loading…</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="tab-pane fade" id="tabLowStock" role="tabpanel">
      <div class="card">
        <div class="card-header fw-semibold">Low Stock</div>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0" id="lowStockTable">
            <thead><tr><th>Medicine</th><th>Qty</th><th>Min</th><th class="text-end">Actions</th></tr></thead>
            <tbody><tr><td colspan="4" class="text-center text-muted py-4">Loading…</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="tab-pane fade" id="tabHistory" role="tabpanel">
      <div class="card">
        <div class="card-header d-flex align-items-center gap-2">
          <div class="fw-semibold">History</div>
          <div class="ms-auto">
            <select id="historyDays" class="form-select form-select-sm" style="width:auto"><option value="7">7d</option><option value="30" selected>30d</option><option value="90">90d</option></select>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0" id="historyTable">
            <thead><tr><th>Time</th><th>Type</th><th>Medicine</th><th>Details</th></tr></thead>
            <tbody><tr><td colspan="4" class="text-center text-muted py-4">Loading…</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="tab-pane fade" id="tabReports" role="tabpanel">
      <div class="card">
        <div class="card-header fw-semibold">Reports</div>
        <div class="card-body d-flex flex-wrap gap-2">
          <a class="btn btn-outline-secondary" href="{{ route('admin.medicines.reports.stock') }}">Download Stock (CSV)</a>
          <a class="btn btn-outline-secondary" href="{{ route('admin.medicines.reports.prescriptions') }}">Download Prescriptions (CSV)</a>
          <a class="btn btn-outline-secondary" href="{{ route('admin.medicines.reports.usage-trends') }}">Download Usage Trends (CSV)</a>
        </div>
      </div>
    </div>
  </div>
</div>

</x-app-layout>

@push('scripts')
<script>
window.initAdminMedicinesIndex = function(host){
  const usageTBody = host.querySelector('#usageTable tbody');
  const lowTBody = host.querySelector('#lowStockTable tbody');
  const histTBody = host.querySelector('#historyTable tbody');

  // Restock action (from Low Stock tab)
  host.addEventListener('click', function(e){
    const restockBtn = e.target.closest('[data-action="restock"]');
    if(restockBtn){
      const id = restockBtn.getAttribute('data-id');
      const curQty = restockBtn.getAttribute('data-qty') || '';
      const curTh = restockBtn.getAttribute('data-th') || '0';
      const qty = prompt('Enter new quantity', curQty);
      if(qty===null) return; // cancelled
      const th = prompt('Minimum threshold', curTh);
      if(th===null) return;
      fetch('{{ route('admin.medicines.update', 0) }}'.replace('/0','/'+id),{
        method:'PUT', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json','Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'}, credentials:'same-origin',
        body: JSON.stringify({quantity: qty, threshold: th})
      }).then(r=>{ if(!r.ok) throw new Error(''+r.status); return r.json(); }).then(res=>{ if(res?.ok){ toastr.success('Updated'); loadLow(); } else { toastr.error('Failed'); } })
        .catch((e)=>{ console.error(e); toastr.error('Failed'); });
    }
  });

  host.querySelectorAll('tr[data-medicine-id] [data-action="add"]').forEach(btn=>{
    btn.addEventListener('click', function(){
      const tr = this.closest('tr');
      const medicineId = tr.getAttribute('data-medicine-id');
      const qty = tr.querySelector('input[data-field="quantity"]').value || 0;
      const th = tr.querySelector('input[data-field="threshold"]').value || 0;
      const name = tr.querySelector('td').innerText.trim();
      fetch('{{ route('admin.medicines.store') }}',{
        method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json','Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'}, credentials:'same-origin',
        body: JSON.stringify({name: name, quantity: qty, threshold: th})
      }).then(r=>{ if(!r.ok) throw new Error(''+r.status); return r.json(); }).then(res=>{
        if(res?.ok){ location.reload(); } else { toastr.error('Failed'); }
      }).catch((e)=>{ console.error(e); toastr.error('Failed'); });
    });
  });

  function loadUsage(){
    if(!usageTBody) return;
    const q = document.getElementById('usageSearch')?.value || '';
    const days = document.getElementById('usageDays')?.value || 30;
    usageTBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Loading…</td></tr>';
    fetch('{{ route('admin.medicines.usage') }}?q='+encodeURIComponent(q)+'&days='+days, {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}, credentials:'same-origin'})
      .then(r=>{ if(!r.ok) throw new Error(''+r.status); const ct=(r.headers.get('content-type')||'').toLowerCase(); if(!ct.includes('application/json')) throw new Error('Non-JSON'); return r.json(); })
      .then(rows=>{
        usageTBody.innerHTML = '';
        if(!rows.length){ usageTBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No data</td></tr>'; return; }
        for(const r of rows){
          const tr = document.createElement('tr');
          tr.innerHTML = `<td class="fw-semibold">${r.name}</td>
            <td>${r.inmate?.name || '—'}</td>
            <td>${r.doctor || '—'}</td>
            <td><span class="badge text-bg-light">${r.status||''}</span></td>
            <td>${r.start_date||'—'}</td>
            <td>${r.end_date||'—'}</td>`;
          usageTBody.appendChild(tr);
        }
      }).catch(()=>{
        usageTBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">Failed to load</td></tr>';
      });
  }
  document.getElementById('usageSearch')?.addEventListener('input', ()=>{ clearTimeout(window.__usageDebounce); window.__usageDebounce = setTimeout(loadUsage, 350); });
  document.getElementById('usageDays')?.addEventListener('change', loadUsage);
  loadUsage();
  function loadLow(){
    if(!lowTBody) return;
    lowTBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">Loading…</td></tr>';
    fetch('{{ route('admin.medicines.low-stock') }}', {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}, credentials:'same-origin'})
      .then(r=>{ if(!r.ok) throw new Error(''+r.status); const ct=(r.headers.get('content-type')||'').toLowerCase(); if(!ct.includes('application/json')) throw new Error('Non-JSON'); return r.json(); })
      .then(rows=>{
        lowTBody.innerHTML = '';
        if(!rows.length){ lowTBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">All good</td></tr>'; return; }
        for(const r of rows){
          const tr = document.createElement('tr');
          tr.innerHTML = `<td class="fw-semibold">${r.name}</td><td>${r.quantity}</td><td>${r.threshold}</td>
            <td class="text-end"><button class="btn btn-sm btn-outline-primary" data-action="restock" data-id="${r.id}" data-qty="${r.quantity}" data-th="${r.threshold}">Restock</button>
            <button class="btn btn-sm btn-outline-warning ms-2" data-action="notify" data-id="${r.id}">Notify Admin</button></td>`;
          lowTBody.appendChild(tr);
        }
  }).catch((e)=>{ lowTBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger py-4">Failed to load (${(e&&e.message)||'error'})</td></tr>`; });
  }
  loadLow();

  function loadHistory(){
    if(!histTBody) return;
    const days = document.getElementById('historyDays')?.value || 30;
    histTBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">Loading…</td></tr>';
    fetch('{{ route('admin.medicines.history') }}?days='+days, {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}, credentials:'same-origin'})
      .then(r=>{ if(!r.ok) throw new Error(''+r.status); const ct=(r.headers.get('content-type')||'').toLowerCase(); if(!ct.includes('application/json')) throw new Error('Non-JSON'); return r.json(); })
      .then(rows=>{
        histTBody.innerHTML = '';
        if(!rows.length){ histTBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">No history</td></tr>'; return; }
        for(const r of rows){
          const tr = document.createElement('tr');
          tr.innerHTML = `<td>${r.at}</td><td>${r.type}</td><td>${r.name||'—'}</td><td class="small text-muted">${r.meta?.inmate||''} ${r.meta?.status?('('+r.meta.status+')'):''} ${r.meta?.quantity?('qty '+r.meta.quantity):''}</td>`;
          histTBody.appendChild(tr);
        }
      }).catch((e)=>{ histTBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger py-4">Failed to load (${(e&&e.message)||'error'})</td></tr>`; });
  }
  document.getElementById('historyDays')?.addEventListener('change', loadHistory);
  loadHistory();

  // Assignees modal
  host.addEventListener('click', function(e){
    const btn = e.target.closest('[data-action="assignees"]');
    if(!btn) return;
    const name = decodeURIComponent(btn.getAttribute('data-name') || btn.getAttribute('data-name'));
    const modalEl = document.getElementById('assigneesModal');
    const listBody = document.querySelector('#assigneesTable tbody');
    listBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">Loading…</td></tr>';
    new bootstrap.Modal(modalEl).show();
    fetch('{{ route('admin.medicines.assignees') }}?name='+encodeURIComponent(name), {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}, credentials:'same-origin'})
      .then(r=>{ if(!r.ok) throw new Error(''+r.status); const ct=(r.headers.get('content-type')||'').toLowerCase(); if(!ct.includes('application/json')) throw new Error('Non-JSON'); return r.json(); })
      .then(rows=>{
        listBody.innerHTML = '';
        if(!rows.length){ listBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">None</td></tr>'; return; }
        for(const r of rows){
          const tr = document.createElement('tr');
          tr.innerHTML = `<td>${r.inmate}</td><td>${r.doctor||'—'}</td><td>${r.status||'—'}</td><td>${r.start_date||'—'}${r.end_date?(' → '+r.end_date):''}</td>`;
          listBody.appendChild(tr);
        }
      }).catch((e)=>{ console.error(e); listBody.innerHTML = '<tr><td colspan="4" class="text-center text-danger py-4">Failed</td></tr>'; });
  });
  // LowStock actions (notify)
  host.addEventListener('click', function(e){
    const notifyBtn = e.target.closest('[data-action="notify"]');
    if(notifyBtn){
      const id = notifyBtn.getAttribute('data-id');
      fetch('{{ url('admin/medicines') }}/'+id+'/notify', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json','X-Requested-With':'XMLHttpRequest'}, credentials:'same-origin'})
        .then(r=>{ if(!r.ok) throw new Error(''+r.status); return r.json(); })
        .then(res=>{ if(res?.ok){ toastr.info('Notification sent'); } else { toastr.error('Failed'); } })
        .catch(()=>toastr.error('Failed'));
    }
  });
};

// Auto-init (single)
(function(){
  const host = document.querySelector('[data-host="admin-medicines-index"]');
  if(!host) return;
  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', ()=>window.initAdminMedicinesIndex(host));
  } else {
    window.initAdminMedicinesIndex(host);
  }
})();
</script>

<!-- Assignees Modal -->
<div class="modal fade" id="assigneesModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Assignees</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-sm align-middle" id="assigneesTable">
            <thead><tr><th>Inmate</th><th>Doctor</th><th>Status</th><th>Duration</th></tr></thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endpush
