<x-app-layout>
	<x-slot name="header">
		<h2 class="h5 mb-0">{{ __('Edit Inmate') }}</h2>
	</x-slot>

	<div class="card">
		<div class="card-body">
			<form method="POST" action="{{ route('developer.inmates.update', $inmate) }}" enctype="multipart/form-data" novalidate>
				@csrf
				@method('PUT')
				<div class="row g-3">
					<div class="col-md-4">
						<label for="registration_number" class="form-label">Registration #</label>
						<input type="text" class="form-control @error('registration_number') is-invalid @enderror" id="registration_number" name="registration_number" value="{{ old('registration_number', $inmate->registration_number) }}">
						@error('registration_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-4">
						<label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
						<input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $inmate->first_name) }}" required>
						@error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-4">
						<label for="last_name" class="form-label">Last Name</label>
						<input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $inmate->last_name) }}">
						@error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
				</div>
				<div class="row g-3">
					<div class="col-md-4">
						<label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
						<input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', optional($inmate->date_of_birth)->format('Y-m-d')) }}" required>
						@error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-4">
						<label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
						<select id="gender" name="gender" class="form-select @error('gender') is-invalid @enderror" required>
							@foreach(['Male','Female','Other'] as $g)
								<option value="{{ $g }}" @selected(old('gender', $inmate->gender)===$g)>{{ $g }}</option>
							@endforeach
						</select>
						@error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-4">
						<label for="admission_date" class="form-label">Admission Date <span class="text-danger">*</span></label>
						<input type="date" class="form-control @error('admission_date') is-invalid @enderror" id="admission_date" name="admission_date" value="{{ old('admission_date', optional($inmate->admission_date)->format('Y-m-d')) }}" required>
						@error('admission_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
				</div>
				<div class="row g-3 mt-1">
					<div class="col-md-4">
						<label class="form-label">Photo</label>
						@if($inmate->photo_path)
							<div class="mb-1"><img src="{{ $inmate->avatar_url }}" alt="Photo" class="img-thumbnail" style="max-height:100px"></div>
						@endif
						<input type="file" class="form-control @error('photo') is-invalid @enderror" name="photo">
						@error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-4">
						<label class="form-label">Aadhaar Card</label>
						@if($inmate->aadhaar_card_path)
							@php $d = Storage::disk(config('filesystems.default')); try { $a1 = config('filesystems.default')==='s3' ? $d->temporaryUrl($inmate->aadhaar_card_path, now()->addMinutes(5)) : $d->url($inmate->aadhaar_card_path); } catch (\Throwable $e) { $a1 = null; } @endphp
							@if($a1)<div class="mb-1"><a href="{{ $a1 }}" target="_blank">Current</a></div>@endif
						@endif
						<input type="file" class="form-control @error('aadhaar_card') is-invalid @enderror" name="aadhaar_card">
						@error('aadhaar_card')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-4">
						<label class="form-label">Ration Card</label>
						@if($inmate->ration_card_path)
							@php $d = Storage::disk(config('filesystems.default')); try { $a2 = config('filesystems.default')==='s3' ? $d->temporaryUrl($inmate->ration_card_path, now()->addMinutes(5)) : $d->url($inmate->ration_card_path); } catch (\Throwable $e) { $a2 = null; } @endphp
							@if($a2)<div class="mb-1"><a href="{{ $a2 }}" target="_blank">Current</a></div>@endif
						@endif
						<input type="file" class="form-control @error('ration_card') is-invalid @enderror" name="ration_card">
						@error('ration_card')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
				</div>
				<div class="row g-3 mt-1">
					<div class="col-md-4">
						<label class="form-label">Panchayath Letter</label>
						@if($inmate->panchayath_letter_path)
							@php $d = Storage::disk(config('filesystems.default')); try { $a3 = config('filesystems.default')==='s3' ? $d->temporaryUrl($inmate->panchayath_letter_path, now()->addMinutes(5)) : $d->url($inmate->panchayath_letter_path); } catch (\Throwable $e) { $a3 = null; } @endphp
							@if($a3)<div class="mb-1"><a href="{{ $a3 }}" target="_blank">Current</a></div>@endif
						@endif
						<input type="file" class="form-control @error('panchayath_letter') is-invalid @enderror" name="panchayath_letter">
						@error('panchayath_letter')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-4">
						<label class="form-label">Disability Card</label>
						@if($inmate->disability_card_path)
							@php $d = Storage::disk(config('filesystems.default')); try { $a4 = config('filesystems.default')==='s3' ? $d->temporaryUrl($inmate->disability_card_path, now()->addMinutes(5)) : $d->url($inmate->disability_card_path); } catch (\Throwable $e) { $a4 = null; } @endphp
							@if($a4)<div class="mb-1"><a href="{{ $a4 }}" target="_blank">Current</a></div>@endif
						@endif
						<input type="file" class="form-control @error('disability_card') is-invalid @enderror" name="disability_card">
						@error('disability_card')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-4">
						<label class="form-label">Doctor Certificate</label>
						@if($inmate->doctor_certificate_path)
							@php $d = Storage::disk(config('filesystems.default')); try { $a5 = config('filesystems.default')==='s3' ? $d->temporaryUrl($inmate->doctor_certificate_path, now()->addMinutes(5)) : $d->url($inmate->doctor_certificate_path); } catch (\Throwable $e) { $a5 = null; } @endphp
							@if($a5)<div class="mb-1"><a href="{{ $a5 }}" target="_blank">Current</a></div>@endif
						@endif
						<input type="file" class="form-control @error('doctor_certificate') is-invalid @enderror" name="doctor_certificate">
						@error('doctor_certificate')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
				</div>
				<div class="row g-3 mt-1">
					<div class="col-md-4">
						<label class="form-label">Vincent Depaul Card</label>
						@if($inmate->vincent_depaul_card_path)
							@php $d = Storage::disk(config('filesystems.default')); try { $a6 = config('filesystems.default')==='s3' ? $d->temporaryUrl($inmate->vincent_depaul_card_path, now()->addMinutes(5)) : $d->url($inmate->vincent_depaul_card_path); } catch (\Throwable $e) { $a6 = null; } @endphp
							@if($a6)<div class="mb-1"><a href="{{ $a6 }}" target="_blank">Current</a></div>@endif
						@endif
						<input type="file" class="form-control @error('vincent_depaul_card') is-invalid @enderror" name="vincent_depaul_card">
						@error('vincent_depaul_card')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-4">
						<label for="aadhaar_number" class="form-label">Aadhaar Number</label>
						<input type="text" class="form-control @error('aadhaar_number') is-invalid @enderror" id="aadhaar_number" name="aadhaar_number" value="{{ old('aadhaar_number', $inmate->aadhaar_number) }}">
						@error('aadhaar_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
				</div>

				<hr class="my-4">
				<h5>Guardian Information</h5>
				<div class="row g-3">
					<div class="col-md-3">
						<label class="form-label">Relation</label>
						<input type="text" class="form-control @error('guardian_relation') is-invalid @enderror" name="guardian_relation" value="{{ old('guardian_relation', $inmate->guardian_relation) }}">
						@error('guardian_relation')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-3">
						<label class="form-label">First Name</label>
						<input type="text" class="form-control @error('guardian_first_name') is-invalid @enderror" name="guardian_first_name" value="{{ old('guardian_first_name', $inmate->guardian_first_name) }}">
						@error('guardian_first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-3">
						<label class="form-label">Last Name</label>
						<input type="text" class="form-control @error('guardian_last_name') is-invalid @enderror" name="guardian_last_name" value="{{ old('guardian_last_name', $inmate->guardian_last_name) }}">
						@error('guardian_last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-3">
						<label class="form-label">Phone</label>
						<input type="text" class="form-control @error('guardian_phone') is-invalid @enderror" name="guardian_phone" value="{{ old('guardian_phone', $inmate->guardian_phone) }}">
						@error('guardian_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
				</div>
				<div class="row g-3 mt-1">
					<div class="col-md-4">
						<label class="form-label">Email</label>
						<input type="email" class="form-control @error('guardian_email') is-invalid @enderror" name="guardian_email" value="{{ old('guardian_email', $inmate->guardian_email) }}">
						@error('guardian_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-8">
						<label class="form-label">Address</label>
						<textarea name="guardian_address" rows="2" class="form-control @error('guardian_address') is-invalid @enderror">{{ old('guardian_address', $inmate->guardian_address) }}</textarea>
						@error('guardian_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
				</div>

				<hr class="my-4">
				<h5>Additional Notes</h5>
				<div class="mb-3">
					<textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $inmate->notes) }}</textarea>
					@error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
				</div>
				<div class="mb-3">
					<label for="critical_alert" class="form-label">Critical Alert <span class="text-danger small">(visible to all staff)</span></label>
					<textarea id="critical_alert" name="critical_alert" rows="2" class="form-control @error('critical_alert') is-invalid @enderror" placeholder="e.g., Allergic to penicillin, monitor vitals">{{ old('critical_alert', $inmate->critical_alert) }}</textarea>
					@error('critical_alert')<div class="invalid-feedback">{{ $message }}</div>@enderror
				</div>

				<hr class="my-4">
				<h5>Extra Documents</h5>
				<div id="extra-documents-wrapper"></div>
				<button type="button" class="btn btn-sm btn-outline-primary" id="add-document-btn">Add Document</button>
				<small class="text-muted d-block mt-2">Add new name + file pairs. Existing uploaded system docs shown above.</small>
				<div class="d-flex gap-2 mt-4">
					<button type="submit" class="btn btn-success">Update Inmate</button>
					<a href="{{ route('developer.inmates.index') }}" class="btn btn-outline-secondary">Cancel</a>
				</div>
			</form>
		</div>
	</div>

@push('scripts')
<script>
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
