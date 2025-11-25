<x-app-layout>
	<x-slot name="header">
		<h2 class="h4 font-weight-bold">{{ __('Edit Staff') }}</h2>
	</x-slot>

	<div class="card my-4">
		<div class="card-body">
			<form action="{{ route('developer.users.update', $user) }}" method="POST">
				@csrf
				@method('PUT')

				<div class="mb-3">
					<label for="name" class="form-label">Name</label>
					<input type="text" id="name" name="name" class="form-control" value="{{ old('name',$user->name) }}" required>
				</div>

				<div class="mb-3">
					<label for="email" class="form-label">Email</label>
					<input type="email" id="email" name="email" class="form-control" value="{{ old('email',$user->email) }}" required>
				</div>

				<div class="mb-3">
					<label for="role" class="form-label">Role</label>
					<select id="role" name="role" class="form-select" required>
						@foreach($roles as $r)
							<option value="{{ $r }}" @selected($user->role === $r)>{{ ucfirst($r) }}</option>
						@endforeach
					</select>
				</div>

				<div class="mb-3">
					<label for="institution_id" class="form-label">Institution (optional)</label>
					<select id="institution_id" name="institution_id" class="form-select">
						<option value="">-- None --</option>
						@foreach($institutions as $inst)
							<option value="{{ $inst->id }}" @selected($user->institution_id == $inst->id)>{{ $inst->name }}</option>
						@endforeach
					</select>
				</div>

				<hr>
				<p class="small text-muted">Leave password fields blank to keep the current password.</p>
				<div class="row g-3">
					<div class="col-md-6">
						<label for="password" class="form-label">New Password</label>
						<input type="password" id="password" name="password" class="form-control" autocomplete="new-password">
					</div>
					<div class="col-md-6">
						<label for="password_confirmation" class="form-label">Confirm New Password</label>
						<input type="password" id="password_confirmation" name="password_confirmation" class="form-control" autocomplete="new-password">
					</div>
				</div>

				<div class="d-flex gap-2 mt-3">
					<button type="submit" class="btn btn-primary">Save Changes</button>
					<a href="{{ route('developer.users.index') }}" class="btn btn-secondary">Cancel</a>
				</div>
			</form>
		</div>
	</div>
</x-app-layout>
