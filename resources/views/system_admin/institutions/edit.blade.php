<x-app-layout>
	<x-slot name="header"><h2 class="h5 mb-0">Edit Institution</h2></x-slot>
	<div class="card"><div class="card-body">
		@if ($errors->any())
			<div class="alert alert-danger">
				<div class="fw-semibold mb-1">Please fix the following:</div>
				<ul class="mb-0 small">
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif
		<form method="POST" action="{{ route('system_admin.institutions.update', $institution->id) }}">
			@csrf
			@method('PUT')
			<div class="row g-3">
				<div class="col-md-6">
					<label class="form-label">Name <span class="text-danger">*</span></label>
					<input name="name" class="form-control form-control-sm" value="{{ old('name', $institution->name) }}" required>
				</div>
				<div class="col-md-6">
					<label class="form-label">Phone</label>
					<input name="phone" class="form-control form-control-sm" value="{{ old('phone', $institution->phone) }}">
				</div>
				<div class="col-12">
					<label class="form-label">Email</label>
					<input type="email" name="email" class="form-control form-control-sm" value="{{ old('email', $institution->email) }}">
				</div>
				<div class="col-12">
					<label class="form-label">Address <span class="text-danger">*</span></label>
					<textarea name="address" rows="3" class="form-control" required>{{ old('address', $institution->address) }}</textarea>
				</div>
			</div>
			<hr class="my-4">
			<h6 class="mb-2">Enabled Features</h6>
			@php $features = old('features', $institution->enabled_features ?? []); @endphp
			<div class="row row-cols-1 row-cols-md-2 g-2">
				<div class="col"><label class="form-check small"><input class="form-check-input me-2" type="checkbox" name="features[]" value="orphan_care" {{ in_array('orphan_care',$features) ? 'checked' : '' }}> Orphan Care Module</label></div>
				<div class="col"><label class="form-check small"><input class="form-check-input me-2" type="checkbox" name="features[]" value="elderly_care" {{ in_array('elderly_care',$features) ? 'checked' : '' }}> Elderly Care Module</label></div>
				<div class="col"><label class="form-check small"><input class="form-check-input me-2" type="checkbox" name="features[]" value="mental_health" {{ in_array('mental_health',$features) ? 'checked' : '' }}> Mental Health Module</label></div>
				<div class="col"><label class="form-check small"><input class="form-check-input me-2" type="checkbox" name="features[]" value="rehabilitation" {{ in_array('rehabilitation',$features) ? 'checked' : '' }}> Rehabilitation Module</label></div>
				<div class="col"><label class="form-check small"><input class="form-check-input me-2" type="checkbox" name="features[]" value="undefined_inmate" {{ in_array('undefined_inmate',$features) ? 'checked' : '' }}> Allow Undefined / Other Inmate Type</label></div>
			</div>
			<div class="form-check form-switch mt-3">
				<input class="form-check-input" type="checkbox" role="switch" id="doctor_assignment_enabled" name="doctor_assignment_enabled" value="1" {{ old('doctor_assignment_enabled', $institution->doctor_assignment_enabled) ? 'checked' : '' }}>
				<label class="form-check-label" for="doctor_assignment_enabled">Enable doctor-patient assignment (restrict doctors to assigned inmates)</label>
			</div>
			<div class="d-flex gap-2 mt-3">
				<button class="btn btn-primary">Update</button>
				<a href="{{ route('system_admin.institutions.index') }}" class="btn btn-outline-secondary">Cancel</a>
			</div>
		</form>
	</div></div>
</x-app-layout>
