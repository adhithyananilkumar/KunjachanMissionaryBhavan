<x-app-layout>
  <x-slot name="header"><h2 class="h5 mb-0">Doctor Profile & Schedule</h2></x-slot>

  <div class="d-flex align-items-center gap-3 mb-3">
    <img src="{{ $doctor->avatar_url }}" alt="avatar" class="rounded-circle" style="width:64px;height:64px;object-fit:cover;">
    <div class="flex-grow-1">
      <div class="h5 mb-1 d-flex align-items-center gap-2 flex-wrap">
        <span>{{ $doctor->name }}</span>
        <span class="badge rounded-pill bg-light border text-dark" style="font-size:.7rem"><span class="bi bi-stethoscope me-1"></span>Doctor</span>
      </div>
      <div class="text-muted small">{{ $doctor->email }}</div>
    </div>
    <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
  </div>

  @push('styles')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">
  <style>#calendar{min-height:600px;}</style>
  @endpush

  <ul class="nav nav-pills small mb-3" id="docTabs" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tabProfile" type="button"><span class="bi bi-person-badge me-1"></span>Profile</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabSchedule" type="button"><span class="bi bi-calendar3 me-1"></span>Schedule</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabAssign" type="button"><span class="bi bi-people me-1"></span>Assign Inmates</button></li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade show active" id="tabProfile">
      <div class="row g-3">
        <div class="col-12 col-lg-7">
          <div class="card"><div class="card-body small">
            <dl class="row mb-0">
              <dt class="col-4 col-md-3">Name</dt><dd class="col-8 col-md-9">{{ $doctor->name }}</dd>
              <dt class="col-4 col-md-3">Email</dt><dd class="col-8 col-md-9">{{ $doctor->email }}</dd>
            </dl>
          </div></div>
        </div>
        <div class="col-12 col-lg-5">
          <div class="card h-100">
            <div class="card-header py-2"><span class="bi bi-activity me-2"></span>Recent Activity</div>
            <div class="card-body">
              @if(!empty($activities))
                <ul class="list-unstyled small mb-0">
                  @foreach($activities as $act)
                    <li class="mb-2 d-flex align-items-start gap-2">
                      <span class="bi bi-{{ $act['icon'] ?? 'dot' }} mt-1 text-secondary"></span>
                      <div>
                        <div>{{ $act['text'] }}</div>
                        <div class="text-muted">{{ \Carbon\Carbon::parse($act['at'])->diffForHumans() }}</div>
                      </div>
                    </li>
                  @endforeach
                </ul>
              @else
                <div class="text-muted small">No recent activity.</div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="tab-pane fade" id="tabSchedule">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div class="btn-group btn-group-sm" role="group">
            <button class="btn btn-outline-secondary active" id="viewCalBtn">Calendar</button>
            <button class="btn btn-outline-secondary" id="viewListBtn">List</button>
          </div>
          <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#emergencyModal"><span class="bi bi-lightning-charge me-1"></span>Emergency</button>
        </div>
        <div class="card-body">
          <div id="calendar"></div>
          <div id="scheduleList" class="d-none">
            <ul class="list-group small" id="scheduleItems"></ul>
          </div>
          <div id="calendarLoading" class="text-muted small mt-2 d-none">Loading calendar…</div>
        </div>
      </div>
    </div>
    <div class="tab-pane fade" id="tabAssign">
      <div class="card">
        <div class="card-header">Assign Inmates</div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.doctors.assignments',$doctor) }}" id="assignForm">@csrf
            <div class="mb-2 d-flex align-items-center gap-2">
              <input type="text" class="form-control form-control-sm" id="searchInmates" placeholder="Search by name...">
            </div>
            <div class="border rounded overflow-auto mb-2" style="max-height: 380px;">
              <ul class="list-group list-group-flush" id="assignList">
                @foreach($inmates as $itm)
                  <li class="list-group-item d-flex align-items-center justify-content-between">
                    <label class="d-flex align-items-center gap-2 m-0 flex-grow-1" style="cursor:pointer">
                      <input type="checkbox" class="form-check-input inmate-checkbox" name="inmate_ids[]" value="{{ $itm->id }}" @checked(in_array($itm->id,$assignedIds))>
                      <span>{{ $itm->full_name }}</span>
                    </label>
                    @if(in_array($itm->id,$assignedIds))<span class="badge bg-primary-subtle border text-primary small">Assigned</span>@endif
                  </li>
                @endforeach
              </ul>
            </div>
            <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
              <div class="small text-muted" id="selectedCount">Selected: {{ count($assignedIds) }}</div>
              <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-outline-secondary" id="selectAllFiltered">Select all filtered</button>
                <button type="button" class="btn btn-outline-secondary" id="clearAll">Clear</button>
              </div>
              <button class="btn btn-sm btn-primary" id="assignSaveBtn">Save</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="emergencyModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Schedule Emergency Appointment</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="{{ route('admin.doctors.emergency',$doctor) }}" id="emergencyForm">@csrf
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Patient (Inmate)</label>
              <select name="inmate_id" class="form-select" required>
                @foreach(\App\Models\Inmate::where('institution_id', auth()->user()->institution_id)
                    ->when(optional($doctor->institution)->doctor_assignment_enabled, function($q) use ($doctor){
                      $q->where(function($qq) use ($doctor){
                        $qq->where('doctor_id',$doctor->id)
                           ->orWhereExists(function($sub) use ($doctor){
                              $sub->selectRaw('1')->from('doctor_inmate as di')
                                  ->whereColumn('di.inmate_id','inmates.id')
                                  ->where('di.doctor_id',$doctor->id);
                           });
                      });
                    })
                    ->orderBy('first_name')->orderBy('last_name')->get() as $inmate)
                  <option value="{{ $inmate->id }}">{{ $inmate->full_name }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Date</label>
              <input type="date" name="scheduled_for" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Reason</label>
              <textarea name="reason" class="form-control" rows="3" placeholder="Describe briefly why this is urgent" required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
            <button class="btn btn-danger" type="button" id="emergencySubmitBtn">Schedule</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  @push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
  <script>
  document.addEventListener('DOMContentLoaded', function(){
    // Prevent Enter from submitting emergency form by accident
    const emModal = document.getElementById('emergencyModal');
    const emBtn = document.getElementById('emergencySubmitBtn');
    const emForm = document.getElementById('emergencyForm');
    emModal?.addEventListener('keydown', function(ev){ if(ev.key==='Enter'){ ev.preventDefault(); }});
    emBtn?.addEventListener('click', function(){ emForm?.submit(); });

    // Assign list filtering and selection helpers
    const search = document.getElementById('searchInmates');
    const assignList = document.getElementById('assignList');
    const selectedCount = document.getElementById('selectedCount');
    function updateSelected(){ if(selectedCount){ selectedCount.textContent = 'Selected: ' + (document.querySelectorAll('.inmate-checkbox:checked').length); } }
    assignList?.addEventListener('change', updateSelected);
    search?.addEventListener('input', function(){
      const term = this.value.toLowerCase();
      assignList.querySelectorAll('li').forEach(item=>{
        const txt = item.textContent.toLowerCase();
        item.style.display = txt.includes(term)? '' : 'none';
      });
    });
    updateSelected();
    document.getElementById('selectAllFiltered')?.addEventListener('click', ()=>{
      const term = (search.value||'').toLowerCase();
      assignList.querySelectorAll('li').forEach(item=>{
        const txt = item.textContent.toLowerCase(); const cb=item.querySelector('input[type=checkbox]');
        if(txt.includes(term) && cb){ cb.checked = true; }
      });
      updateSelected();
    });
    document.getElementById('clearAll')?.addEventListener('click', ()=>{
      assignList.querySelectorAll('input[type=checkbox]').forEach(cb=> cb.checked=false);
      updateSelected();
    });

    // Calendar render with fallback to list view
    const calendarEl = document.getElementById('calendar');
    const listEl = document.getElementById('scheduleList');
    const listUl = document.getElementById('scheduleItems');
    const calendarLoading = document.getElementById('calendarLoading');
    let calendar; let calendarRendered = false; let tries = 0;
    function renderCalendarSafely(){
      if(calendarRendered && calendar){ calendar.updateSize(); calendar.refetchEvents(); return; }
      calendarLoading?.classList.remove('d-none');
      if(typeof FullCalendar === 'undefined' || !calendarEl){
        tries++; if(tries>40){
          calendarEl.classList.add('d-none'); listEl.classList.remove('d-none');
          calendarLoading?.classList.add('d-none'); loadList();
          if(window.toastr) toastr.info('Calendar library not available, showing list view.');
          return;
        }
        return setTimeout(renderCalendarSafely, 150);
      }
      calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: { url: '{{ route('admin.doctors.feed',$doctor) }}' },
        eventDidMount(info){
          const status = info.event.extendedProps.status;
          if(info.event.extendedProps.is_emergency){ info.el.style.background='#dc3545'; info.el.style.border='none'; info.el.style.color='#fff'; }
          else if(status==='completed'){ info.el.style.background='#198754'; info.el.style.border='none'; info.el.style.color='#fff'; }
        }
      });
      calendar.render(); calendarRendered = true; calendarLoading?.classList.add('d-none');
    }
    function loadList(){
      fetch('{{ route('admin.doctors.feed',$doctor) }}', {headers:{'Accept':'application/json'}})
        .then(r=>r.json()).then(items=>{
          listUl.innerHTML='';
          items.sort((a,b)=> new Date(a.start) - new Date(b.start));
          if(!items.length){
            const empty=document.createElement('li'); empty.className='list-group-item text-muted'; empty.textContent='No scheduled items yet.'; listUl.appendChild(empty); return;
          }
          items.forEach(ev=>{
            const li=document.createElement('li'); li.className='list-group-item d-flex justify-content-between align-items-center';
            const dt=new Date(ev.start);
            li.innerHTML = `<span><span class="bi bi-calendar-event me-2"></span>${dt.toLocaleString()}<span class="text-muted ms-2">${ev.title}</span></span>` + (ev.extendedProps?.is_emergency ? '<span class="badge bg-danger">Emergency</span>' : '');
            listUl.appendChild(li);
          });
        });
    }
    document.getElementById('viewCalBtn')?.addEventListener('click', function(){ this.classList.add('active'); document.getElementById('viewListBtn').classList.remove('active'); calendarEl.classList.remove('d-none'); listEl.classList.add('d-none'); renderCalendarSafely(); });
    document.getElementById('viewListBtn')?.addEventListener('click', function(){ this.classList.add('active'); document.getElementById('viewCalBtn').classList.remove('active'); calendarEl.classList.add('d-none'); listEl.classList.remove('d-none'); loadList(); });
    const docTabs = document.getElementById('docTabs');
    docTabs?.addEventListener('shown.bs.tab', function (event) {
      const target = event.target?.getAttribute('data-bs-target');
      if (target) { history.replaceState(null, '', target); }
      if (target === '#tabSchedule') { renderCalendarSafely(); }
    });
    const scheduleBtn = document.querySelector('[data-bs-target="#tabSchedule"]');
    scheduleBtn?.addEventListener('click', function(){ setTimeout(renderCalendarSafely, 50); setTimeout(()=>{ if(!calendarRendered){ document.getElementById('viewListBtn')?.click(); } }, 800); });
    const schedulePane = document.getElementById('tabSchedule'); if (schedulePane && schedulePane.classList.contains('show')) { renderCalendarSafely(); }
    const initialHash = location.hash; if (initialHash) { const hashBtn = document.querySelector(`[data-bs-target="${initialHash}"]`); if (hashBtn) { hashBtn.click(); } }

    // AJAX assignment save
    const assignForm = document.getElementById('assignForm');
    assignForm?.addEventListener('submit', async function(e){
      e.preventDefault();
      const btn = document.getElementById('assignSaveBtn'); btn?.setAttribute('disabled','disabled');
      try {
        const formData = new FormData(assignForm);
        const res = await fetch(assignForm.action, { method:'POST', headers:{ 'Accept':'application/json', 'X-CSRF-TOKEN': formData.get('_token') }, body: formData });
        const data = await res.json();
        if(data.ok){ if(window.toastr){ toastr.success('Assignments updated'); } else { alert('Assignments updated'); } }
        else { throw new Error('Save failed'); }
      } catch(err){ if(window.toastr){ toastr.error('Failed to save assignments'); } else { alert('Failed to save assignments'); } }
      finally { btn?.removeAttribute('disabled'); }
    });
  });
  </script>
  @endpush
</x-app-layout>
