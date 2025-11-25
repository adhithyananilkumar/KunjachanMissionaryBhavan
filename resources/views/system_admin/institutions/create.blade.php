<x-app-layout>
	<x-slot name="header"><h2 class="h5 mb-0">Create Institution</h2></x-slot>
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
		<form method="POST" action="{{ route('system_admin.institutions.store') }}">
			@csrf
			<div class="row g-3">
				<div class="col-md-6">
					<label class="form-label">Name <span class="text-danger">*</span></label>
					<input name="name" class="form-control form-control-sm" value="{{ old('name') }}" required>
				</div>
				<div class="col-md-6">
					<label class="form-label">Phone</label>
					<input name="phone" class="form-control form-control-sm" value="{{ old('phone') }}">
				</div>
				<div class="col-12">
					<label class="form-label">Email</label>
					<input type="email" name="email" class="form-control form-control-sm" value="{{ old('email') }}">
				</div>
				<div class="col-12">
					<label class="form-label">Address <span class="text-danger">*</span></label>
					<textarea name="address" rows="3" class="form-control" required>{{ old('address') }}</textarea>
				</div>
			</div>
			<div class="d-flex gap-2 mt-3">
				<button class="btn btn-success">Save</button>
				<a href="{{ route('system_admin.institutions.index') }}" class="btn btn-outline-secondary">Cancel</a>
			</div>
		</form>
	</div></div>
</x-app-layout>
