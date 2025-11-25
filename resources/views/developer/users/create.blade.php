<x-app-layout>
	<x-slot name="header">
		<h2 class="h4 font-weight-bold">{{ __('Add New Staff') }}</h2>
	</x-slot>

	<div class="card my-4">
		<div class="card-body">
			<form action="{{ route('developer.users.store') }}" method="POST">
				@csrf

				<div class="mb-3">
					<label for="institution_id" class="form-label">Institution</label>
					<select class="form-select" id="institution_id" name="institution_id" required>
						<option selected disabled>Select an institution...</option>
						@foreach($institutions as $institution)
							<option value="{{ $institution->id }}">{{ $institution->name }}</option>
						@endforeach
					</select>
				</div>

				<div class="mb-3">
					<label for="name" class="form-label">Name</label>
					<input type="text" class="form-control" id="name" name="name" required>
				</div>

				<div class="mb-3">
					<label for="email" class="form-label">Email Address</label>
					<input type="email" class="form-control" id="email" name="email" required>
				</div>

				<div class="mb-3">
					<label for="role" class="form-label">Role</label>
					<select class="form-select" id="role" name="role" required>
						@foreach($roles as $r)
							<option value="{{ $r }}">{{ ucfirst($r) }}</option>
						@endforeach
					</select>
				</div>

				<div class="mb-3">
					<label for="password" class="form-label">Password</label>
					<input type="password" class="form-control" id="password" name="password" required>
				</div>

				<div class="mb-3">
					<label for="password_confirmation" class="form-label">Confirm Password</label>
					<input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
				</div>

				<button type="submit" class="btn btn-primary">Save Staff Member</button>
				<a href="{{ route('developer.users.index') }}" class="btn btn-secondary">Cancel</a>
			</form>
		</div>
	</div>
	<script>
	document.addEventListener('DOMContentLoaded', function(){
		const roleSelect = document.getElementById('role');
		const instSelect = document.getElementById('institution_id');
		function syncInstitutionState(){
			if(roleSelect.value === 'system_admin'){
				instSelect.value = '';
				instSelect.setAttribute('disabled','disabled');
				instSelect.removeAttribute('required');
			} else {
				instSelect.removeAttribute('disabled');
				instSelect.setAttribute('required','required');
			}
		}
		roleSelect.addEventListener('change', syncInstitutionState);
		syncInstitutionState();
	});
	</script>
</x-app-layout>
