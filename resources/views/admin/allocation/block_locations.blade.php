<x-app-layout>
  <x-slot name="header"><h2 class="h5 mb-0">Manage Locations · {{ $block->name }} {{ $block->prefix ? '(' . $block->prefix . ')' : '' }}</h2></x-slot>
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  <div class="d-flex align-items-center mb-3">
    <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.blocks.index') }}">Back to Blocks</a>
    <button class="btn btn-sm btn-primary ms-auto" data-bs-toggle="modal" data-bs-target="#createLocation">Create New Location</button>
  </div>
  <div class="card">
    <div class="d-none d-md-block table-responsive">
      <table class="table table-striped mb-0 align-middle">
        <thead class="table-light"><tr><th>Type</th><th>No.</th><th>Display Name</th><th>Occupants</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
          @forelse($locations as $loc)
          @php $activeAssignments = $loc->assignments()->whereNull('end_date')->latest('start_date')->get(); @endphp
          <tr>
            <td class="text-capitalize">{{ $loc->type }}</td>
            <td>{{ $loc->number }}</td>
            <td>{{ $loc->name }}</td>
            <td>
              @if($activeAssignments->count())
                @foreach($activeAssignments as $as)
                  <a href="{{ route('admin.inmates.show', $as->inmate_id) }}" class="badge bg-secondary-subtle border text-secondary text-decoration-none me-1 mb-1">{{ $as->inmate?->full_name ?? ('Inmate #'.$as->inmate_id) }}</a>
                @endforeach
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
            <td>
              @php $occupiedNow = $activeAssignments->count() > 0; @endphp
              <span class="badge @class([
                'bg-danger' => $occupiedNow,
                'bg-warning text-dark' => !$occupiedNow && $loc->status==='maintenance',
                'bg-success' => !$occupiedNow && $loc->status!=='maintenance',
              ])">{{ $occupiedNow ? 'Occupied' : ($loc->status==='maintenance' ? 'Maintenance' : 'Available') }}</span>
            </td>
            <td class="text-end">
              @if($activeAssignments->count())
                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#transferModal" data-inmate="{{ $activeAssignments->first()->inmate_id }}" data-inmate-name="{{ $activeAssignments->first()->inmate?->full_name ?? ('Inmate #'.$activeAssignments->first()->inmate_id) }}" data-institution="{{ $loc->institution_id }}">Transfer</button>
              @elseif($loc->status!=='maintenance')
                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#allocateModal" data-location="{{ $loc->id }}" data-institution="{{ $loc->institution_id }}" data-name="{{ $loc->name }}">Allocate</button>
              @endif
              @if($loc->status==='maintenance')
                <form method="POST" action="{{ route('admin.locations.update', $loc) }}" class="d-inline ms-2" onsubmit="return confirm('Mark this location Available?')">
                  @csrf @method('PUT')
                  <input type="hidden" name="status" value="available">
                  <button class="btn btn-sm btn-outline-warning">Mark Available</button>
                </form>
              @else
                <form method="POST" action="{{ route('admin.locations.update', $loc) }}" class="d-inline ms-2" onsubmit="return confirm('Mark this location under Maintenance? Allocation will be disabled.')">
                  @csrf @method('PUT')
                  <input type="hidden" name="status" value="maintenance">
                  <button class="btn btn-sm btn-outline-warning" @disabled($occupiedNow)>Mark Maintenance</button>
                </form>
              @endif
              <form method="POST" action="{{ route('admin.locations.destroy', $loc) }}" class="d-inline ms-2" onsubmit="return confirm('Delete this location? This cannot be undone.')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="6" class="text-center py-4">No locations created yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <!-- Mobile cards -->
    <div class="d-md-none p-2">
      @forelse($locations as $loc)
        @php $activeAssignments = $loc->assignments()->whereNull('end_date')->latest('start_date')->get(); $occupiedNow = $activeAssignments->count() > 0; @endphp
        <div class="card mb-2">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="fw-semibold">{{ $loc->name }}</div>
                <div class="text-muted small">{{ ucfirst($loc->type) }} · #{{ $loc->number }}</div>
              </div>
              <span class="badge @class([
                'bg-danger' => $occupiedNow,
                'bg-warning text-dark' => !$occupiedNow && $loc->status==='maintenance',
                'bg-success' => !$occupiedNow && $loc->status!=='maintenance',
              ])">{{ $occupiedNow ? 'Occupied' : ($loc->status==='maintenance' ? 'Maintenance' : 'Available') }}</span>
            </div>
            <div class="mt-2">
              <div class="small text-muted mb-1">Occupants</div>
              @if($activeAssignments->count())
                <div class="d-flex flex-wrap gap-1">
                  @foreach($activeAssignments as $as)
                    <a href="{{ route('admin.inmates.show', $as->inmate_id) }}" class="badge bg-secondary-subtle border text-secondary text-decoration-none">{{ $as->inmate?->full_name ?? ('Inmate #'.$as->inmate_id) }}</a>
                  @endforeach
                </div>
              @else
                <div class="text-muted">—</div>
              @endif
            </div>
            <div class="mt-2 d-flex gap-2 flex-wrap">
              @if($activeAssignments->count())
                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#transferModal" data-inmate="{{ $activeAssignments->first()->inmate_id }}" data-inmate-name="{{ $activeAssignments->first()->inmate?->full_name ?? ('Inmate #'.$activeAssignments->first()->inmate_id) }}" data-institution="{{ $loc->institution_id }}">Transfer</button>
              @elseif($loc->status!=='maintenance')
                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#allocateModal" data-location="{{ $loc->id }}" data-institution="{{ $loc->institution_id }}" data-name="{{ $loc->name }}">Allocate</button>
              @endif
              @if($loc->status==='maintenance')
                <form method="POST" action="{{ route('admin.locations.update', $loc) }}" onsubmit="return confirm('Mark this location Available?')">
                  @csrf @method('PUT')
                  <input type="hidden" name="status" value="available">
                  <button class="btn btn-sm btn-outline-warning">Mark Available</button>
                </form>
              @else
                <form method="POST" action="{{ route('admin.locations.update', $loc) }}" onsubmit="return confirm('Mark this location under Maintenance? Allocation will be disabled.')">
                  @csrf @method('PUT')
                  <input type="hidden" name="status" value="maintenance">
                  <button class="btn btn-sm btn-outline-warning" @disabled($occupiedNow)>Mark Maintenance</button>
                </form>
              @endif
              <form method="POST" action="{{ route('admin.locations.destroy', $loc) }}" onsubmit="return confirm('Delete this location? This cannot be undone.')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
            </div>
          </div>
        </div>
      @empty
        <div class="text-center text-muted py-4">No locations created yet.</div>
      @endforelse
    </div>
    @if($locations->hasPages())<div class="card-footer">{{ $locations->links() }}</div>@endif
  </div>

  <div class="modal fade" id="createLocation" tabindex="-1">
    <div class="modal-dialog">
      <form class="modal-content" method="POST" action="{{ route('admin.blocks.locations.store',$block) }}">
        @csrf
        <div class="modal-header"><h5 class="modal-title">Create Location in {{ $block->name }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Type</label>
            <select name="type" class="form-select" required>
              <option value="room">Room</option>
              <option value="bed">Bed</option>
              <option value="cell">Cell</option>
            </select>
          </div>
          <div class="mb-3"><label class="form-label">Number</label><input name="number" class="form-control" placeholder="e.g., 102" required></div>
        </div>
        <div class="modal-footer"><button class="btn btn-primary">Create</button></div>
      </form>
    </div>
  </div>
  <div class="modal fade" id="allocateModal" tabindex="-1">
    <div class="modal-dialog">
      <form class="modal-content" id="allocateForm" method="POST">
        @csrf
        <div class="modal-header"><h5 class="modal-title">Allocate inmate to <span id="allocateLocName">location</span></h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-2 small text-muted">Search inmate</div>
          <input type="text" id="allocateSearch" class="form-control form-control-sm" placeholder="Type a name or reg. no...">
          <div id="allocateList" class="list-group small mt-2" style="max-height:260px; overflow:auto"></div>
          <input type="hidden" name="location_id" id="allocateLocationId">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="allocateSubmit" disabled>Allocate</button>
        </div>
      </form>
    </div>
  </div>
  <div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog">
  <form class="modal-content" id="transferForm" method="POST">
        @csrf
        <div class="modal-header"><h5 class="modal-title">Transfer occupant</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-2 small text-muted">Inmate</div>
          <div id="transferInmate" class="fw-semibold">—</div>
          <div class="mt-3">
            <div class="input-group input-group-sm">
              <input type="text" id="transferSearch" class="form-control" placeholder="Search rooms...">
              <button class="btn btn-outline-secondary" type="button" id="transferReload"><span class="bi bi-arrow-repeat"></span></button>
            </div>
            <div class="form-check form-switch mt-2 small">
              <input class="form-check-input" type="checkbox" id="transferShowOcc">
              <label class="form-check-label" for="transferShowOcc">Show occupied</label>
            </div>
          </div>
          <div id="transferList" class="list-group small mt-2" style="max-height:260px; overflow:auto"></div>
          <input type="hidden" name="location_id" id="transferLocationId">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="transferSubmit" disabled>Transfer</button>
        </div>
      </form>
    </div>
  </div>

  @push('scripts')
  <script>
    (function(){
      function showToast(type, msg){
        if(window.toastr){
          if(type==='success') toastr.success(msg); else toastr.error(msg);
        } else {
          alert(msg);
        }
      }
      function hideModal(modalEl){
        const inst = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        inst.hide();
      }
      const modal = document.getElementById('transferModal');
      const list = document.getElementById('transferList');
      const search = document.getElementById('transferSearch');
      const reload = document.getElementById('transferReload');
      const showOcc = document.getElementById('transferShowOcc');
      const inmateNameOut = document.getElementById('transferInmate');
  const form = document.getElementById('transferForm');
  const transferSubmit = document.getElementById('transferSubmit');
      let instId = null, inmateId = null, rooms = [];
  // Prevent Enter key from submitting transfer form unintentionally
  modal.addEventListener('keydown', function(ev){ if(ev.key==='Enter'){ ev.preventDefault(); } });
      modal.addEventListener('show.bs.modal', function(e){
        const btn = e.relatedTarget; if(!btn) return;
        inmateId = btn.getAttribute('data-inmate'); instId = btn.getAttribute('data-institution');
        inmateNameOut.textContent = btn.getAttribute('data-inmate-name') || ('Inmate #'+inmateId);
        form.action = `{{ url('admin/inmates') }}/${inmateId}/assign-location`;
        document.getElementById('transferLocationId').value = '';
        transferSubmit.disabled = true;
        load();
      });
      async function load(){
        const res = await fetch(`{{ url('admin/allocation/api/institutions') }}/${instId}/locations?show_occupied=${showOcc.checked?1:0}`, {headers:{'Accept':'application/json'}});
        const data = await res.json(); rooms = data.locations||[]; render();
      }
      function render(){
        const term = (search.value||'').toLowerCase(); list.innerHTML='';
        const items = rooms.filter(r=> r.name.toLowerCase().includes(term));
        if(items.length===0){ list.innerHTML = '<div class="text-center text-muted py-3">No rooms</div>'; return; }
        items.forEach(r=>{
          const btn = document.createElement('button'); btn.type='button'; btn.className='list-group-item list-group-item-action d-flex justify-content-between align-items-center';
          const isMaint = r.status === 'maintenance';
          const label = isMaint ? 'Maintenance' : (r.occupied ? 'Occupied' : 'Available');
          const badgeClass = isMaint ? 'bg-warning text-dark' : (r.occupied ? 'bg-secondary' : 'bg-success');
          btn.innerHTML = `<span><span class=\"bi bi-door-closed me-2\"></span>${r.name}${r.occupant ? ' <span class=\"badge bg-secondary ms-2\">'+r.occupant+'</span>' : ''}</span>` +
            `<span class=\"badge ${badgeClass}\">${label}</span>`;
          btn.disabled = isMaint || (r.occupied && !showOcc.checked);
          btn.addEventListener('click', ()=>{ document.getElementById('transferLocationId').value = r.id; document.querySelectorAll('#transferList .list-group-item').forEach(x=>x.classList.remove('active')); btn.classList.add('active'); transferSubmit.disabled = false; });
          list.appendChild(btn);
        });
      }
      search?.addEventListener('input', render);
      reload?.addEventListener('click', load);
      showOcc?.addEventListener('change', load);
      transferSubmit.addEventListener('click', async function(ev){
        ev.preventDefault();
        const locId = document.getElementById('transferLocationId').value;
        if(!locId){ showToast('error','Select a destination location.'); return; }
        const token = form.querySelector('input[name=_token]')?.value;
        try{
          const res = await fetch(form.action, {
            method: 'POST',
            headers: { 'Accept':'application/json','X-CSRF-TOKEN': token,'Content-Type':'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ location_id: locId })
          });
          const data = await res.json().catch(()=>({ ok:false, message:'Unexpected response' }));
          if(res.ok && data.ok){
            showToast('success', data.message || 'Location updated.');
            hideModal(modal);
            window.location.reload();
          } else {
            showToast('error', data.message || 'Failed to update location.');
          }
        }catch(e){ showToast('error','Network error.'); }
      });
      // Allocate modal (admin scope)
      const allocModal = document.getElementById('allocateModal');
      const allocList = document.getElementById('allocateList');
      const allocSearch = document.getElementById('allocateSearch');
      const allocForm = document.getElementById('allocateForm');
      const allocLocName = document.getElementById('allocateLocName');
      const allocSubmit = document.getElementById('allocateSubmit');
      let allocInstId = null, allocLocId = null, candidates = [], selectedInmate = null;
  // Prevent Enter key from submitting allocate form unintentionally
  allocModal.addEventListener('keydown', function(ev){ if(ev.key==='Enter'){ ev.preventDefault(); } });
  allocModal.addEventListener('show.bs.modal', function(e){
        const btn = e.relatedTarget; if(!btn) return;
        allocInstId = btn.getAttribute('data-institution');
        allocLocId = btn.getAttribute('data-location');
        allocLocName.textContent = btn.getAttribute('data-name') || 'location';
        document.getElementById('allocateLocationId').value = allocLocId;
        allocForm.action = `{{ url('admin/inmates') }}`; // will append /{id}/assign-location dynamically
        selectedInmate = null; allocSubmit.disabled = true; loadInmates('');
      });
      allocSearch?.addEventListener('input', ()=> loadInmates(allocSearch.value||''));
      async function loadInmates(term){
        const url = `{{ url('admin/allocation/api/institutions') }}/${allocInstId}/inmates?term=${encodeURIComponent(term)}`;
        const res = await fetch(url, {headers:{'Accept':'application/json'}});
        const data = await res.json(); candidates = data.inmates||[]; renderInmates();
      }
      function renderInmates(){
        allocList.innerHTML = '';
        if(candidates.length===0){ allocList.innerHTML = '<div class="text-center text-muted py-3">No results</div>'; return; }
        candidates.forEach(i=>{
          const btn = document.createElement('button'); btn.type='button'; btn.className='list-group-item list-group-item-action'; btn.textContent = i.name;
          btn.addEventListener('click', ()=>{ selectedInmate = i; allocSubmit.disabled = false; document.querySelectorAll('#allocateList .list-group-item').forEach(x=>x.classList.remove('active')); btn.classList.add('active'); });
          allocList.appendChild(btn);
        });
      }
      allocSubmit.addEventListener('click', async function(ev){
        ev.preventDefault();
        if(!selectedInmate || !allocLocId){ showToast('error','Select an inmate.'); return; }
        const token = allocForm.querySelector('input[name=_token]')?.value;
        const url = `{{ url('admin/inmates') }}/${selectedInmate.id}/assign-location`;
        try{
          const res = await fetch(url, {
            method: 'POST',
            headers: { 'Accept':'application/json','X-CSRF-TOKEN': token,'Content-Type':'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ location_id: allocLocId })
          });
          const data = await res.json().catch(()=>({ ok:false, message:'Unexpected response' }));
          if(res.ok && data.ok){
            showToast('success', data.message || 'Location updated.');
            hideModal(allocModal);
            window.location.reload();
          } else {
            showToast('error', data.message || 'Failed to allocate.');
          }
        }catch(e){ showToast('error','Network error.'); }
      });
    })();
  </script>
  @endpush
</x-app-layout>
