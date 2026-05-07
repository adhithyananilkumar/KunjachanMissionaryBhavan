<x-app-layout>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Appointments</h1>
    <div class="d-flex align-items-center gap-2">
      <div class="btn-group btn-group-sm" role="group">
          <button class="btn btn-outline-secondary" id="viewCalBtn" type="button">Calendar</button>
          <button class="btn btn-outline-secondary active" id="viewListBtn" type="button">List</button>
      </div>
      <button id="newAppointmentBtn" class="btn btn-primary btn-sm" type="button">New</button>
    </div>
  </div>

  @push('styles')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">
  <style>
    #calendar{min-height:600px;}
    .fc-event{cursor:pointer;}
  </style>
  @endpush

  <div class="card">
    <div class="card-body">
      <div id="calendar"></div>
      <div id="scheduleList" class="d-none">
          <ul class="nav nav-tabs small mb-2" id="listTabs" role="tablist">
            <li class="nav-item" role="presentation"><button class="nav-link active" type="button" data-target="upcoming">Upcoming</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" type="button" data-target="past">Past</button></li>
          </ul>
          <div class="small text-muted mb-2">Upcoming shows Today first, then Future dates.</div>
          <ul class="list-group small" id="scheduleItems"></ul>
      </div>
      <div id="calendarLoading" class="text-muted small mt-2 d-none">Loading calendar…</div>
    </div>
  </div>

  <div class="modal fade" id="appointmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="appointmentModalTitle">New Appointment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="appointmentForm">
            <input type="hidden" name="id" id="appointment_id">
            <div class="mb-3">
              <label class="form-label">Inmate</label>
              <select name="inmate_id" id="inmate_id" class="form-select" required>
                <option value="">Select Inmate</option>
                @foreach(\App\Models\Inmate::where('institution_id', auth()->user()->institution_id)->orderBy('last_name')->get() as $inmate)
                  <option value="{{$inmate->id}}">{{$inmate->full_name}}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Title</label>
              <input type="text" class="form-control" name="title" id="title" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Scheduled Date</label>
              <input type="date" class="form-control" name="scheduled_for" id="scheduled_for" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Notes</label>
              <textarea class="form-control" name="notes" id="notes" rows="3"></textarea>
            </div>
            <div class="mb-3 d-none" id="statusWrapper">
              <label class="form-label">Status</label>
              <select class="form-select" name="status" id="status">
                <option value="scheduled">Scheduled</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" id="saveAppointmentBtn" class="btn btn-primary">Save</button>
          <button type="button" id="deleteAppointmentBtn" class="btn btn-danger d-none">Delete</button>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
  <script>
    (function(){
      const calendarEl = document.getElementById('calendar');
      const listEl = document.getElementById('scheduleList');
      const listItemsEl = document.getElementById('scheduleItems');
      const listTabsEl = document.getElementById('listTabs');
      let currentListTab = 'upcoming';
      const viewCalBtn = document.getElementById('viewCalBtn');
      const viewListBtn = document.getElementById('viewListBtn');
      const modalEl = document.getElementById('appointmentModal');
      const modal = new bootstrap.Modal(modalEl);
      const form = document.getElementById('appointmentForm');
      const statusWrapper = document.getElementById('statusWrapper');
      const deleteBtn = document.getElementById('deleteAppointmentBtn');
      const saveBtn = document.getElementById('saveAppointmentBtn');
      const newBtn = document.getElementById('newAppointmentBtn');

      if(!window.toastr){
        window.toastr = { success:console.log, error:console.error, info:console.log, warning:console.warn };
        console.warn('Toastr not ready yet when appointments script ran. Using console fallback.');
      }

      function resetForm(){
        form?.reset();
        const hiddenId = document.getElementById('appointment_id');
        if(hiddenId) hiddenId.value='';
        statusWrapper.classList.add('d-none');
        deleteBtn.classList.add('d-none');
        document.getElementById('appointmentModalTitle').innerText='New Appointment';
      }

      newBtn?.addEventListener('click',()=>{ resetForm(); modal.show(); });

      function colorize(info){
        const status = info.event.extendedProps?.status || 'scheduled';
        let bg = '#0d6efd';
        if(status==='completed') bg = '#198754';
        else if(status==='cancelled') bg = '#6c757d';
        info.el.style.backgroundColor = bg;
        info.el.style.borderColor = bg;
        info.el.style.color = '#fff';
        info.el.style.padding='2px 4px';
        info.el.style.fontSize='.75rem';
      }

  const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView:'dayGridMonth',
        height: 'auto',
        editable: true,
        eventSources: [
          {
            url: '{{ route('doctor.appointments.feed') }}',
            method: 'GET',
            failure: ()=> console.error('Failed to load events')
          }
        ],
        eventDidMount: colorize,
        eventDrop(info){
          const payload = {
            scheduled_for: info.event.startStr.substring(0,10),
            inmate_id: info.event.extendedProps.inmate_id,
            title: info.event.title.split(' - ')[0],
            status: info.event.extendedProps.status,
            notes: info.event.extendedProps.notes || ''
          };
          fetch(`{{ url('doctor/appointments') }}/${info.event.id}`, {method:'PATCH', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json','Content-Type':'application/json'}, body: JSON.stringify(payload)})
            .then(r=>{ if(!r.ok) throw r; return r.json(); })
            .then(()=> toastr.success('Appointment updated successfully.'))
            .catch(()=> { toastr.error('Failed to reschedule'); info.revert(); });
        },
        eventClick(info){
          resetForm();
          document.getElementById('appointmentModalTitle').innerText='Edit Appointment';
          document.getElementById('appointment_id').value=info.event.id;
          document.getElementById('title').value=info.event.title.split(' - ')[0];
          document.getElementById('inmate_id').value=info.event.extendedProps.inmate_id;
          document.getElementById('notes').value=info.event.extendedProps.notes || '';
          document.getElementById('scheduled_for').value = info.event.startStr.substring(0,10);
          statusWrapper.classList.remove('d-none');
          document.getElementById('status').value=info.event.extendedProps.status;
          deleteBtn.classList.remove('d-none');
          modal.show();
        }
      });
      calendar.render();

      // Nudge FullCalendar after first render to correct initial sizing on some devices
      function nudgeCalendar(){
        // Defer to next paint so container sizing is settled
        setTimeout(()=>{ try { calendar.updateSize(); } catch(e) { /* noop */ } }, 100);
      }
      nudgeCalendar();

      function handleSuccess(message){
        toastr.success(message);
        try { modal.hide(); } catch(e) {}
        calendar.refetchEvents();
        if(!listEl.classList.contains('d-none')){ updateList(currentListTab); }
      }

      saveBtn.addEventListener('click', ()=>{
        const id = document.getElementById('appointment_id').value;
        const fd = new FormData(form);
        const payload = Object.fromEntries(fd.entries());
        const method = id? 'PATCH':'POST';
        const url = id? '{{ url('doctor/appointments') }}/'+id : '{{ route('doctor.appointments.store') }}';
        saveBtn.disabled=true; saveBtn.textContent='Saving...';
        fetch(url, {method, headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json','Content-Type':'application/json'}, body: JSON.stringify(payload)})
          .then(resp=>resp.json().then(data=>({ok:resp.ok, data})))
          .then(({ok,data})=>{
            if(!ok){
              let errorMessage = data.message || 'Save failed';
              if(data.errors){ errorMessage = Object.values(data.errors).flat().join(' '); }
              throw new Error(errorMessage);
            }
            handleSuccess(id? (data.message || 'Appointment updated successfully!') : (data.message || 'Appointment created'));
          })
          .catch(error=>{ toastr.error(error.message || 'Save failed'); })
          .finally(()=>{ saveBtn.disabled=false; saveBtn.textContent='Save'; });
      });

      deleteBtn.addEventListener('click',()=>{
        const id = document.getElementById('appointment_id').value; if(!id) return;
        if(!confirm('Delete this appointment?')) return;
        fetch('{{ url('doctor/appointments') }}/'+id, {method:'DELETE', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}})
          .then(r=>{ if(!r.ok) throw r; return r.json(); })
          .then(()=>{ handleSuccess('Appointment deleted.'); })
          .catch(()=> toastr.error('Delete failed'));
      });

      function updateList(tab = currentListTab){
        currentListTab = tab;
        const url = new URL('{{ route('doctor.appointments.feed') }}', window.location.origin);
        listItemsEl.innerHTML = '<li class="list-group-item text-muted">Loading…</li>';
        fetch(url.toString())
          .then(r=>r.json())
          .then(items=>{
            if(!Array.isArray(items) || items.length===0){
              listItemsEl.innerHTML = '<li class="list-group-item text-muted">No appointments.</li>';
              return;
            }
            const today = new Date().toISOString().slice(0,10);
            const todays = items.filter(i=>i.start===today).sort((a,b)=> a.title.localeCompare(b.title));
            const futures = items.filter(i=>i.start>today).sort((a,b)=> a.start.localeCompare(b.start));
            const past = items.filter(i=>i.start<today).sort((a,b)=> b.start.localeCompare(a.start)); // newest past first
            listItemsEl.innerHTML = '';
            function addGroup(title, arr){
              if(arr.length===0) return;
              const header = document.createElement('li');
              header.className='list-group-item text-bg-light fw-semibold';
              header.textContent = title;
              listItemsEl.appendChild(header);
              arr.forEach(ev=>{
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-start gap-2';
                li.innerHTML = `
                  <div>
                    <div class="fw-semibold">${ev.title}</div>
                    <div class="text-muted small">Date: ${ev.start}</div>
                  </div>
                  <span class="badge ${ev.extendedProps?.status==='completed' ? 'text-bg-success' : (ev.extendedProps?.status==='cancelled' ? 'text-bg-secondary' : 'text-bg-primary')}">${ev.extendedProps?.status || 'scheduled'}</span>`;
                listItemsEl.appendChild(li);
              });
            }
            if(currentListTab==='upcoming'){
              if(todays.length===0 && futures.length===0){ listItemsEl.innerHTML = '<li class="list-group-item text-muted">No upcoming appointments.</li>'; return; }
              addGroup('Today', todays);
              addGroup('Future', futures);
            } else {
              if(past.length===0){ listItemsEl.innerHTML = '<li class="list-group-item text-muted">No past appointments.</li>'; return; }
              addGroup('Past', past);
            }
          })
          .catch(()=>{ listItemsEl.innerHTML = '<li class="list-group-item text-danger">Failed to load.</li>'; });
      }

      function showCalendar(){
        calendarEl.classList.remove('d-none');
        listEl.classList.add('d-none');
        viewCalBtn.classList.add('active');
        viewListBtn.classList.remove('active');
        nudgeCalendar();
      }
      function showList(){
        calendarEl.classList.add('d-none');
        listEl.classList.remove('d-none');
        viewCalBtn.classList.remove('active');
        viewListBtn.classList.add('active');
        updateList('upcoming');
      }

      viewCalBtn?.addEventListener('click', showCalendar);
      viewListBtn?.addEventListener('click', showList);
      listTabsEl?.addEventListener('click', (e)=>{
        const btn = e.target.closest('button[data-target]'); if(!btn) return;
        document.querySelectorAll('#listTabs .nav-link').forEach(b=>b.classList.remove('active'));
        btn.classList.add('active');
        updateList(btn.getAttribute('data-target'));
      });

      // Default to List view on load
      showList();

    })();
  </script>
  @endpush
</x-app-layout>
