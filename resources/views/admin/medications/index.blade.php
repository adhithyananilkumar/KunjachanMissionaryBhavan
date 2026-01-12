<x-app-layout>
<x-slot name="header"></x-slot>
<div class="container py-3" data-host="admin-medications-index">
  <div class="d-flex align-items-center gap-2 mb-3">
    <h1 class="h5 mb-0">Medicines</h1>
    <div class="ms-auto small text-muted">Today: {{ now()->toDateString() }}</div>
  </div>

  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-log-med" role="tab">Log medication</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-live" role="tab">Live</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-logs" role="tab">Logs</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-reports" role="tab">Reports</a></li>
  </ul>
  <div class="tab-content border border-top-0 rounded-bottom p-2">
    <div class="tab-pane fade" id="tab-live" role="tabpanel">
      <div class="table-responsive">
        <table class="table table-sm align-middle mb-0" id="liveTable">
          <thead><tr><th>Patient</th><th>Medicine</th><th>Dosage</th><th>Route</th><th>Freq</th><th>Windows</th><th>Status</th><th>Action</th></tr></thead>
          <tbody><tr><td colspan="8" class="text-center text-muted py-4">Loading…</td></tr></tbody>
        </table>
      </div>
    </div>
    <div class="tab-pane fade" id="tab-logs" role="tabpanel">
      <div class="d-flex align-items-center gap-2 mb-2">
        <div class="ms-auto"><select id="logsDays" class="form-select form-select-sm" style="width:auto"><option value="1">1d</option><option value="7" selected>7d</option><option value="30">30d</option></select></div>
      </div>
      <div class="table-responsive">
        <table class="table table-sm align-middle mb-0" id="logsTable">
          <thead><tr><th>Time</th><th>Patient</th><th>Medicine</th><th>Status</th><th>By</th></tr></thead>
          <tbody><tr><td colspan="5" class="text-center text-muted py-4">Loading…</td></tr></tbody>
        </table>
      </div>
    </div>
    <div class="tab-pane fade show active" id="tab-log-med" role="tabpanel">
      <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
        <div class="ms-auto" style="min-width:260px">
          <input type="search" id="logSearch" class="form-control form-control-sm" placeholder="Search patient/medicine…">
        </div>
      </div>
      <div id="logEntryList" class="d-flex flex-column gap-2">
        <div class="text-center text-muted py-4">Loading…</div>
      </div>
    </div>
    <div class="tab-pane fade" id="tab-reports" role="tabpanel">
      <div class="alert alert-info py-2">Download CSV reports:</div>
      <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.medicines.reports.stock') }}">Stock</a>
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.medicines.reports.prescriptions') }}">Prescriptions</a>
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.medicines.reports.usage-trends') }}">Usage trends</a>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
(function(){
  const init = function(){
    const liveTBody = document.querySelector('#liveTable tbody');
    const logsTBody = document.querySelector('#logsTable tbody');
    const logEntryList = document.getElementById('logEntryList');

    function badgeFor(status){ const map={taken:'success',due:'warning',missable:'danger',waiting:'light'}; return `<span class="badge text-bg-${map[status]||'secondary'}">${status}</span>`; }
    function ensureJson(r){ const ct=(r.headers.get('content-type')||'').toLowerCase(); if(!r.ok) throw new Error(''+r.status); if(!ct.includes('application/json')) throw new Error('Non-JSON'); return r.json(); }

    function loadLive(){
      if(!liveTBody) return;
      liveTBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">Loading…</td></tr>';
      const timeout = setTimeout(()=>{ if(liveTBody && liveTBody.textContent.includes('Loading')) liveTBody.innerHTML = '<tr><td colspan="8" class="text-center text-warning py-4">Still loading… check network</td></tr>'; }, 6000);
      fetch('{{ route('admin.medicines.live') }}?t='+Date.now(),{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}, credentials:'same-origin'})
        .then(ensureJson)
        .then(rows=>{
          clearTimeout(timeout);
          liveTBody.innerHTML='';
          // Exclude anything already logged today (taken or missed)
          const list = (Array.isArray(rows)?rows:[]).filter(r=> !r.loggedToday && (r.status||'').toLowerCase()!=='taken');
          if(list.length===0){ liveTBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">None</td></tr>'; return; }
          for(const r of list){
            const tr=document.createElement('tr');
            const action = (r.status==='due') ? `<button type="button" class="btn btn-success btn-sm" data-med-id="${r.id}"><span class="bi bi-check2-circle me-1"></span>Taken</button>` : '—';
            tr.innerHTML = `<td>${r.patient||'—'}</td><td class="fw-semibold">${r.name||''}</td><td>${r.dosage||'—'}</td><td>${r.route||'—'}</td><td>${r.frequency||'—'}</td><td>${(r.windows||[]).join(', ')}</td><td>${badgeFor(r.status||'')}</td><td>${action}</td>`;
            liveTBody.appendChild(tr);
          }
        }).catch((e)=>{ clearTimeout(timeout); liveTBody.innerHTML = `<tr><td colspan="8" class="text-center text-danger py-4">Failed to load (${(e&&e.message)||'error'})</td></tr>`; console.error('admin live fetch failed', e); });
    }

    function loadLogs(){
      if(!logsTBody) return;
      const days = document.getElementById('logsDays')?.value || 7;
      logsTBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Loading…</td></tr>';
      const timeout = setTimeout(()=>{ if(logsTBody && logsTBody.textContent.includes('Loading')) logsTBody.innerHTML = '<tr><td colspan="5" class="text-center text-warning py-4">Still loading… check network</td></tr>'; }, 6000);
      fetch('{{ route('admin.medicines.logs') }}?days='+days,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}, credentials:'same-origin'})
        .then(ensureJson)
        .then(rows=>{
          clearTimeout(timeout);
          logsTBody.innerHTML='';
          if(!Array.isArray(rows) || rows.length===0){ logsTBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No logs</td></tr>'; return; }
          for(const r of rows){
            const tr=document.createElement('tr');
            tr.innerHTML = `<td>${r.time||''}</td><td>${r.patient||'—'}</td><td>${r.medicine||'—'}</td><td>${badgeFor(r.status||'')}</td><td>${r.by||'—'}</td>`;
            logsTBody.appendChild(tr);
          }
        }).catch((e)=>{ clearTimeout(timeout); logsTBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger py-4">Failed to load (${(e&&e.message)||'error'})</td></tr>`; console.error('admin logs fetch failed', e); });
    }

    function loadLogEntry(){
      if(!logEntryList) return;
      logEntryList.innerHTML = '<div class="text-center text-muted py-4">Loading…</div>';
      const timeout = setTimeout(()=>{ if(logEntryList && logEntryList.textContent.includes('Loading')) logEntryList.innerHTML = '<div class="text-center text-warning py-4">Still loading… check network</div>'; }, 6000);
      fetch('{{ route('admin.medicines.live') }}?t='+Date.now(),{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}, credentials:'same-origin'})
        .then(ensureJson)
        .then(rows=>{
          clearTimeout(timeout);
          logEntryList.innerHTML='';
          const list = Array.isArray(rows) ? rows : [];
          // Only show items not logged today
          let actionable = list.filter(r=> !r.loggedToday && ((r.status||'').toLowerCase()==='due' || (r.status||'').toLowerCase()==='missable'));
          if(!actionable.length){ actionable = list.filter(r=> !r.loggedToday && (r.status||'').toLowerCase()==='waiting'); }
          const groups = new Map();
          for(const r of actionable){
            const pid = r.patient_id || r.patient || 'unknown';
            if(!groups.has(pid)) groups.set(pid, {patient:r.patient, items:[]});
            groups.get(pid).items.push(r);
          }
          if(groups.size===0){ logEntryList.innerHTML = '<div class="text-center text-muted py-4">Nothing due now</div>'; return; }
          for(const [pid, g] of groups){
            const meds = g.items.sort((a,b)=> (a.name||'').localeCompare(b.name||''));
            const first = meds[0];
            const card = document.createElement('div');
            card.className = 'card';
            card.setAttribute('data-patient', (g.patient||'').toLowerCase());
            card.setAttribute('data-medicine', meds.map(m=>m.name||'').join(', ').toLowerCase());
            const options = meds.map(m=>{
              const details = [m.name, (m.dosage||'').trim(), (m.route||'').trim()].filter(Boolean).join(' ');
              const windows = Array.isArray(m.windows)?m.windows.join('/') : '';
              const right = [m.frequency||'—', windows?`[${windows}]`:'' ].filter(Boolean).join(' ');
              return `<option value="${m.id}">${details} — ${right}</option>`;
            }).join('');
      const size = Math.min(5, meds.length);
      card.innerHTML = `
              <div class="card-body d-flex flex-wrap gap-2 align-items-center">
                <div class="flex-grow-1">
                  <div class="fw-semibold">${g.patient||'—'}</div>
                  <div class="text-muted small">${meds.length} medication${meds.length>1?'s':''} due</div>
                </div>
        <select class="form-select form-select-sm flex-grow-1" data-med-select multiple size="${size}" style="min-width:260px">${options}</select>
                <div class="btn-group">
                  <button type="button" class="btn btn-success btn-sm" data-med-id="${first.id}" data-status="taken"><span class="bi bi-check2-circle me-1"></span>Taken</button>
                  <button type="button" class="btn btn-outline-danger btn-sm" data-med-id="${first.id}" data-status="missed"><span class="bi bi-x-circle me-1"></span>Missed</button>
                </div>
              </div>`;
            logEntryList.appendChild(card);
          }
          logEntryList.querySelectorAll('select[data-med-select]').forEach(sel=>{
            sel.addEventListener('change', ()=>{
              const card = sel.closest('.card');
              const takenBtn = card.querySelector('button[data-status="taken"]');
              const missedBtn = card.querySelector('button[data-status="missed"]');
              takenBtn?.setAttribute('data-med-id', sel.value);
              missedBtn?.setAttribute('data-med-id', sel.value);
            });
          });
        }).catch((e)=>{ clearTimeout(timeout); logEntryList.innerHTML = `<div class="text-center text-danger py-4">Failed to load (${(e&&e.message)||'error'})</div>`; console.error('admin logEntry fetch failed', e); });
    }

    document.addEventListener('click', function(e){
      const btn = e.target.closest('button[data-med-id]');
      if(!btn) return;
      const status = btn.getAttribute('data-status') || 'taken';
      const card = btn.closest('.card');
      const sel = card?.querySelector('select[data-med-select]');
      const selected = sel ? Array.from(sel.selectedOptions).map(o=>({id:o.value, text:o.textContent})) : [];
      const ids = selected.length ? selected.map(s=>s.id) : [btn.getAttribute('data-med-id')];
      const texts = selected.length ? selected.map(s=>s.text) : [sel?.selectedOptions?.[0]?.textContent || ''];
      btn.disabled = true; btn.classList.add('disabled');
      const postOne = (mid) => fetch('{{ route('admin.medications.log') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, credentials:'same-origin', body: new URLSearchParams({medication_id: mid, status})}).then(r=>{ if(!r.ok) throw new Error(''+r.status); return r.json().catch(()=>({ok:true})); });
      // Process sequentially to keep order and avoid backend race on duplicates
      (ids.reduce((p, mid)=> p.then(acc=> postOne(mid).then(res=>{ acc.push({mid, res}); return acc; })), Promise.resolve([])))
        .then(results=>{
          const patientName = card?.querySelector('.fw-semibold')?.textContent || 'Patient';
          // Remove options for successfully posted meds
          if(sel){ ids.forEach(id=>{ const opt = sel.querySelector(`option[value="${id}"]`); opt?.remove(); }); if(!sel.querySelector('option')){ card.remove(); } }
          else { card && card.remove(); }
          // Compose toast
          const dupCount = results.filter(r=> r.res && r.res.duplicate).length;
          const okCount = results.length - dupCount;
          if(okCount>0){ toastr.success(`${okCount} marked ${status.toUpperCase()}`, patientName); }
          if(dupCount>0){ toastr.info(`${dupCount} already logged today`, patientName); }
          loadLive(); loadLogs(); loadLogEntry();
        })
        .catch((e)=>{ console.error('admin log post failed', e); toastr.error('Failed to log medication ('+(e?.message||'error')+')','Error'); })
        .finally(()=>{ btn.disabled=false; btn.classList.remove('disabled'); });
    });

    const logSearch = document.getElementById('logSearch');
    logSearch?.addEventListener('input', ()=>{
      const s = (logSearch.value||'').toLowerCase();
      document.querySelectorAll('#logEntryList .card').forEach(card=>{
        const p=(card.getAttribute('data-patient')||'');
        const m=(card.getAttribute('data-medicine')||'');
        card.style.display = (!s || p.includes(s) || m.includes(s)) ? '' : 'none';
      });
    });
    document.getElementById('logsDays')?.addEventListener('change', loadLogs);

    loadLive();
    loadLogs();
    loadLogEntry();
  };
  if(document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
</script>
@endpush
</x-app-layout>
