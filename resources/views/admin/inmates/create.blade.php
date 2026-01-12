<x-app-layout>
    <x-slot name="header"><h2 class="h5 mb-0">Add Inmate</h2></x-slot>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.inmates.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3 mb-3">
                    @if(!empty($types))
                    <div class="col-md-3">
                        <label class="form-label">Inmate Type</label>
                        <select name="type" id="inmate_type" class="form-select">
                            <option value="">Select...</option>
                            @foreach($types as $t)
                                <option value="{{ $t['value'] }}" @selected(old('type')===$t['value'])>{{ $t['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-3">
                        <label class="form-label">Registration #</label>
                        <input name="registration_number" type="text" class="form-control" value="{{ old('registration_number') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">First Name<span class="text-danger">*</span></label>
                        <input name="first_name" type="text" class="form-control" value="{{ old('first_name') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Last Name</label>
                        <input name="last_name" type="text" class="form-control" value="{{ old('last_name') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Gender<span class="text-danger">*</span></label>
                        <select name="gender" class="form-select" required>
                            <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select...</option>
                            @foreach(['Male','Female','Other'] as $g)
                                <option value="{{ $g }}" @selected(old('gender')===$g)>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Date of Birth<span class="text-danger">*</span></label>
                        <input name="date_of_birth" type="date" class="form-control" value="{{ old('date_of_birth') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Admission Date<span class="text-danger">*</span></label>
                        <input name="admission_date" type="date" class="form-control" value="{{ old('admission_date') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Aadhaar Number</label>
                        <input name="aadhaar_number" type="text" class="form-control" value="{{ old('aadhaar_number') }}">
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Photo</label>
                        <input name="photo" type="file" accept="image/*" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Aadhaar Card</label>
                        <input name="aadhaar_card" type="file" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Ration Card</label>
                        <input name="ration_card" type="file" class="form-control">
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Panchayath Letter</label>
                        <input name="panchayath_letter" type="file" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Disability Card</label>
                        <input name="disability_card" type="file" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Doctor Certificate</label>
                        <input name="doctor_certificate" type="file" class="form-control">
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Vincent Depaul Card</label>
                        <input name="vincent_depaul_card" type="file" class="form-control">
                    </div>
                </div>
                <hr class="my-4">
                <h5>Guardian Information</h5>
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Relation</label>
                        <input name="guardian_relation" type="text" class="form-control" value="{{ old('guardian_relation') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">First Name</label>
                        <input name="guardian_first_name" type="text" class="form-control" value="{{ old('guardian_first_name') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Last Name</label>
                        <input name="guardian_last_name" type="text" class="form-control" value="{{ old('guardian_last_name') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Phone</label>
                        <input name="guardian_phone" type="text" class="form-control" value="{{ old('guardian_phone') }}">
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input name="guardian_email" type="email" class="form-control" value="{{ old('guardian_email') }}">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Address</label>
                        <textarea name="guardian_address" rows="2" class="form-control">{{ old('guardian_address') }}</textarea>
                    </div>
                </div>
                <hr class="my-4">
                <h5>Notes</h5>
                <div class="mb-3">
                    <textarea name="notes" rows="4" class="form-control">{{ old('notes') }}</textarea>
                </div>
                <div class="mb-3 d-none" id="intake_history_wrapper">
                    <label class="form-label">Intake History (Narrative)</label>
                    <textarea name="intake_history" rows="4" class="form-control" placeholder="Describe how the child came to the institution">{{ old('intake_history') }}</textarea>
                </div>
                <hr class="my-4">
                <h5>Extra Documents</h5>
                <div id="extra-documents-wrapper"></div>
                <button type="button" id="add-document-btn" class="btn btn-sm btn-outline-primary mb-3">Add Document</button>
                <small class="text-muted d-block mb-3">Add any additional supporting documents.</small>
                <div class="d-flex gap-2">
                    <button class="btn btn-success" type="submit">Save</button>
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
});
</script>
@endpush
</x-app-layout>
