<x-app-layout>
	<x-slot name="header">
		<h2 class="h5 mb-0">{{ __('Add New Inmate') }}</h2>
	</x-slot>

	<div class="card">
		<div class="card-body">
			<form method="POST" action="{{ route('developer.inmates.store') }}" enctype="multipart/form-data" novalidate>
				@csrf
				<div class="row g-3 mb-2">
					<div class="col-md-6">
						<label for="institution_id" class="form-label">Institution <span class="text-danger">*</span></label>
						<select id="institution_id" name="institution_id" class="form-select form-select-sm @error('institution_id') is-invalid @enderror" required>
							<option value="" disabled {{ old('institution_id') ? '' : 'selected' }}>Select institution...</option>
							@foreach($institutions as $inst)
								<option value="{{ $inst->id }}" @selected(old('institution_id') == $inst->id)>{{ $inst->name }}</option>
							@endforeach
						</select>
						@error('institution_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-6">
						<label for="type" class="form-label">Inmate Type <span class="text-danger">*</span></label>
						<select id="inmate_type" name="type" class="form-select form-select-sm @error('type') is-invalid @enderror" required>
							<option value="" disabled {{ old('type') ? '' : 'selected' }}>Select type...</option>
							@foreach(($inmateTypes ?? []) as $label => $value)
								<option value="{{ $value }}" @selected(old('type')===$value)>{{ $label }}</option>
							@endforeach
						</select>
						@error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
				</div>
				<div class="row g-3">
					<div class="col-md-4">
						<label for="registration_number" class="form-label">Registration #</label>
						<input type="text" class="form-control @error('registration_number') is-invalid @enderror" id="registration_number" name="registration_number" value="{{ old('registration_number') }}">
						@error('registration_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-4">
						<label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
						<input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
						@error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-4">
						<label for="last_name" class="form-label">Last Name</label>
						<input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}">
						@error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
				</div>
				<div class="row g-3">
					<div class="col-md-4">
						<label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
						<input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
						@error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-4">
						<label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
						<select id="gender" name="gender" class="form-select @error('gender') is-invalid @enderror" required>
							<option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select...</option>
							@foreach(['Male','Female','Other'] as $g)
								<option value="{{ $g }}" @selected(old('gender')===$g)>{{ $g }}</option>
							@endforeach
						</select>
						@error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-4">
						<label for="admission_date" class="form-label">Admission Date <span class="text-danger">*</span></label>
						<input type="date" class="form-control @error('admission_date') is-invalid @enderror" id="admission_date" name="admission_date" value="{{ old('admission_date') }}" required>
						@error('admission_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
				</div>
				<div class="row g-3 mt-1">
					<div class="col-md-4">
						<label for="photo" class="form-label">Photo</label>
						<input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*">
						@error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-4">
						<label for="aadhaar_card" class="form-label">Aadhaar Card</label>
						<input type="file" class="form-control @error('aadhaar_card') is-invalid @enderror" id="aadhaar_card" name="aadhaar_card">
						@error('aadhaar_card')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-4">
						<label for="ration_card" class="form-label">Ration Card</label>
						<input type="file" class="form-control @error('ration_card') is-invalid @enderror" id="ration_card" name="ration_card">
						@error('ration_card')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
				</div>
				<div class="row g-3 mt-1">
					<div class="col-md-4">
						<label for="panchayath_letter" class="form-label">Panchayath Letter</label>
						<input type="file" class="form-control @error('panchayath_letter') is-invalid @enderror" id="panchayath_letter" name="panchayath_letter">
						@error('panchayath_letter')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-4">
						<label for="disability_card" class="form-label">Disability Card</label>
						<input type="file" class="form-control @error('disability_card') is-invalid @enderror" id="disability_card" name="disability_card">
						@error('disability_card')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-4">
						<label for="doctor_certificate" class="form-label">Doctor Certificate</label>
						<input type="file" class="form-control @error('doctor_certificate') is-invalid @enderror" id="doctor_certificate" name="doctor_certificate">
						@error('doctor_certificate')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
				</div>
				<div class="row g-3 mt-1">
					<div class="col-md-4">
						<label for="vincent_depaul_card" class="form-label">Vincent Depaul Card</label>
						<input type="file" class="form-control @error('vincent_depaul_card') is-invalid @enderror" id="vincent_depaul_card" name="vincent_depaul_card">
						@error('vincent_depaul_card')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-4">
						<label for="aadhaar_number" class="form-label">Aadhaar Number</label>
						<input type="text" class="form-control @error('aadhaar_number') is-invalid @enderror" id="aadhaar_number" name="aadhaar_number" value="{{ old('aadhaar_number') }}">
						@error('aadhaar_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
				</div>

				<hr class="my-4">
				<h5>Guardian Information</h5>
				<div class="row g-3">
					<div class="col-md-3">
						<label for="guardian_relation" class="form-label">Relation</label>
						<input type="text" class="form-control @error('guardian_relation') is-invalid @enderror" id="guardian_relation" name="guardian_relation" value="{{ old('guardian_relation') }}">
						@error('guardian_relation')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-3">
						<label for="guardian_first_name" class="form-label">First Name</label>
						<input type="text" class="form-control @error('guardian_first_name') is-invalid @enderror" id="guardian_first_name" name="guardian_first_name" value="{{ old('guardian_first_name') }}">
						@error('guardian_first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-3">
						<label for="guardian_last_name" class="form-label">Last Name</label>
						<input type="text" class="form-control @error('guardian_last_name') is-invalid @enderror" id="guardian_last_name" name="guardian_last_name" value="{{ old('guardian_last_name') }}">
						@error('guardian_last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-3">
						<label for="guardian_phone" class="form-label">Phone</label>
						<input type="text" class="form-control @error('guardian_phone') is-invalid @enderror" id="guardian_phone" name="guardian_phone" value="{{ old('guardian_phone') }}">
						@error('guardian_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
				</div>
				<div class="row g-3 mt-1">
					<div class="col-md-4">
						<label for="guardian_email" class="form-label">Email</label>
						<input type="email" class="form-control @error('guardian_email') is-invalid @enderror" id="guardian_email" name="guardian_email" value="{{ old('guardian_email') }}">
						@error('guardian_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-8">
						<label for="guardian_address" class="form-label">Address</label>
						<textarea id="guardian_address" name="guardian_address" rows="2" class="form-control @error('guardian_address') is-invalid @enderror">{{ old('guardian_address') }}</textarea>
						@error('guardian_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
				</div>

				<hr class="my-4">
				<h5>Additional Notes</h5>
				<div class="mb-3">
					<textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
					@error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
				</div>
				<div class="mb-3">
					<label for="critical_alert" class="form-label">Critical Alert <span class="text-danger small">(visible to all staff)</span></label>
					<textarea id="critical_alert" name="critical_alert" rows="2" class="form-control @error('critical_alert') is-invalid @enderror" placeholder="e.g., Allergic to penicillin, monitor blood pressure daily">{{ old('critical_alert') }}</textarea>
					@error('critical_alert')<div class="invalid-feedback">{{ $message }}</div>@enderror
				</div>

				{{-- Dynamic Type Sections (initially hidden) --}}
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

				<hr class="my-4">
				<h5>Extra Documents</h5>
				<div id="extra-documents-wrapper"></div>
				<button type="button" class="btn btn-sm btn-outline-primary" id="add-document-btn">Add Document</button>
				<small class="text-muted d-block mt-2">Name + file pair. You can add multiple.</small>
				<div class="d-flex gap-2 mt-4">
					<button type="submit" class="btn btn-success">Save Inmate</button>
					<a href="{{ route('developer.inmates.index') }}" class="btn btn-outline-secondary">Cancel</a>
				</div>
			</form>
		</div>
	</div>

@push('scripts')
<script>
// Dynamic inmate type section toggling
document.addEventListener('DOMContentLoaded', () => {
	const select = document.getElementById('inmate_type');
	const blocks = document.querySelectorAll('.type-block');
	function refresh(){
		const val = select.value;
		blocks.forEach(b=>{ b.style.display = (b.getAttribute('data-type')===val) ? '' : 'none'; });
	}
	if(select){
		select.addEventListener('change', refresh);
		if(select.value) refresh();
	}
});
// Extra documents logic
document.addEventListener('DOMContentLoaded', () => {
	const btn = document.getElementById('add-document-btn');
	const wrapper = document.getElementById('extra-documents-wrapper');
	let index = 0;
	btn.addEventListener('click', () => {
		const div = document.createElement('div');
		div.className = 'row g-2 align-items-end mb-2';
		div.innerHTML = `
			<div class="col-md-5">
				<label class="form-label small">Document Name</label>
				<input type="text" name="doc_names[${index}]" class="form-control" />
			</div>
			<div class="col-md-5">
				<label class="form-label small">File</label>
				<input type="file" name="doc_files[${index}]" class="form-control" />
			</div>
			<div class="col-md-2 d-grid">
				<button type="button" class="btn btn-outline-danger btn-sm remove-doc">Remove</button>
			</div>`;
		wrapper.appendChild(div);
		index++;
	});
	wrapper.addEventListener('click', e => {
		if(e.target.classList.contains('remove-doc')){
			e.target.closest('.row').remove();
		}
	});
});
</script>
@endpush
</x-app-layout>
