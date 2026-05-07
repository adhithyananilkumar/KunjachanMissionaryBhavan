<x-app-layout>
    <x-slot name="header"><h2 class="h5 mb-0">Lab Tests</h2></x-slot>
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light"><tr><th>Patient</th><th>Test</th><th>Ordered</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @forelse($tests as $t)
                    <tr>
                        <td>{{ $t->inmate?->full_name ?? '—' }}</td>
                        <td class="fw-semibold">{{ $t->test_name }}</td>
                        <td>{{ optional($t->ordered_date)->format('Y-m-d') ?: '—' }}</td>
                        <td class="text-capitalize">{{ str_replace('_',' ',$t->status) }}</td>
                        <td class="text-end"><a href="{{ route('staff.lab-tests.show',$t) }}" class="btn btn-sm btn-outline-secondary">Open</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No lab tests.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($tests instanceof \Illuminate\Pagination\AbstractPaginator)
            <div class="card-footer py-2">{{ $tests->links() }}</div>
        @endif
    </div>
</x-app-layout>
