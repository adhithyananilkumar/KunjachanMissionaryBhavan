<x-app-layout>
	<x-slot name="header">
		<h2 class="h5 mb-0">User Management</h2>
	</x-slot>
	<div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
		<h1 class="h4 mb-0">Users</h1>
		<a href="{{ route('system_admin.users.create') }}" class="btn btn-primary btn-sm">New User</a>
	</div>
	<div class="d-lg-none d-flex justify-content-end mb-2">
		<button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#userFilters"><span class="bi bi-funnel me-1"></span>Filters</button>
	</div>
	<form method="GET" action="{{ route('system_admin.users.index') }}" class="card card-body mb-3 shadow-sm small collapse d-lg-block" id="userFilters">
		<div class="row g-2 align-items-end">
			<div class="col-md-3">
				<label class="form-label mb-0">Search</label>
				<input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="name or email">
			</div>
	    <div class="col-md-2">
				<label class="form-label mb-0">Role</label>
				<select name="role" class="form-select form-select-sm">
					<option value="">All</option>
		    <option value="system_admin" @selected(request('role')=='system_admin')>System Admin</option>
		    <option value="admin" @selected(request('role')=='admin')>Admin</option>
		    <option value="doctor" @selected(request('role')=='doctor')>Doctor</option>
		    <option value="nurse" @selected(request('role')=='nurse')>Nurse</option>
		    <option value="staff" @selected(request('role')=='staff')>Staff</option>
		    <option value="guardian" @selected(request('role')=='guardian')>Guardian</option>
				</select>
			</div>
			<div class="col-md-3">
				<label class="form-label mb-0">Institution</label>
				<select name="institution_id" class="form-select form-select-sm">
					<option value="">All</option>
					@foreach($institutions as $inst)
						<option value="{{ $inst->id }}" @selected(request('institution_id')==$inst->id)>{{ $inst->name }}</option>
					@endforeach
				</select>
			</div>
			<div class="col-md-2">
				<label class="form-label mb-0">Sort</label>
				<select name="sort" class="form-select form-select-sm">
					<option value="created_desc" @selected(request('sort')=='created_desc')>Newest</option>
					<option value="created_asc" @selected(request('sort')=='created_asc')>Oldest</option>
					<option value="name_asc" @selected(request('sort')=='name_asc')>Name A-Z</option>
					<option value="name_desc" @selected(request('sort')=='name_desc')>Name Z-A</option>
				</select>
			</div>
			<div class="col-md-2 d-flex gap-2">
				<button class="btn btn-secondary btn-sm flex-grow-1">Apply</button>
				<a href="{{ route('system_admin.users.index') }}" class="btn btn-light btn-sm">Reset</a>
			</div>
		</div>
	</form>
	<style>
		.user-item{transition:background-color .15s ease, box-shadow .15s ease, transform .15s ease;}
		.user-item:hover{background:#f8f9fa; box-shadow:0 2px 6px rgba(0,0,0,0.08); transform:translateY(-2px);} 
		.user-avatar{width:52px;height:52px;object-fit:cover;border:2px solid #fff;box-shadow:0 0 0 1px rgba(0,0,0,.1);} 
	</style>
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
</x-app-layout>
<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<form method="POST" id="deleteUserForm">
				@csrf
				@method('DELETE')
				<div class="modal-header bg-danger text-white">
					<h5 class="modal-title">Delete User</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<p class="mb-2 fw-semibold text-danger">Permanent deletion.</p>
					<p id="userDeleteWarning" class="small mb-0"></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-danger">Delete</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
	const modal = document.getElementById('deleteUserModal');
	modal.addEventListener('show.bs.modal', function(event){
		const button = event.relatedTarget;
		const userId = button.getAttribute('data-user-id');
		const userName = button.getAttribute('data-user-name');
		const form = document.getElementById('deleteUserForm');
		form.action = '{{ url('system-admin/users') }}/' + userId;
		document.getElementById('userDeleteWarning').innerText = `Delete user "${userName}"? This cannot be undone.`;
	});

	// No admin bug toggles here
});
</script>
