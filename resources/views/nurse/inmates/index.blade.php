<x-app-layout>
    <x-slot name="header"><h2 class="h5 mb-0">Patients</h2></x-slot>

    <div class="card shadow-sm">
        <div class="list-group list-group-flush">
            @forelse($inmates as $inmate)
                <a href="{{ route('nurse.inmates.show',$inmate) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-3">
                    <div class="text-muted small" style="width:64px">#{{ $inmate->id }}</div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">{{ $inmate->full_name }}</div>
                        <div class="text-muted small">Admitted {{ optional($inmate->admission_date)->format('Y-m-d') ?: '—' }}</div>
                    </div>
                    <span class="bi bi-chevron-right text-muted"></span>
                </a>
            @empty
                <div class="list-group-item text-center text-muted py-4">No patients.</div>
            @endforelse
        </div>
        @if($inmates->hasPages())
            <div class="card-footer">{{ $inmates->links() }}</div>
        @endif
    </div>
</x-app-layout>
