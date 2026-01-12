<x-app-layout>
    <x-slot name="header"><h2 class="h5 mb-0">Patients</h2></x-slot>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form class="row g-2 align-items-end" method="GET" action="{{ route('doctor.inmates.index') }}">
                <div class="col-12 col-md-8">
                    <label class="form-label small text-muted">Search</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="search" class="form-control" name="q" value="{{ $search ?? request('q') }}" placeholder="Name or Registration #">
                    </div>
                </div>
                <div class="col-12 col-md-4 d-grid">
                    <button class="btn btn-primary"><span class="bi bi-funnel me-1"></span>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="list-group shadow-sm">
        @forelse($inmates as $inmate)
            <div class="list-group-item d-flex align-items-center gap-3 position-relative">
                <img src="{{ $inmate->avatar_url }}" class="rounded-circle" style="width:44px;height:44px;object-fit:cover;" alt="avatar">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="fw-semibold">{{ $inmate->full_name }}</span>
                        @if($inmate->registration_number)
                            <span class="badge bg-light border text-dark">#{{ $inmate->registration_number }}</span>
                        @endif
                    </div>
                    <div class="text-muted small">Admitted {{ optional($inmate->admission_date)->format('Y-m-d') ?: '—' }}</div>
                </div>
                <a class="stretched-link" href="{{ route('doctor.inmates.show',$inmate) }}" aria-label="Open"></a>
                <i class="bi bi-chevron-right text-muted"></i>
            </div>
        @empty
            <div class="list-group-item text-center text-muted py-4">No patients found.</div>
        @endforelse
    </div>
    @if($inmates->hasPages())
        <div class="d-flex justify-content-center mt-3">{{ $inmates->appends(request()->query())->links() }}</div>
    @endif

    <style>
        .list-group-item{transition:background .15s ease, transform .15s ease}
        .list-group-item:hover{background:#f8f9fa; transform: translateY(-2px)}
    </style>
</x-app-layout>
