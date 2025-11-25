<x-app-layout>
  <x-slot name="header"><h2 class="h5 mb-0">Medications to Administer Now</h2></x-slot>
  <div class="card shadow-sm">
    <div class="card-header d-flex flex-wrap gap-2 align-items-center">
      <div class="small text-muted">Only showing items due in the current time window</div>
      <div class="ms-auto" style="min-width:220px">
        <input type="search" id="medSearch" class="form-control form-control-sm" placeholder="Search patient name or ID…">
      </div>
    </div>
  <div class="list-group list-group-flush" id="medList">
      @php $shownAny = false; @endphp
      @forelse($meds->groupBy('inmate_id') as $inmateId => $list)
        @php
          $patientName = trim(($list->first()->inmate?->full_name) ?? ('Patient #'+$inmateId));
          $dueItems = $list->filter(function($m) use ($states){ $st = $states[$m->id] ?? null; return $st && ($st['dueNow'] ?? false); });
        @endphp
        @if($dueItems->count() > 0)
        @php $shownAny = true; @endphp
        @php $options = $dueItems->filter(function($m) use ($states){ $st = $states[$m->id] ?? []; return empty($st['taken']); }); @endphp
        @if($options->count() > 0)
        <div class="list-group-item" data-patient="{{ Str::lower($patientName) }}" data-id="{{ $inmateId }}" data-medicine="{{ Str::lower($options->map(fn($m)=> $m->name)->implode(', ')) }}">
          <div class="d-flex flex-wrap align-items-center gap-2">
            <div class="flex-grow-1">
              <div class="fw-semibold">{{ $patientName }}</div>
              <small class="text-muted">ID: {{ $inmateId }}</small>
            </div>
            @php
              $size = min(5, $options->count());
            @endphp
            <select class="form-select form-select-sm flex-grow-1" data-med-select multiple size="{{ $size }}" style="min-width:260px">
              @foreach($options as $m)
                @php $windows = is_array($m->windows ?? null) ? implode('/', $m->windows) : ''; @endphp
                <option value="{{ $m->id }}">{{ trim($m->name.' '.$m->dosage.' '.$m->route) }} — {{ $m->frequency }} @if($windows)[{{ $windows }}]@endif</option>
              @endforeach
            </select>
            <div class="btn-group">
              <button type="button" class="btn btn-success btn-sm" data-action="take"><span class="bi bi-check2-circle me-1"></span>Taken</button>
            </div>
          </div>
        </div>
        @endif
        @endif
      @empty
        <div class="list-group-item text-center text-muted py-4">No medications today.</div>
      @endforelse
      @if(!$shownAny)
        <div class="list-group-item text-center text-muted py-4">Nothing due in this window.</div>
      @endif
    </div>
  </div>
  @push('scripts')
  <script>
    (function(){
      const q = document.getElementById('medSearch');
      const list = document.getElementById('medList');
      if(!q || !list) return;
      q.addEventListener('input', function(){
        const s = (q.value||'').trim().toLowerCase();
        const items = list.querySelectorAll('.list-group-item');
        items.forEach(it=>{
          const name = (it.getAttribute('data-patient')||'');
          const id = (it.getAttribute('data-id')||'');
          const meds = (it.getAttribute('data-medicine')||'');
          const show = !s || name.includes(s) || id.includes(s) || meds.includes(s);
          it.style.display = show ? '' : 'none';
        });
      });
      document.addEventListener('click', function(e){
        const btn = e.target.closest('#medList .btn[data-action="take"]');
        if(!btn) return;
        const item = btn.closest('.list-group-item');
        const sel = item?.querySelector('select[data-med-select]');
        const ids = sel ? Array.from(sel.selectedOptions).map(o=>o.value) : [];
        if(ids.length===0){ window.toastr?.info('Select medications to log','Medication'); return; }
        btn.disabled = true; btn.classList.add('disabled');
        const postOne = (mid) => fetch('{{ route('staff.meds.log') }}', { method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'text/html' }, body: new URLSearchParams({ medication_id: mid, status: 'taken' }) });
        ids.reduce((p, mid)=> p.then(async acc=>{ const r = await postOne(mid); acc.push(r.ok); return acc; }), Promise.resolve([]))
          .then(results=>{
            ids.forEach(id=>{ const opt = sel.querySelector(`option[value="${id}"]`); opt?.remove(); });
            if(!sel.querySelector('option')){ item.remove(); }
            const okCount = results.filter(Boolean).length;
            if(okCount>0){ window.toastr?.success(`${okCount} marked TAKEN`, item?.querySelector('.fw-semibold')?.textContent||'Patient'); }
          })
          .catch(e=>{ window.toastr?.error('Failed to log medications','Error'); })
          .finally(()=>{ btn.disabled=false; btn.classList.remove('disabled'); });
      });
    })();
  </script>
  @endpush
</x-app-layout>
