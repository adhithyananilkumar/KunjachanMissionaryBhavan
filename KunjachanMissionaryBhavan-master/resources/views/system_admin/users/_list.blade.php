<div class="list-group shadow-sm mb-3">
	@forelse($users as $user)
		<div class="list-group-item user-item d-flex align-items-center gap-3 py-3 position-relative">
			<a href="{{ route('system_admin.users.show',$user) }}" class="position-absolute top-0 start-0 w-100 h-100" style="z-index:1;" aria-label="Open user" ></a>
			<img src="{{ $user->avatar_url }}" class="rounded-circle flex-shrink-0 user-avatar" alt="avatar" loading="lazy">
			<div class="flex-grow-1">
				<div class="d-flex flex-wrap align-items-center gap-2 mb-1">
					<span class="fw-semibold">{{ $user->name }}</span>
					<span class="badge bg-secondary text-capitalize">{{ $user->role }}</span>
				</div>
				<div class="text-muted small d-flex flex-wrap gap-3">
					<span><span class="bi bi-envelope me-1"></span>{{ $user->email }}</span>
					@if($user->institution_id)
						<span><span class="bi bi-building me-1"></span>{{ $user->institution?->name }}</span>
					@endif
					<span><span class="bi bi-hash me-1"></span>ID {{ $user->id }}</span>
				</div>
			</div>
			<div class="dropdown ms-1 position-relative" style="z-index:2;">
				<button class="btn btn-sm btn-outline-secondary border-0" data-bs-toggle="dropdown" aria-expanded="false" type="button"><span class="bi bi-three-dots"></span></button>
				<div class="dropdown-menu dropdown-menu-end shadow-sm">
					<a class="dropdown-item" href="{{ route('system_admin.users.edit',$user) }}"><span class="bi bi-pencil-square me-2"></span>Edit</a>
					@if($user->role !== 'developer')
						<button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}">
							<span class="bi bi-trash me-2"></span>Delete
						</button>
					@endif
				</div>
			</div>
		</div>
	@empty
		<div class="list-group-item text-center text-muted py-5">No users found.</div>
	@endforelse
</div>
<div class="d-flex justify-content-center">{{ $users->links('components.pagination.simple') }}</div>
