@extends('layouts.app')
@section('title','Inventory — System Admin')
@section('content')
<div class="container py-3" data-host="system-admin-medicines-index">
  <div class="d-flex align-items-center justify-content-between mb-3">
  <h1 class="h4 mb-0">Inventory</h1>
    <div class="ms-auto d-flex gap-2">
      <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCatalogModal"><span class="bi bi-plus-lg me-1"></span>Add to Catalog</button>
      <form class="" method="get" action="{{ route('system_admin.medicines.index') }}">
        <div class="input-group input-group-sm">
          <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Search medicines...">
          <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
        </div>
      </form>
    </div>
  </div>

  <ul class="nav nav-tabs mb-3">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#saTabOverview">Overview</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#saTabCatalog">Catalog</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#saTabUsage">Usage</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#saTabLowStock">Low Stock</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#saTabHistory">History</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#saTabReports">Reports</button></li>
  </ul>

  <div class="tab-content">
    <div class="tab-pane fade show active" id="saTabOverview">
      <div class="row g-3">
        <div class="col-6 col-lg-3"><div class="card text-center"><div class="card-body"><div class="text-muted small">Total Medicines</div><div class="display-6">{{ $totalMedicines ?? 0 }}</div></div></div></div>
        <div class="col-6 col-lg-3"><div class="card text-center"><div class="card-body"><div class="text-muted small">Active Prescriptions</div><div class="display-6">{{ $activePrescriptions ?? 0 }}</div></div></div></div>
        <div class="col-6 col-lg-3"><div class="card text-center"><div class="card-body"><div class="text-muted small">Low Stock</div><div class="display-6">{{ $lowStockCount ?? 0 }}</div></div></div></div>
        <div class="col-12 col-lg-3">
          <div class="card h-100"><div class="card-header fw-semibold">Recently Added</div>
            <ul class="list-group list-group-flush">
              @forelse(($recentlyAdded ?? collect()) as $ra)
              <li class="list-group-item">{{ $ra->name }} <span class="text-muted small">{{ $ra->created_at->diffForHumans() }}</span></li>
              @empty <li class="list-group-item text-muted small">No recent items</li> @endforelse
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="tab-pane fade" id="saTabCatalog">
      <div class="card h-100">
        <div class="card-header fw-semibold d-flex align-items-center">
          <span class="me-2">Catalog</span>
          <span class="text-muted small">({{ $medicines->total() }})</span>
        </div>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead><tr><th>Name</th><th>Form</th><th>Strength</th><th>Unit</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
              @forelse($medicines as $m)
              <tr>
                <td class="fw-semibold">{{ $m->name }}</td>
                <td>{{ $m->form }}</td>
                <td>{{ $m->strength }}</td>
                <td>{{ $m->unit }}</td>
                <td class="text-end"><button class="btn btn-sm btn-outline-primary" data-action="check-availability" data-id="{{ $m->id }}"><i class="bi bi-geo"></i> Availability</button></td>
              </tr>
              @empty
              <tr><td colspan="5" class="text-center text-muted py-4">No medicines found.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="card-footer">{{ $medicines->withQueryString()->links() }}</div>
      </div>
    </div>

    <div class="tab-pane fade" id="saTabUsage">
      <div class="card">
        <div class="card-header d-flex align-items-center gap-2">
          <div class="fw-semibold">Usage</div>
          <div class="ms-auto d-flex gap-2">
            <input type="text" id="usageSearchSA" class="form-control form-control-sm" placeholder="Search medicine or inmate">
            <select id="usageDaysSA" class="form-select form-select-sm" style="width:auto"><option value="7">7d</option><option value="30" selected>30d</option><option value="90">90d</option></select>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-sm mb-0 table-hover" id="usageTableSA" style="min-width:720px">
            <thead><tr><th>Medicine</th><th>Institution</th><th>Inmate</th><th>Doctor</th><th>Status</th><th>Start</th><th>End</th></tr></thead>
            <tbody><tr><td colspan="7" class="text-center text-muted py-4">Loading…</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="tab-pane fade" id="saTabLowStock">
      <div class="card">
        <div class="card-header fw-semibold">Low Stock (by institution)</div>
        <div class="table-responsive">
          <table class="table table-sm mb-0" id="lowStockTableSA">
            <thead><tr><th>Institution</th><th>Medicine</th><th>Qty</th><th>Min</th></tr></thead>
            <tbody><tr><td colspan="4" class="text-center text-muted py-4">Loading…</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="tab-pane fade" id="saTabHistory">
      <div class="card">
        <div class="card-header d-flex align-items-center gap-2">
          <div class="fw-semibold">History</div>
          <div class="ms-auto"><select id="historyDaysSA" class="form-select form-select-sm" style="width:auto"><option value="7">7d</option><option value="30" selected>30d</option><option value="90">90d</option></select></div>
        </div>
        <div class="table-responsive">
          <table class="table table-sm mb-0" id="historyTableSA">
            <thead><tr><th>Time</th><th>Type</th><th>Medicine</th><th>Details</th></tr></thead>
            <tbody><tr><td colspan="4" class="text-center text-muted py-4">Loading…</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="tab-pane fade" id="saTabReports">
      <div class="card">
        <div class="card-header fw-semibold">Reports</div>
        <div class="card-body d-flex flex-wrap gap-2">
          <a class="btn btn-outline-secondary" href="{{ route('system_admin.medicines.reports.stock') }}">Download Stock (CSV)</a>
          <a class="btn btn-outline-secondary" href="{{ route('system_admin.medicines.reports.prescriptions') }}">Download Prescriptions (CSV)</a>
          <a class="btn btn-outline-secondary" href="{{ route('system_admin.medicines.reports.usage-trends') }}">Download Usage Trends (CSV)</a>
        </div>
      </div>
      <div class="card mt-3">
        <div class="card-header d-flex align-items-center gap-2">
          <div class="fw-semibold text-warning"><span class="bi bi-exclamation-triangle me-1"></span>Not in Catalog</div>
          <div class="ms-auto"><select id="uncatDaysSA" class="form-select form-select-sm" style="width:auto"><option value="30">30d</option><option value="90" selected>90d</option></select></div>
        </div>
        <div class="table-responsive">
          <table class="table table-sm mb-0" id="uncatTableSA">
            <thead><tr><th>Medicine Name (free text)</th><th>Count</th><th class="text-end">Actions</th></tr></thead>
            <tbody><tr><td colspan="3" class="text-center text-muted py-4">Loading…</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="addCatalogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form class="modal-content" id="saAddCatalogForm" action="{{ route('system_admin.medicines.store') }}" method="POST">@csrf
        <div class="modal-header"><h5 class="modal-title">Add Medicine to Catalog</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="row g-2">
            <div class="col-12"><label class="form-label small">Name</label><input name="name" class="form-control" required></div>
            <div class="col-6"><label class="form-label small">Form</label><input name="form" class="form-control" placeholder="tablet"></div>
            <div class="col-3"><label class="form-label small">Strength</label><input name="strength" class="form-control" placeholder="500"></div>
            <div class="col-3"><label class="form-label small">Unit</label><input name="unit" class="form-control" placeholder="mg"></div>
          </div>
        </div>
        <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button><button class="btn btn-primary" type="submit">Save</button></div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="availabilityModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Availability</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="list-group" id="availabilityList"></div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
