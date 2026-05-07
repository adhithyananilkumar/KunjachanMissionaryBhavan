<x-app-layout>
    <x-slot name="header"><h2 class="h5 mb-0">Guardians</h2></x-slot>
    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <a href="{{ route('system_admin.guardians.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Guardian</a>
        <form method="GET" action="{{ route('system_admin.guardians.index') }}" class="d-flex flex-wrap align-items-center gap-2 ms-auto" id="filters">
            <div class="input-group input-group-sm" style="max-width: 260px;">
                <span class="input-group-text d-none d-sm-inline">Search</span>
                <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="name or phone">
            </div>
            <select name="institution_id" class="form-select form-select-sm" style="min-width: 160px;">
                <option value="">All institutions</option>
                @foreach($institutions as $ins)
                    <option value="{{ $ins->id }}" @selected(($institutionId ?? null)==$ins->id)>{{ $ins->name }}</option>
                @endforeach
            </select>
            <select name="sort" class="form-select form-select-sm" style="min-width: 140px;">
                <option value="created_desc" @selected(($sort ?? '')=='created_desc')>Newest</option>
                <option value="created_asc" @selected(($sort ?? '')=='created_asc')>Oldest</option>
                <option value="name_asc" @selected(($sort ?? '')=='name_asc')>Name A-Z</option>
                <option value="name_desc" @selected(($sort ?? '')=='name_desc')>Name Z-A</option>
            </select>
            <button class="btn btn-secondary btn-sm">Apply</button>
            <a href="{{ route('system_admin.guardians.index') }}" class="btn btn-light btn-sm">Reset</a>
        </form>
    </div>

    <div class="card">
        <div class="list-group list-group-flush" id="guardianList">
            @forelse($guardians as $g)
                @php
                    $parts = preg_split('/\s+/', trim($g->full_name));
                    $initials = strtoupper(substr($parts[0]??'',0,1).substr($parts[1]??'',0,1));
                @endphp
                <div class="list-group-item py-3 guardian-row" data-href="{{ route('system_admin.guardians.show',$g) }}">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width:40px;height:40px;font-weight:600;">
                            {{ $initials ?: 'GU' }}
                        </div>
                        <div class="flex-fill">
                            <div class="d-flex align-items-center gap-2">
                                <div class="fw-semibold">{{ $g->full_name }}</div>
                                @if($g->inmate?->institution)
                                    <span class="badge bg-light text-dark small">{{ $g->inmate->institution->name }}</span>
                                @endif
                            </div>
                            <div class="small text-muted mt-1">
                                <span class="me-3"><i class="bi bi-telephone me-1"></i>{{ $g->phone_number ?: '—' }}</span>
                                <span><i class="bi bi-geo-alt me-1"></i>{{ $g->address ?: '—' }}</span>
                            </div>
                            @if($g->inmate)
                                <div class="small mt-1">Inmate: <a href="{{ route('system_admin.inmates.show',$g->inmate) }}" onclick="event.stopPropagation()">{{ $g->inmate->full_name }}</a></div>
                            @endif
                        </div>
                        <div class="ms-2 dropdown" onclick="event.stopPropagation()">
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-three-dots"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('system_admin.guardians.edit',$g) }}">Edit</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-muted">No guardians found.</div>
            @endforelse
        </div>
        <div class="card-footer">{{ $guardians->withQueryString()->links() }}</div>
    </div>
</x-app-layout>

@push('scripts')
<script>
  document.addEventListener('click', function(e){
    const row = e.target.closest('.guardian-row');
    if(row){ window.location.href = row.getAttribute('data-href'); }
  });
</script>
@endpush
