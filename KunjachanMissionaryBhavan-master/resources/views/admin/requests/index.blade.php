<x-app-layout>
    <x-slot name="header"><h2 class="h5 mb-0">My Action Requests</h2></x-slot>

    <div class="card shadow-sm">
        <div class="list-group list-group-flush">
            @forelse($requests as $r)
                @php
                    $statusClass = 'secondary';
                    if($r->status === 'pending') $statusClass = 'warning';
                    elseif(strtolower($r->status) === 'approved' || strtolower($r->status) === 'resolved') $statusClass='success';
                    elseif(strtolower($r->status) === 'rejected') $statusClass='danger';
                @endphp
                <div class="list-group-item d-flex align-items-start justify-content-between gap-3">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2">
                                <div class="fw-semibold">#{{ $r->id }}&nbsp;{{ $r->subject }}</div>
                            <span class="badge text-bg-{{ $statusClass }} text-uppercase">{{ $r->status }}</span>
                        </div>
                        <div class="small text-muted">Created {{ $r->created_at->diffForHumans() }}</div>
                        @if($r->developer_reply)
                            <div class="mt-2 small" style="white-space:pre-wrap">{{ $r->developer_reply }}</div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="list-group-item text-center text-muted py-4">No requests yet.</div>
            @endforelse
        </div>
        @if($requests->hasPages())<div class="card-footer">{{ $requests->links() }}</div>@endif
    </div>
</x-app-layout>
