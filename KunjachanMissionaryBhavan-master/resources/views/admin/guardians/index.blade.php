<x-app-layout>
    <x-slot name="header"><h2 class="h5 mb-0">Guardians (Institution)</h2></x-slot>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h1 class="h4 mb-0">Guardians</h1>
        <a href="{{ route('admin.guardians.create') }}" class="btn btn-primary"><span class="bi bi-plus-lg me-1"></span>Add</a>
    </div>
    @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    <style>.guardian-item{transition:.15s;}.guardian-item:hover{background:#f8f9fa;box-shadow:0 2px 6px rgba(0,0,0,.08);} </style>
        <div class="list-group shadow-sm mb-4" id="adminGuardianList">
                @forelse($guardians as $g)
                        <div class="list-group-item guardian-item d-flex gap-3 align-items-start position-relative py-3 guardian-row" data-href="{{ route('admin.guardians.show',$g) }}">
                <div class="flex-grow-1">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                        <span class="fw-semibold">{{ $g->full_name }}</span>
                        <span class="badge bg-secondary">ID {{ $g->id }}</span>
                        @if($g->inmate)
                            <span class="badge bg-info text-dark">Inmate: {{ $g->inmate->full_name }}</span>
                        @endif
                    </div>
                    <div class="text-muted small d-flex flex-wrap gap-3">
                        <span><span class="bi bi-telephone me-1"></span>{{ $g->phone_number ?: '—' }}</span>
                        <span><span class="bi bi-geo me-1"></span>{{ Str::limit($g->address,50) ?: '—' }}</span>
                    </div>
                </div>
                                <div class="dropdown ms-auto position-relative" style="z-index:2;" onclick="event.stopPropagation()">
                    <button class="btn btn-sm btn-outline-secondary border-0" data-bs-toggle="dropdown" type="button"><span class="bi bi-three-dots"></span></button>
                    <div class="dropdown-menu dropdown-menu-end shadow-sm">
                        <a href="{{ route('admin.guardians.edit',$g) }}" class="dropdown-item"><span class="bi bi-pencil-square me-2"></span>Edit</a>
                        <form action="{{ route('admin.guardians.destroy',$g) }}" method="POST" onsubmit="return confirm('Delete guardian?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger"><span class="bi bi-trash me-2"></span>Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="list-group-item text-center text-muted py-5">No guardians found.</div>
        @endforelse
    </div>
    <div class="d-flex justify-content-center">{{ $guardians->links() }}</div>
</x-app-layout>

@push('scripts')
<script>
    document.addEventListener('click', function(e){
        const row = e.target.closest('.guardian-row');
        if(row){ window.location.href = row.getAttribute('data-href'); }
    });
</script>
@endpush
