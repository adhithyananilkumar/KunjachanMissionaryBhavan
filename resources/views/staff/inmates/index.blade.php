<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">Inmates</h2>
    </x-slot>

    <div class="card shadow-sm">
        <div class="list-group list-group-flush">
            @forelse($inmates as $inmate)
                <a href="{{ route('staff.inmates.show',$inmate) }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between text-decoration-none">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ $inmate->avatar_url }}" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;" alt="avatar">
                        <div>
                            <div class="fw-semibold">{{ $inmate->full_name ?? $inmate->first_name }}</div>
                            <div class="text-muted small">DOB {{ optional($inmate->date_of_birth)->format('Y-m-d') ?: '—' }} · Admitted {{ optional($inmate->admission_date)->format('Y-m-d') ?: '—' }}</div>
                        </div>
                    </div>
                    <span class="badge text-bg-light text-capitalize">{{ $inmate->gender ?: '—' }}</span>
                </a>
            @empty
                <div class="list-group-item text-center text-muted py-4">No inmates found.</div>
            @endforelse
        </div>
    @if($inmates instanceof \Illuminate\Pagination\AbstractPaginator)
            <div class="card-footer py-2">
                {{ $inmates->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
