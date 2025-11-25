@extends('layouts.app')
@section('title','Medicines — System Admin')
@section('content')
<div class="container py-3">
  <div class="d-flex align-items-center gap-2 mb-3">
    <h1 class="h4 mb-0">Medicines</h1>
    <div class="ms-auto d-flex align-items-center gap-2">
      <select id="instFilterSA" class="form-select form-select-sm" style="min-width:220px">
        <option value="">All institutions</option>
        @foreach(\App\Models\Institution::orderBy('name')->get() as $inst)
          <option value="{{ $inst->name }}">{{ $inst->name }}</option>
        @endforeach
      </select>
      <span class="small text-muted">Today: {{ now()->toDateString() }}</span>
    </div>
  </div>

  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-live-sa" role="tab">Live</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-logs-sa" role="tab">Logs</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-log-med-sa" role="tab">Log medication</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-reports-sa" role="tab">Reports</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-settings-sa" role="tab">Settings</a></li>
  </ul>
  <div class="tab-content border border-top-0 rounded-bottom p-2">
    <div class="tab-pane fade show active" id="tab-live-sa" role="tabpanel">
      <div class="card">
        <div class="card-header d-flex align-items-center gap-2 flex-wrap">
          <div class="fw-semibold">Live medications (all institutions)</div>
          <div class="ms-auto d-flex gap-2 align-items-center flex-wrap">
            <span class="badge text-bg-success">taken</span>
            <span class="badge text-bg-warning">due</span>
            <span class="badge text-bg-danger">missable</span>
            <span class="badge text-bg-light">waiting</span>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0 table-hover" id="liveTableSA" style="min-width:720px">
            <thead><tr><th>Institution</th><th>Patient</th><th>Medicine</th><th>Dosage</th><th>Route</th><th>Freq</th><th>Windows</th><th>Status</th><th>Action</th></tr></thead>
            <tbody><tr><td colspan="9" class="text-center text-muted py-4">Loading…</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="tab-pane fade" id="tab-logs-sa" role="tabpanel">
      <div class="d-flex align-items-center gap-2 mb-2">
        <div class="ms-auto"><select id="logsDaysSA" class="form-select form-select-sm" style="width:auto"><option value="1">1d</option><option value="7" selected>7d</option><option value="30">30d</option></select></div>
      </div>
      <div class="table-responsive">
        <table class="table table-sm align-middle mb-0" id="logsTableSA">
          <thead><tr><th>Time</th><th>Institution</th><th>Patient</th><th>Medicine</th><th>Status</th><th>By</th></tr></thead>
          <tbody><tr><td colspan="6" class="text-center text-muted py-4">Loading…</td></tr></tbody>
        </table>
      </div>
    </div>
    <div class="tab-pane fade" id="tab-log-med-sa" role="tabpanel">
  <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
        <div class="ms-auto" style="min-width:260px">
          <input type="search" id="logSearchSA" class="form-control form-control-sm" placeholder="Search institution/patient/medicine…">
        </div>
      </div>
      <div id="logEntryListSA" class="d-flex flex-column gap-2">
        <div class="text-center text-muted py-4">Loading…</div>
      </div>
    </div>
    <div class="tab-pane fade" id="tab-reports-sa" role="tabpanel">
      <div class="alert alert-info py-2">Download CSV reports:</div>
      <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('system_admin.medicines.reports.stock') }}">Stock</a>
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('system_admin.medicines.reports.prescriptions') }}">Prescriptions</a>
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('system_admin.medicines.reports.usage-trends') }}">Usage trends</a>
      </div>
    </div>
    <div class="tab-pane fade" id="tab-settings-sa" role="tabpanel">
      <div class="alert alert-secondary py-2">Medication time windows</div>
      <a class="btn btn-sm btn-primary" href="{{ route('system_admin.settings.medication-windows') }}">Open settings</a>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
  const liveTBody = document.querySelector('#liveTableSA tbody');
  const logsTBody = document.querySelector('#logsTableSA tbody');
  const logEntryList = document.getElementById('logEntryListSA');

  function badgeFor(status){ const m={taken:'success',due:'warning',missable:'danger',waiting:'light'}; return `<span class="badge text-bg-${m[status]||'secondary'}">${status}</span>`; }
  function ensureJson(r){ if(!r.ok) throw new Error(''+r.status); return r.json().catch(()=>({})); }

  function loadLive(){
    if(!liveTBody) return;
    liveTBody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">Loading…</td></tr>';
    fetch('{{ route('system_admin.medicines.live') }}?t='+Date.now(), {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}})
      .then(ensureJson)
      .then(rows=>{
        liveTBody.innerHTML='';
        const inst = (document.getElementById('instFilterSA')?.value||'').toLowerCase();
        const list = (Array.isArray(rows)?rows:[])
          .filter(r=> !inst || (r.institution||'').toLowerCase()===inst )
          .filter(r=> !r.loggedToday && (r.status||'').toLowerCase()!=='taken');
        if(list.length===0){ liveTBody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">None</td></tr>'; return; }
        for(const r of list){
          const tr=document.createElement('tr');
          const action = (String(r.status).toLowerCase()==='due') ? `<button type="button" class="btn btn-success btn-sm" data-med-id="${r.id}"><span class="bi bi-check2-circle me-1"></span>Taken</button>` : '—';
          tr.innerHTML = `<td>${r.institution||'—'}</td><td>${r.patient||'—'}</td><td class="fw-semibold text-truncate" style="max-width:180px">${r.name||''}</td><td>${r.dosage||'—'}</td><td>${r.route||'—'}</td><td>${r.frequency||'—'}</td><td class="text-truncate" style="max-width:140px">${(r.windows||[]).join(', ')}</td><td>${badgeFor((r.status||'').toLowerCase())}</td><td>${action}</td>`;
          liveTBody.appendChild(tr);
        }
      }).catch((e)=>{ liveTBody.innerHTML = '<tr><td colspan="9" class="text-center text-danger py-4">Failed to load</td></tr>'; console.error('sa live fetch failed', e); });
  }

  function loadLogs(){
    if(!logsTBody) return;
    const days = document.getElementById('logsDaysSA')?.value || 7;
    logsTBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Loading…</td></tr>';
    fetch('{{ route('system_admin.medicines.logs') }}?days='+days, {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}})
      .then(ensureJson)
      .then(rows=>{
        logsTBody.innerHTML = '';
        if(!Array.isArray(rows) || rows.length===0){ logsTBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No logs</td></tr>'; return; }
        for(const r of rows){
          const tr=document.createElement('tr');
          tr.innerHTML = `<td>${r.time||''}</td><td>${r.institution||'—'}</td><td>${r.patient||'—'}</td><td>${r.medicine||'—'}</td><td>${badgeFor((r.status||'').toLowerCase())}</td><td>${r.by||'—'}</td>`;
          logsTBody.appendChild(tr);
        }
      }).catch(()=>{ logsTBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">Failed</td></tr>'; });
  }

  function loadLogEntry(){
    if(!logEntryList) return;
    logEntryList.innerHTML = '<div class="text-center text-muted py-4">Loading…</div>';
    fetch('{{ route('system_admin.medicines.live') }}?t='+Date.now(), {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}})
      .then(ensureJson)
      .then(rows=>{
        logEntryList.innerHTML='';
        const inst = (document.getElementById('instFilterSA')?.value||'').toLowerCase();
        const list = (Array.isArray(rows) ? rows : [])
          .filter(r=> !inst || (r.institution||'').toLowerCase()===inst )
          .filter(r=> !r.loggedToday);
        let actionable = list.filter(r=> (String(r.status).toLowerCase()==='due') || (String(r.status).toLowerCase()==='missable'));
        if(!actionable.length){ actionable = list.filter(r=> (String(r.status).toLowerCase()==='waiting')); }
        const groups = new Map();
        for(const r of actionable){
          const key = `${r.institution||''}::${r.patient_id || r.patient || ''}`;
          if(!groups.has(key)) groups.set(key, { institution:r.institution, patient:r.patient, items:[] });
          groups.get(key).items.push(r);
        }
        if(!groups.size){ logEntryList.innerHTML = '<div class="text-center text-muted py-4">Nothing due now</div>'; return; }
        for(const [, g] of groups){
          const meds = g.items.sort((a,b)=> (a.name||'').localeCompare(b.name||''));
          const card=document.createElement('div');
          card.className='card';
          card.setAttribute('data-institution', (g.institution||'').toLowerCase());
          card.setAttribute('data-patient', (g.patient||'').toLowerCase());
          card.setAttribute('data-medicine', meds.map(m=>m.name||'').join(', ').toLowerCase());
          const options = meds.map(m=>{
            const details = [m.name,(m.dosage||'').trim(),(m.route||'').trim()].filter(Boolean).join(' ');
            const windows = Array.isArray(m.windows)?m.windows.join('/') : '';
            const right = [m.frequency||'—', windows?`[${windows}]`:'' ].filter(Boolean).join(' ');
            return `<option value="${m.id}">${details} — ${right}</option>`;
          }).join('');
          const size = Math.min(5, meds.length);
          card.innerHTML = `
            <div class="card-body d-flex flex-wrap gap-2 align-items-center">
              <div class="flex-grow-1">
                <div class="fw-semibold">${g.institution||'—'} • ${g.patient||'—'}</div>
                <div class="text-muted small">${meds.length} medication${meds.length>1?'s':''} due</div>
              </div>
              <select class="form-select form-select-sm flex-grow-1" data-med-select multiple size="${size}" style="min-width:260px">${options}</select>
              <div class="btn-group">
                <button type="button" class="btn btn-success btn-sm" data-status="taken"><span class="bi bi-check2-circle me-1"></span>Taken</button>
                <button type="button" class="btn btn-outline-danger btn-sm" data-status="missed"><span class="bi bi-x-circle me-1"></span>Missed</button>
              </div>
            </div>`;
          logEntryList.appendChild(card);
        }
      }).catch((e)=>{ logEntryList.innerHTML = '<div class="text-center text-danger py-4">Failed to load</div>'; console.error('sa logEntry fetch failed', e); });
  }

  // Log from Live table (single item, taken only)
  document.addEventListener('click', function(e){
    const btn = e.target.closest('#tab-live-sa button[data-med-id]');
    if(!btn) return;
    const medId = btn.getAttribute('data-med-id');
    const status = 'taken';
    fetch('{{ route('system_admin.medications.log') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body: new URLSearchParams({medication_id: medId, status})})
      .then(ensureJson)
      .then((resp)=>{
        btn.disabled=true; btn.classList.add('btn-secondary'); btn.textContent='Logged';
        const row = btn.closest('tr');
        const who = row?.querySelector('td:nth-child(2)')?.textContent || 'Patient';
        toastr[resp && resp.duplicate ? 'info' : 'success'](resp && resp.duplicate ? 'Already logged today' : 'Marked TAKEN', who);
        loadLive(); loadLogs(); loadLogEntry();
      })
      .catch((e)=>{ toastr.error('Failed to log medication ('+(e?.message||'error')+')','Error'); });
  });

  // Batch log from Log medication cards (multi-select)
  document.addEventListener('click', function(e){
    const btn = e.target.closest('#tab-log-med-sa .btn[data-status]');
    if(!btn) return;
    const status = btn.getAttribute('data-status') || 'taken';
    const card = btn.closest('.card');
    const sel = card?.querySelector('select[data-med-select]');
    const ids = sel ? Array.from(sel.selectedOptions).map(o=>o.value) : [];
    if(ids.length===0){ toastr.info('Select medications to log','Medication'); return; }
    btn.disabled=true; btn.classList.add('disabled');
    const postOne = (mid) => fetch('{{ route('system_admin.medications.log') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body: new URLSearchParams({medication_id: mid, status})}).then(ensureJson);
    ids.reduce((p, mid)=> p.then(acc=> postOne(mid).then(res=>{ acc.push(res); return acc; })), Promise.resolve([]))
      .then(results=>{
        const who = card?.querySelector('.fw-semibold')?.textContent || 'Patient';
        ids.forEach(id=>{ const opt = sel.querySelector(`option[value="${id}"]`); opt?.remove(); });
        if(!sel.querySelector('option')){ card.remove(); }
        const dupCount = results.filter(r=> r && r.duplicate).length;
        const okCount = results.length - dupCount;
        if(okCount>0){ toastr.success(`${okCount} marked ${status.toUpperCase()}`, who); }
        if(dupCount>0){ toastr.info(`${dupCount} already logged today`, who); }
        loadLive(); loadLogs(); loadLogEntry();
      })
      .catch((e)=>{ toastr.error('Failed to log medication ('+(e?.message||'error')+')','Error'); })
      .finally(()=>{ btn.disabled=false; btn.classList.remove('disabled'); });
  });

  document.getElementById('instFilterSA')?.addEventListener('change', ()=>{ loadLive(); loadLogEntry(); });
  const logSearch = document.getElementById('logSearchSA');
  logSearch?.addEventListener('input', ()=>{
    const s = (logSearch.value||'').toLowerCase();
    document.querySelectorAll('#logEntryListSA .card').forEach(card=>{
      const a=(card.getAttribute('data-institution')||'');
      const p=(card.getAttribute('data-patient')||'');
      const m=(card.getAttribute('data-medicine')||'');
      card.style.display = (!s || a.includes(s) || p.includes(s) || m.includes(s)) ? '' : 'none';
    });
  });
  document.getElementById('logsDaysSA')?.addEventListener('change', loadLogs);
  loadLive();
  loadLogs();
  loadLogEntry();
})();
</script>
@endpush
