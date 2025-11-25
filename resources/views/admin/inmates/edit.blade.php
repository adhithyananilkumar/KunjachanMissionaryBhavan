<x-app-layout>
    <x-slot name="header"><h2 class="h5 mb-0">Edit Inmate</h2></x-slot>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.inmates.update', $inmate) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row g-3 mb-3">
                    @if(!empty($types))
                    <div class="col-md-3">
                        <label class="form-label">Inmate Type</label>
                        <select name="type" id="inmate_type" class="form-select">
                            <option value="">Select...</option>
                            @foreach($types as $t)
                                <option value="{{ $t['value'] }}" @selected(old('type',$inmate->type)===$t['value'])>{{ $t['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-3">
                        <label class="form-label">Registration #</label>
                        <input name="registration_number" type="text" class="form-control" value="{{ old('registration_number',$inmate->registration_number) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">First Name<span class="text-danger">*</span></label>
                        <input name="first_name" type="text" class="form-control" value="{{ old('first_name',$inmate->first_name) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Last Name</label>
                        <input name="last_name" type="text" class="form-control" value="{{ old('last_name',$inmate->last_name) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Gender<span class="text-danger">*</span></label>
                        <select name="gender" class="form-select" required>
                            @foreach(['Male','Female','Other'] as $g)
                                <option value="{{ $g }}" @selected(old('gender',$inmate->gender)===$g)>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Date of Birth<span class="text-danger">*</span></label>
                        <input name="date_of_birth" type="date" class="form-control" value="{{ old('date_of_birth',$inmate->date_of_birth?->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Admission Date<span class="text-danger">*</span></label>
                        <input name="admission_date" type="date" class="form-control" value="{{ old('admission_date',$inmate->admission_date?->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Aadhaar Number</label>
                        <input name="aadhaar_number" type="text" class="form-control" value="{{ old('aadhaar_number',$inmate->aadhaar_number) }}">
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Photo</label>
                        @if($inmate->photo_path)
                            @php $disk = Storage::disk(config('filesystems.default')); $photoUrl = $inmate->photo_path ? (config('filesystems.default')==='s3' ? $disk->temporaryUrl($inmate->photo_path, now()->addMinutes(5)) : $disk->url($inmate->photo_path)) : null; @endphp
                            @if($photoUrl)<div class="mb-1"><img src="{{ $photoUrl }}" alt="Photo" class="img-thumbnail" style="max-height:100px"></div>@endif
                        @endif
                        <input name="photo" type="file" accept="image/*" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Aadhaar Card</label>
                        @if($inmate->aadhaar_card_path)
                            @php $u = $inmate->aadhaar_card_path ? (config('filesystems.default')==='s3' ? $disk->temporaryUrl($inmate->aadhaar_card_path, now()->addMinutes(5)) : $disk->url($inmate->aadhaar_card_path)) : null; @endphp
                            @if($u)<div class="mb-1"><a href="{{ $u }}" target="_blank">Current</a></div>@endif
                        @endif
                        <input name="aadhaar_card" type="file" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Ration Card</label>
                        @if($inmate->ration_card_path)
                            @php $u = $inmate->ration_card_path ? (config('filesystems.default')==='s3' ? $disk->temporaryUrl($inmate->ration_card_path, now()->addMinutes(5)) : $disk->url($inmate->ration_card_path)) : null; @endphp
                            @if($u)<div class="mb-1"><a href="{{ $u }}" target="_blank">Current</a></div>@endif
                        @endif
                        <input name="ration_card" type="file" class="form-control">
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Panchayath Letter</label>
                        @if($inmate->panchayath_letter_path)
                            @php $u = $inmate->panchayath_letter_path ? (config('filesystems.default')==='s3' ? $disk->temporaryUrl($inmate->panchayath_letter_path, now()->addMinutes(5)) : $disk->url($inmate->panchayath_letter_path)) : null; @endphp
                            @if($u)<div class="mb-1"><a href="{{ $u }}" target="_blank">Current</a></div>@endif
                        @endif
                        <input name="panchayath_letter" type="file" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Disability Card</label>
                        @if($inmate->disability_card_path)
                            @php $u = $inmate->disability_card_path ? (config('filesystems.default')==='s3' ? $disk->temporaryUrl($inmate->disability_card_path, now()->addMinutes(5)) : $disk->url($inmate->disability_card_path)) : null; @endphp
                            @if($u)<div class="mb-1"><a href="{{ $u }}" target="_blank">Current</a></div>@endif
                        @endif
                        <input name="disability_card" type="file" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Doctor Certificate</label>
                        @if($inmate->doctor_certificate_path)
                            @php $u = $inmate->doctor_certificate_path ? (config('filesystems.default')==='s3' ? $disk->temporaryUrl($inmate->doctor_certificate_path, now()->addMinutes(5)) : $disk->url($inmate->doctor_certificate_path)) : null; @endphp
                            @if($u)<div class="mb-1"><a href="{{ $u }}" target="_blank">Current</a></div>@endif
                        @endif
                        <input name="doctor_certificate" type="file" class="form-control">
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Vincent Depaul Card</label>
                        @if($inmate->vincent_depaul_card_path)
                            @php $u = $inmate->vincent_depaul_card_path ? (config('filesystems.default')==='s3' ? $disk->temporaryUrl($inmate->vincent_depaul_card_path, now()->addMinutes(5)) : $disk->url($inmate->vincent_depaul_card_path)) : null; @endphp
                            @if($u)<div class="mb-1"><a href="{{ $u }}" target="_blank">Current</a></div>@endif
                        @endif
                        <input name="vincent_depaul_card" type="file" class="form-control">
                    </div>
                </div>
                <hr class="my-4">
                <h5>Guardian Information</h5>
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Relation</label>
                        <input name="guardian_relation" type="text" class="form-control" value="{{ old('guardian_relation',$inmate->guardian_relation) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">First Name</label>
                        <input name="guardian_first_name" type="text" class="form-control" value="{{ old('guardian_first_name',$inmate->guardian_first_name) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Last Name</label>
                        <input name="guardian_last_name" type="text" class="form-control" value="{{ old('guardian_last_name',$inmate->guardian_last_name) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Phone</label>
                        <input name="guardian_phone" type="text" class="form-control" value="{{ old('guardian_phone',$inmate->guardian_phone) }}">
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input name="guardian_email" type="email" class="form-control" value="{{ old('guardian_email',$inmate->guardian_email) }}">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Address</label>
                        <textarea name="guardian_address" rows="2" class="form-control">{{ old('guardian_address',$inmate->guardian_address) }}</textarea>
                    </div>
                </div>
                <hr class="my-4">
                <h5>Notes</h5>
                <div class="mb-3">
                    <textarea name="notes" rows="4" class="form-control">{{ old('notes',$inmate->notes) }}</textarea>
                </div>
                <div class="mb-3 d-none" id="intake_history_wrapper">
                    <label class="form-label">Intake History (Narrative)</label>
                    <textarea name="intake_history" rows="4" class="form-control" placeholder="Describe how the child came to the institution">{{ old('intake_history',$inmate->intake_history) }}</textarea>
                </div>
                                <hr class="my-4">
                                                <h5>Location Assignment</h5>
                                                <div class="card mb-3">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <div>
                                                                <div class="small text-muted">Current Location</div>
                                                                <div class="fw-semibold">{{ optional($inmate->currentLocation?->location)->name ?? 'Not Assigned' }}</div>
                                                            </div>
                                                        </div>
                                                        <form method="POST" action="{{ route('admin.inmates.assign-location',$inmate) }}" class="row g-2">
                                                            @csrf
                                                            <div class="col-md-4">
                                                                <label class="form-label">Block</label>
                                                                <select id="alloc_block" class="form-select">
                                                                    <option value="">Select block...</option>
                                                                    @foreach(\App\Models\Block::where('institution_id', auth()->user()->institution_id)->orderBy('name')->get() as $b)
                                                                        <option value="{{ $b->id }}">{{ $b->name }}{{ $b->prefix ? ' ('.$b->prefix.')' : '' }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label">Type</label>
                                                                <select id="alloc_type" class="form-select" disabled>
                                                                    <option value="">Select type...</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label">Number</label>
                                                                <select id="alloc_number" name="location_id" class="form-select" disabled>
                                                                    <option value="">Select number...</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-12 d-flex justify-content-between mt-2">
                                                                <button type="button" id="alloc_clear" class="btn btn-outline-secondary btn-sm">Clear (None)</button>
                                                                <button class="btn btn-outline-primary">Update Location</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                <hr class="my-4">
                <h5>Extra Documents</h5>
                <div id="extra-documents-wrapper"></div>
                <button type="button" id="add-document-btn" class="btn btn-sm btn-outline-primary mb-3">Add Document</button>
                <small class="text-muted d-block mb-3">Upload new supporting documents.</small>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Update</button>
                    <a href="{{ route('admin.inmates.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', ()=>{
    const typeSelect=document.getElementById('inmate_type');
    const intakeWrapper=document.getElementById('intake_history_wrapper');
    function toggleIntake(){ if(typeSelect && intakeWrapper){ if(typeSelect.value==='child_resident'){ intakeWrapper.classList.remove('d-none'); } else { intakeWrapper.classList.add('d-none'); } } }
    if(typeSelect){ typeSelect.addEventListener('change', toggleIntake); toggleIntake(); }
  const btn=document.getElementById('add-document-btn');
  const wrapper=document.getElementById('extra-documents-wrapper');
  let i=0;
  btn.addEventListener('click',()=>{
    const row=document.createElement('div');
    row.className='row g-2 align-items-end mb-2';
    row.innerHTML=`<div class="col-md-5"><label class="form-label small">Document Name</label><input type="text" name="doc_names[${i}]" class="form-control"></div>
      <div class="col-md-5"><label class="form-label small">File</label><input type="file" name="doc_files[${i}]" class="form-control"></div>
      <div class="col-md-2 d-grid"><button type="button" class="btn btn-outline-danger btn-sm remove-doc">Remove</button></div>`;
    wrapper.appendChild(row); i++;
  });
  wrapper.addEventListener('click',e=>{ if(e.target.classList.contains('remove-doc')) e.target.closest('.row').remove(); });
    // Cascading selects for allocation
    const blockSel = document.getElementById('alloc_block');
    const typeSel = document.getElementById('alloc_type');
    const numSel = document.getElementById('alloc_number');
    const clearBtn = document.getElementById('alloc_clear');
    function resetType(){ typeSel.innerHTML = '<option value="">Select type...</option>'; typeSel.disabled = true; }
    function resetNumber(){ numSel.innerHTML = '<option value="">Select number...</option>'; numSel.disabled = true; }
    blockSel?.addEventListener('change', async function(){
        resetType(); resetNumber();
        const id = this.value; if(!id){ return; }
        const res = await fetch(`{{ url('admin/allocation/api/blocks') }}/${id}/types`);
        if(!res.ok){ return; }
        const types = await res.json();
        types.forEach(t=>{ const opt=document.createElement('option'); opt.value=t; opt.textContent=t.charAt(0).toUpperCase()+t.slice(1); typeSel.appendChild(opt); });
        typeSel.disabled = types.length===0;
    });
    typeSel?.addEventListener('change', async function(){
        resetNumber();
        const blockId = blockSel.value; const type = this.value; if(!blockId || !type){ return; }
        const res = await fetch(`{{ url('admin/allocation/api/blocks') }}/${blockId}/types/${type}/numbers`);
        if(!res.ok){ return; }
        const nums = await res.json();
        nums.forEach(n=>{ const opt=document.createElement('option'); opt.value=n.id; opt.textContent=n.number; numSel.appendChild(opt); });
        numSel.disabled = nums.length===0;
    });
    clearBtn?.addEventListener('click', ()=>{ blockSel.value=''; resetType(); resetNumber(); });
});
</script>
@endpush
</x-app-layout>
