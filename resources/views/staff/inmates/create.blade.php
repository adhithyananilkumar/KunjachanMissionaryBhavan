<x-app-layout>
    <x-slot name="header"><h2 class="h5 mb-0">Register New Inmate</h2></x-slot>
    <div class="card"><div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <div class="fw-semibold mb-1">Please fix the following:</div>
                <ul class="mb-0 small">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('staff.inmates.store') }}" enctype="multipart/form-data" novalidate>@csrf
            <div class="row g-3 mb-2">
                <div class="col-md-6">
                    <label class="form-label">Institution</label>
                    <input class="form-control form-control-sm" value="{{ $institution->name }}" disabled>
                    <input type="hidden" name="institution_id" value="{{ $institution->id }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Inmate Type</label>
                    <select id="inmate_type" name="type" class="form-select form-select-sm">
                        <option value="" disabled {{ old('type')?'' :'selected' }}>Select type...</option>
                        @foreach(($inmateTypes ?? []) as $label=>$value)
                            <option value="{{ $value }}" @selected(old('type')==$value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label">Registration #</label><input name="registration_number" class="form-control" value="{{ old('registration_number') }}"></div>
                <div class="col-md-4"><label class="form-label">First Name <span class="text-danger">*</span></label><input name="first_name" class="form-control" required value="{{ old('first_name') }}"></div>
                <div class="col-md-4"><label class="form-label">Last Name</label><input name="last_name" class="form-control" value="{{ old('last_name') }}"></div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-md-4"><label class="form-label">Date of Birth <span class="text-danger">*</span></label><input type="date" name="date_of_birth" class="form-control" required value="{{ old('date_of_birth') }}"></div>
                <div class="col-md-4"><label class="form-label">Gender <span class="text-danger">*</span></label><select name="gender" class="form-select" required><option value="" disabled selected>Select...</option>@foreach(['Male','Female','Other'] as $g)<option value="{{ $g }}" @selected(old('gender')===$g)>{{ $g }}</option>@endforeach</select></div>
                <div class="col-md-4"><label class="form-label">Admission Date <span class="text-danger">*</span></label><input type="date" name="admission_date" class="form-control" required value="{{ old('admission_date') }}"></div>
            </div>
            <hr class="my-4"><h5>Guardian Information</h5>
            <div class="row g-3"><div class="col-md-3"><label class="form-label">Relation</label><input name="guardian_relation" class="form-control" value="{{ old('guardian_relation') }}"></div><div class="col-md-3"><label class="form-label">First Name</label><input name="guardian_first_name" class="form-control" value="{{ old('guardian_first_name') }}"></div><div class="col-md-3"><label class="form-label">Last Name</label><input name="guardian_last_name" class="form-control" value="{{ old('guardian_last_name') }}"></div><div class="col-md-3"><label class="form-label">Phone</label><input name="guardian_phone" class="form-control" value="{{ old('guardian_phone') }}"></div></div>
            <div class="row g-3 mt-1"><div class="col-md-4"><label class="form-label">Email</label><input type="email" name="guardian_email" class="form-control" value="{{ old('guardian_email') }}"></div><div class="col-md-8"><label class="form-label">Address</label><textarea name="guardian_address" rows="2" class="form-control">{{ old('guardian_address') }}</textarea></div></div>
            <hr class="my-4"><h5>Additional Notes</h5><div class="mb-3"><textarea name="notes" rows="4" class="form-control">{{ old('notes') }}</textarea></div>
            <div class="mb-3"><label class="form-label">Critical Alert (optional)</label><input name="critical_alert" class="form-control" value="{{ old('critical_alert') }}" placeholder="Brief critical alert for clinicians"></div>
            <hr class="my-4"><h5>Room Assignment (optional)</h5>
            <div class="card mb-3"><div class="card-body">
                <div class="row g-2 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label">Pick Room</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" id="roomSearchCreate" placeholder="Search rooms...">
                            <button type="button" class="btn btn-outline-secondary" id="loadRoomsCreate"><span class="bi bi-arrow-repeat"></span></button>
                        </div>
                        <div class="form-check form-switch mt-2 small">
                            <input class="form-check-input" type="checkbox" id="showOccupiedCreate">
                            <label class="form-check-label" for="showOccupiedCreate">Show occupied</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted">Selected</div>
                        <div id="selectedRoomCreate" class="fw-semibold">None</div>
                    </div>
                </div>
                <div class="list-group small mt-2" id="roomsListCreate" style="max-height: 240px; overflow: auto;"><div class="text-muted text-center py-3">Use reload to list rooms</div></div>
                <input type="hidden" name="location_id" id="locationIdCreate" value="">
            </div></div>
            {{-- Dynamic Type Sections --}}
            <div id="dynamic-type-sections" class="mt-4">
                <div class="type-block" data-type="child" style="display:none;">
                    @include('partials.inmates._intake_history')
                    @include('partials.inmates._educational_records')
                </div>
                <div class="type-block" data-type="elderly" style="display:none;">
                    @include('partials.inmates._geriatric_care_plan')
                </div>
                <div class="type-block" data-type="mental_health" style="display:none;">
                    @include('partials.inmates._mental_health_plan')
                </div>
                <div class="type-block" data-type="rehabilitation" style="display:none;">
                    @include('partials.inmates._rehabilitation_plan')
                </div>
            </div>
            <hr class="my-4"><h5>Documents (optional)</h5>
            <div class="border rounded p-2 mb-3">
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label small">Profile Photo</label>
                        <input type="file" name="photo" class="form-control form-control-sm" accept="image/*">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Aadhaar Card</label>
                        <input type="file" name="aadhaar_card" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Ration Card</label>
                        <input type="file" name="ration_card" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Panchayath Letter</label>
                        <input type="file" name="panchayath_letter" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Disability Card</label>
                        <input type="file" name="disability_card" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Doctor Certificate</label>
                        <input type="file" name="doctor_certificate" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Vincent De Paul Card</label>
                        <input type="file" name="vincent_depaul_card" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="mt-2">
                    <div id="docRows"></div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="addDocRow"><span class="bi bi-plus-lg me-1"></span>Add Other Document</button>
                </div>
                <small class="text-muted">Standard IDs above mirror the Admin form. Use “Add Other Document” for anything else.</small>
            </div>
            <div class="d-flex gap-2 mt-2"><button class="btn btn-success">Save Inmate</button><a href="{{ route('staff.inmates.index') }}" class="btn btn-outline-secondary">Cancel</a></div>
        </form>
    </div></div>
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded',()=>{
        const sel=document.getElementById('inmate_type');
        const blocks=document.querySelectorAll('.type-block');
        function show(){const v=sel?.value;blocks.forEach(b=>b.style.display=b.getAttribute('data-type')===v?'':'none');}
        sel?.addEventListener('change',show); if(sel?.value) show();
        // Room picker (staff scope)
        const list=document.getElementById('roomsListCreate');
        const q=document.getElementById('roomSearchCreate');
        const reload=document.getElementById('loadRoomsCreate');
        const showOcc=document.getElementById('showOccupiedCreate');
        const out=document.getElementById('selectedRoomCreate');
        const hidden=document.getElementById('locationIdCreate');
        let all=[];
        async function load(){
            const res=await fetch(`{{ url('staff/allocation/api/locations') }}?show_occupied=${showOcc.checked?1:0}`,{headers:{'Accept':'application/json'}});
            const data=await res.json(); all=data.locations||[]; render();
        }
        function render(){
            const term=(q.value||'').toLowerCase(); list.innerHTML='';
            const items=all.filter(r=> r.name.toLowerCase().includes(term));
            if(items.length===0){ list.innerHTML='<div class="text-muted text-center py-3">No rooms</div>'; return; }
            items.forEach(r=>{ const btn=document.createElement('button'); btn.type='button'; btn.className='list-group-item list-group-item-action d-flex justify-content-between align-items-center'; btn.innerHTML=`<span><span class="bi bi-door-closed me-2"></span>${r.name}${r.occupant? ' <span class=\"badge bg-secondary ms-2\">'+r.occupant+'</span>':''}</span><span class="badge ${r.occupied?'bg-secondary':'bg-success'}">${r.occupied?'Occupied':'Available'}</span>`; btn.disabled=r.occupied && !showOcc.checked; btn.addEventListener('click',()=>{ hidden.value=r.id; out.textContent=r.name; document.querySelectorAll('#roomsListCreate .list-group-item').forEach(x=>x.classList.remove('active')); btn.classList.add('active'); }); list.appendChild(btn); });
        }
        reload?.addEventListener('click', load); q?.addEventListener('input', render); showOcc?.addEventListener('change', load);
        // Docs dynamic rows
        const docRows=document.getElementById('docRows'); const addBtn=document.getElementById('addDocRow'); let d=0;
        function addRow(){ const div=document.createElement('div'); div.className='row g-2 align-items-center mb-2'; div.innerHTML=`<div class="col-md-6"><input name="doc_names[${d}]" class="form-control form-control-sm" placeholder="Document name (e.g., Aadhaar)"></div><div class="col-md-6"><input type="file" name="doc_files[${d}]" class="form-control form-control-sm"></div>`; docRows.appendChild(div); d++; }
        addBtn?.addEventListener('click', addRow);
    });
    </script>
    @endpush
</x-app-layout>
