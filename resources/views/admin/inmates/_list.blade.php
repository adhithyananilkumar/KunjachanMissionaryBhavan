<div class="list-group shadow-sm mb-4">
	@forelse($inmates as $inmate)
		<div class="list-group-item inmate-item d-flex gap-3 align-items-center position-relative py-3">
			<a href="{{ route('admin.inmates.show',$inmate) }}" class="position-absolute top-0 start-0 w-100 h-100" style="z-index:1;" aria-label="Open inmate"></a>
			<img src="{{ $inmate->avatar_url }}" alt="avatar" class="rounded-circle inmate-avatar shadow-sm" loading="lazy">
			<div class="flex-grow-1" style="min-width:0;">
				<div class="d-flex flex-wrap align-items-center gap-2 mb-1">
					<span class="fw-semibold text-truncate d-inline-block" style="max-width:100%">{{ $inmate->full_name }}</span>
					<span class="badge bg-secondary">ID {{ $inmate->id }}</span>
					@if($inmate->admission_date)
						<span class="badge bg-info text-dark">Admitted {{ $inmate->admission_date->format('Y-m-d') }}</span>
					@endif
				</div>
				<div class="text-muted small d-flex flex-wrap gap-3 inmate-meta">
					<span class="hide-xs"><span class="bi bi-calendar-event me-1"></span>DOB {{ $inmate->date_of_birth?->format('Y-m-d') ?? '—' }}</span>
					@if($inmate->gender)
						<span class="hide-xs"><span class="bi bi-person-badge me-1"></span>{{ $inmate->gender }}</span>
					@endif
				</div>
			</div>
			<div class="ms-auto position-relative d-flex align-items-center gap-1" style="z-index:2;">
				<div class="dropdown">
					<button class="btn btn-sm btn-outline-secondary border-0" data-bs-toggle="dropdown" type="button"><span class="bi bi-three-dots"></span></button>
					<div class="dropdown-menu dropdown-menu-end shadow-sm">
						<a class="dropdown-item" href="{{ route('admin.inmates.edit',$inmate) }}"><span class="bi bi-pencil-square me-2"></span>Edit</a>
						<form action="{{ route('admin.inmates.destroy',$inmate) }}" method="POST" onsubmit="return confirm('Delete this inmate?');">@csrf @method('DELETE')<button type="submit" class="dropdown-item text-danger"><span class="bi bi-trash me-2"></span>Delete</button></form>
					</div>
				</div>
			</div>
		</div>
	@empty
		<div class="list-group-item text-center text-muted py-5">No inmates found.</div>
	@endforelse
</div>
<div class="d-flex justify-content-center">{{ $inmates->links() }}</div>
