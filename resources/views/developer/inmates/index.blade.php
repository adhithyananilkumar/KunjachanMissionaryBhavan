<x-app-layout>
	<x-slot name="header">
		<h2 class="h5 mb-0">Inmate Management</h2>
	</x-slot>
	<div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
		<h1 class="h4 mb-0">Inmates</h1>
		<a href="{{ route('developer.inmates.create') }}" class="btn btn-primary btn-sm"><span class="bi bi-plus-lg me-1"></span>Register</a>
	</div>

	<form method="GET" action="{{ route('developer.inmates.index') }}" class="card card-body mb-4 shadow-sm small">
		<div class="row g-2 align-items-end">
			<div class="col-md-3">
				<label class="form-label mb-0">Search</label>
				<input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="name or id">
			</div>
			<div class="col-md-2">
				<label class="form-label mb-0">Type</label>
				<select name="type" class="form-select form-select-sm">
					<option value="">All</option>
					<option value="juvenile" @selected(request('type')=='juvenile')>Juvenile</option>
					<option value="adult" @selected(request('type')=='adult')>Adult</option>
					<option value="senior" @selected(request('type')=='senior')>Senior</option>
				</select>
			</div>
			<div class="col-md-3">
				<label class="form-label mb-0">Institution</label>
				<select name="institution_id" class="form-select form-select-sm">
					<option value="">All</option>
					@foreach(($institutions ?? []) as $inst)
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
				<a href="{{ route('developer.inmates.index') }}" class="btn btn-light btn-sm">Reset</a>
			</div>
		</div>
	</form>
	<style>
		.inmate-item{transition:background-color .15s ease, box-shadow .15s ease, transform .15s ease;}
		.inmate-item:hover{background:#f8f9fa; box-shadow:0 2px 6px rgba(0,0,0,0.08); transform:translateY(-2px);} 
		.inmate-avatar{width:52px;height:52px;object-fit:cover;border:2px solid #fff;box-shadow:0 0 0 1px rgba(0,0,0,.1);}
	</style>
	<div class="list-group shadow-sm mb-4">
		@forelse($inmates as $inmate)
			<div class="list-group-item inmate-item d-flex gap-3 align-items-center position-relative py-3">
				<a href="{{ route('developer.inmates.show',$inmate) }}" class="position-absolute top-0 start-0 w-100 h-100" style="z-index:1;" aria-label="Open inmate"></a>
				<img src="{{ $inmate->avatar_url }}" alt="avatar" class="rounded-circle inmate-avatar shadow-sm" loading="lazy">
				<div class="flex-grow-1">
					<div class="d-flex flex-wrap align-items-center gap-2 mb-1">
						<span class="fw-semibold">{{ $inmate->full_name }}</span>
						<span class="badge bg-secondary">ID {{ $inmate->id }}</span>
						@if($inmate->admission_date)
							<span class="badge bg-info text-dark">Admitted {{ $inmate->admission_date->format('Y-m-d') }}</span>
						@endif
					</div>
					<div class="text-muted small d-flex flex-wrap gap-3">
						<span><span class="bi bi-building me-1"></span>{{ $inmate->institution->name ?? 'No Institution' }}</span>
						<span><span class="bi bi-calendar-event me-1"></span>DOB {{ $inmate->date_of_birth?->format('Y-m-d') ?? '—' }}</span>
						@if($inmate->gender)
							<span><span class="bi bi-person-badge me-1"></span>{{ $inmate->gender }}</span>
						@endif
					</div>
				</div>
				<div class="dropdown ms-auto position-relative" style="z-index:2;">
					<button class="btn btn-sm btn-outline-secondary border-0" data-bs-toggle="dropdown" type="button"><span class="bi bi-three-dots"></span></button>
					<div class="dropdown-menu dropdown-menu-end shadow-sm">
						<a href="{{ route('developer.inmates.edit',$inmate) }}" class="dropdown-item"><span class="bi bi-pencil-square me-2"></span>Edit</a>
						<form action="{{ route('developer.inmates.destroy',$inmate) }}" method="POST" onsubmit="return confirm('Delete this inmate?');">
							@csrf @method('DELETE')
							<button type="submit" class="dropdown-item text-danger"><span class="bi bi-trash me-2"></span>Delete</button>
						</form>
					</div>
				</div>
			</div>
		@empty
			<div class="list-group-item text-center text-muted py-5">No inmates found.</div>
		@endforelse
	</div>
	<div class="d-flex justify-content-center">{{ $inmates->links() }}</div>
</x-app-layout>
