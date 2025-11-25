<x-app-layout>
	<x-slot name="header">
		<h2 class="h5 mb-0">Institutions Management</h2>
	</x-slot>
	@if (session('success'))
		<div class="alert alert-success mb-3">{{ session('success') }}</div>
	@endif
	@if (session('error'))
		<div class="alert alert-danger mb-3">{{ session('error') }}</div>
	@endif
	<div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-2">
		<h1 class="h5 mb-0">Institutions</h1>
		<div class="d-flex align-items-center gap-2">
			<button class="btn btn-outline-secondary d-inline-flex d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse" aria-expanded="false" aria-controls="filtersCollapse" title="Filters"><span class="bi bi-funnel"></span></button>
			<a href="{{ route('system_admin.institutions.create') }}" class="btn btn-primary"><span class="bi bi-plus-lg me-1"></span>Add</a>
		</div>
	</div>
	<form method="GET" action="{{ route('system_admin.institutions.index') }}" class="card card-body mb-3 shadow-sm small collapse d-md-block" id="filtersCollapse">
		<div class="row g-2 align-items-end">
			<div class="col-12 col-md-6 col-lg-4">
				<label class="form-label mb-0">Search</label>
				<input type="text" name="search" value="{{ request('search') }}" placeholder="Institution name" class="form-control form-control-sm" />
			</div>
			<div class="col-6 col-md-4 col-lg-3">
				<label class="form-label mb-0">Sort</label>
				<select name="sort" class="form-select form-select-sm">
					<option value="created_desc" @selected(request('sort')=='created_desc')>Newest</option>
					<option value="created_asc" @selected(request('sort')=='created_asc')>Oldest</option>
					<option value="name_asc" @selected(request('sort')=='name_asc')>Name A-Z</option>
					<option value="name_desc" @selected(request('sort')=='name_desc')>Name Z-A</option>
				</select>
			</div>
			<div class="col-6 col-md-4 col-lg-2 d-flex gap-2">
				<button class="btn btn-secondary btn-sm flex-grow-1"><span class="bi bi-filter me-1"></span>Apply</button>
				<a href="{{ route('system_admin.institutions.index') }}" class="btn btn-light btn-sm">Reset</a>
			</div>
		</div>
	</form>
	<div class="list-group shadow-sm mb-4">
		@forelse($institutions as $institution)
			<div class="list-group-item d-flex gap-3 align-items-start position-relative py-3">
				<a href="{{ route('system_admin.institutions.show',$institution) }}" class="position-absolute top-0 start-0 w-100 h-100" style="z-index:1;" aria-label="Open institution"></a>
				<div class="flex-grow-1">
					<div class="d-flex align-items-center gap-2 mb-1">
						<span class="fw-semibold">{{ $institution->name }}</span>
						<span class="badge bg-secondary">#{{ $institution->id }}</span>
						<span class="badge text-bg-light border"><span class="bi bi-people me-1"></span>{{ $institution->users_count }}</span>
						<span class="badge text-bg-light border"><span class="bi bi-person-bounding-box me-1"></span>{{ $institution->inmates_count }}</span>
					</div>
					<div class="text-muted small">{{ Str::limit($institution->address, 80) ?: 'No address' }}</div>
				</div>
				<div class="dropdown ms-auto position-relative" style="z-index:2;">
					<button class="btn btn-sm btn-outline-secondary border-0" data-bs-toggle="dropdown" type="button"><span class="bi bi-three-dots"></span></button>
					<div class="dropdown-menu dropdown-menu-end shadow-sm">
						<a href="{{ route('system_admin.institutions.show',$institution) }}" class="dropdown-item"><span class="bi bi-building me-2"></span>Open</a>
						<a href="{{ route('system_admin.institutions.edit',$institution) }}" class="dropdown-item"><span class="bi bi-pencil-square me-2"></span>Edit</a>
						<button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteInstitutionModal" data-inst-id="{{ $institution->id }}" data-inst-name="{{ $institution->name }}"><span class="bi bi-trash me-2"></span>Delete</button>
					</div>
				</div>
			</div>
		@empty
			<div class="list-group-item text-center text-muted py-5">No institutions found.</div>
		@endforelse
	</div>
</x-app-layout>
<!-- Delete Institution Modal -->
<div class="modal fade" id="deleteInstitutionModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<form method="POST" id="deleteInstitutionForm">
				@csrf
				@method('DELETE')
				<div class="modal-header bg-danger text-white">
					<h5 class="modal-title">Delete Institution</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<p class="mb-2 fw-semibold text-danger">This action is permanent.</p>
					<p id="institutionDeleteWarning" class="small mb-3"></p>
					<div class="form-check">
						<input class="form-check-input" type="checkbox" value="1" id="deleteUsersCheckbox" name="delete_users">
						<label class="form-check-label" for="deleteUsersCheckbox">
							Also delete all non-developer users belonging to this institution.
						</label>
					</div>
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
	const modal = document.getElementById('deleteInstitutionModal');
	modal.addEventListener('show.bs.modal', function (event) {
		const button = event.relatedTarget;
		const instId = button.getAttribute('data-inst-id');
		const instName = button.getAttribute('data-inst-name');
	const form = document.getElementById('deleteInstitutionForm');
	form.action = '{{ url('system-admin/institutions') }}/' + instId;
		document.getElementById('institutionDeleteWarning').innerText = `Delete institution "${instName}"? Users will remain unless you check the box below.`;
		document.getElementById('deleteUsersCheckbox').checked = false;
	});
});
</script>