window.initSystemAdminMedicinesIndex = function(host){
  const modal = new bootstrap.Modal(document.getElementById('availabilityModal'));
  const availList = document.getElementById('availabilityList');
  const usageTBody = document.querySelector('#usageTableSA tbody');
  const lowTBody = document.querySelector('#lowStockTableSA tbody');
  const uncatTBody = document.querySelector('#uncatTableSA tbody');
  const histTBody = document.querySelector('#historyTableSA tbody');

  host.querySelectorAll('[data-action="check-availability"]').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const id = btn.getAttribute('data-id');
      availList.innerHTML = '<div class="p-3 text-muted small">Loading…</div>';
  fetch('{{ route('system_admin.medicines.availability') }}?medicine_id='+id).then(r=>r.json()).then(items=>{
        availList.innerHTML='';
        if(!items.length){
          availList.innerHTML = '<div class="p-3 text-muted small">No institutions currently hold stock for this medicine.</div>';
        } else {
          for(const it of items){
            const li = document.createElement('div');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.innerHTML = `<span>${it.institution.name}</span><span class="badge bg-light text-dark">${it.quantity}</span>`;
            availList.appendChild(li);
          }
        }
        modal.show();
      }).catch(()=>{
        availList.innerHTML = '<div class="p-3 text-danger small">Failed to load.</div>';
        modal.show();
      });
    });
  });

  function loadLow(){
    if(!lowTBody) return;
    lowTBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">Loading…</td></tr>';
    fetch('{{ route('system_admin.medicines.low-stock') }}', {headers:{'Accept':'application/json'}})
      .then(r=>r.json()).then(rows=>{
        lowTBody.innerHTML = '';
        if(!rows.length){ lowTBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">No low stock</td></tr>'; return; }
        for(const r of rows){
          const tr = document.createElement('tr');
          tr.innerHTML = `<td>${r.institution}</td><td class="fw-semibold">${r.name}</td><td>${r.quantity}</td><td>${r.threshold}</td>`;
          lowTBody.appendChild(tr);
        }
      }).catch(()=>{ lowTBody.innerHTML = '<tr><td colspan="4" class="text-center text-danger py-4">Failed</td></tr>'; });
  }
  loadLow();
  function loadUsage(){
    if(!usageTBody) return;
    const q = document.getElementById('usageSearchSA')?.value || '';
    const days = document.getElementById('usageDaysSA')?.value || 30;
    usageTBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Loading…</td></tr>';
    fetch('{{ route('system_admin.medicines.usage') }}?q='+encodeURIComponent(q)+'&days='+days,{headers:{'Accept':'application/json'}})
      .then(r=>r.json()).then(rows=>{
        usageTBody.innerHTML = '';
        if(!rows.length){ usageTBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No data</td></tr>'; return; }
        for(const r of rows){
          const tr = document.createElement('tr');
          tr.innerHTML = `<td class="fw-semibold">${r.name}</td>
            <td>${r.inmate?.institution || '—'}</td>
            <td>${r.inmate?.name || '—'}</td>
            <td>${r.doctor || '—'}</td>
            <td><span class="badge text-bg-light">${r.status||''}</span></td>
            <td>${r.start_date||'—'}</td>
            <td>${r.end_date||'—'}</td>`;
          usageTBody.appendChild(tr);
        }
      }).catch(()=>{ usageTBody.innerHTML = '<tr><td colspan="7" class="text-center text-danger py-4">Failed to load</td></tr>'; });
  }
  document.getElementById('usageSearchSA')?.addEventListener('input', ()=>{ clearTimeout(window.__usageDebounceSA); window.__usageDebounceSA=setTimeout(loadUsage, 350); });
  document.getElementById('usageDaysSA')?.addEventListener('change', loadUsage);
  loadUsage();

  function loadUncat(){
    if(!uncatTBody) return;
    const days = document.getElementById('uncatDaysSA')?.value || 90;
    uncatTBody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-4">Loading…</td></tr>';
    fetch('{{ route('system_admin.medicines.uncatalogued') }}?days='+days, {headers:{'Accept':'application/json'}})
      .then(r=>r.json()).then(rows=>{
        uncatTBody.innerHTML = '';
        if(!rows.length){ uncatTBody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-4">None</td></tr>'; return; }
        for(const r of rows){
          const tr = document.createElement('tr');
          tr.className = 'table-warning';
          tr.innerHTML = `<td class="fw-semibold"><span class="bi bi-exclamation-triangle me-1"></span>${r.name}</td>
            <td>${r.cnt}</td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-primary" data-action="add-catalog-sa" data-name="${encodeURIComponent(r.name)}">Add to Catalog</button>
              <button class="btn btn-sm btn-outline-secondary ms-2" data-action="assignees-sa" data-name="${encodeURIComponent(r.name)}">Assignees</button>
            </td>`;
          uncatTBody.appendChild(tr);
        }
      }).catch(()=>{ uncatTBody.innerHTML = '<tr><td colspan="3" class="text-center text-danger py-4">Failed</td></tr>'; });
  }
  document.getElementById('uncatDaysSA')?.addEventListener('change', loadUncat);
  loadUncat();

  function loadHistory(){
    if(!histTBody) return;
    const days = document.getElementById('historyDaysSA')?.value || 30;
    histTBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">Loading…</td></tr>';
    fetch('{{ route('system_admin.medicines.history') }}?days='+days, {headers:{'Accept':'application/json'}})
      .then(r=>r.json()).then(rows=>{
        histTBody.innerHTML = '';
        if(!rows.length){ histTBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">No history</td></tr>'; return; }
        for(const r of rows){
          const tr = document.createElement('tr');
          tr.innerHTML = `<td>${r.at}</td><td>${r.type}</td><td>${r.name||'—'}</td><td class="small text-muted">${r.meta?.institution||''} ${r.meta?.inmate?(' - '+r.meta.inmate):''} ${r.meta?.status?('('+r.meta.status+')'):''} ${r.meta?.quantity?('qty '+r.meta.quantity):''}</td>`;
          histTBody.appendChild(tr);
        }
      }).catch(()=>{ histTBody.innerHTML = '<tr><td colspan="4" class="text-center text-danger py-4">Failed</td></tr>'; });
  }
  document.getElementById('historyDaysSA')?.addEventListener('change', loadHistory);
  loadHistory();

  host.addEventListener('click', function(e){
    const btnA = e.target.closest('[data-action="assignees-sa"]');
    if(btnA){
      const nm = decodeURIComponent(btnA.getAttribute('data-name'));
      const listBody = document.querySelector('#assigneesTableSA tbody');
      listBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Loading…</td></tr>';
      new bootstrap.Modal(document.getElementById('assigneesModalSA')).show();
      fetch('{{ route('system_admin.medicines.assignees') }}?name='+encodeURIComponent(nm),{headers:{'Accept':'application/json'}})
        .then(r=>r.json()).then(rows=>{
          listBody.innerHTML = '';
          if(!rows.length){ listBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">None</td></tr>'; return; }
          for(const r of rows){
            const tr = document.createElement('tr');
            tr.innerHTML = `<td>${r.inmate}</td><td>${r.institution||'—'}</td><td>${r.doctor||'—'}</td><td>${r.status||'—'}</td><td>${r.start_date||'—'}${r.end_date?(' → '+r.end_date):''}</td>`;
            listBody.appendChild(tr);
          }
        }).catch(()=>{ listBody.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4">Failed</td></tr>'; });
    }
  });

  document.getElementById('saAddCatalogForm')?.addEventListener('submit', function(e){
    e.preventDefault();
    fetch(this.action, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body: new FormData(this)})
      .then(r=>r.json()).then(res=>{ if(res?.ok){ toastr.success('Added to catalog'); location.reload(); } else { toastr.error('Failed'); } })
      .catch(()=>toastr.error('Failed'));
  });
};

(function boot(){
  const host = document.querySelector('[data-host="system-admin-medicines-index"]');
  if(!host){ return; }
  if(window.initSystemAdminMedicinesIndex){ window.initSystemAdminMedicinesIndex(host); }
})();
</script>

<!-- Assignees Modal (System Admin) -->
<div class="modal fade" id="assigneesModalSA" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Assignees</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-sm align-middle" id="assigneesTableSA">
            <thead><tr><th>Inmate</th><th>Institution</th><th>Doctor</th><th>Status</th><th>Duration</th></tr></thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endpush
