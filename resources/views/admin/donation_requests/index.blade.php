<x-app-layout>
    <x-slot name="header">
        <h2 class="h5 mb-0 text-primary d-flex align-items-center gap-2">
            <i class="bi bi-heart"></i> Donation Requests
        </h2>
    </x-slot>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Donor</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $r)
                        <tr class="position-relative">
                            <td class="ps-4">
                                <div class="fw-semibold">{{ $r->donor_name }}</div>
                                <div class="small text-muted">{{ $r->donor_email }}</div>
                                @if($r->donor_phone)<div class="small text-muted"><i class="bi bi-telephone me-1"></i>{{ $r->donor_phone }}</div>@endif
                            </td>
                            <td>
                                <div class="fw-bold">₹{{ number_format($r->amount) }}</div>
                                <div class="small text-muted">{{ $r->details['meal_type'] ?? 'Custom' }}</div>
                            </td>
                            <td>
                                @php
                                    $statusClass = match($r->status){
                                        'pending' => 'bg-warning-subtle text-warning-emphasis',
                                        'contacted' => 'bg-info-subtle text-info-emphasis',
                                        'completed' => 'bg-success-subtle text-success-emphasis',
                                        'cancelled' => 'bg-danger-subtle text-danger-emphasis',
                                        default => 'bg-secondary-subtle text-secondary-emphasis'
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }} text-capitalize rounded-pill">{{ $r->status }}</span>
                            </td>
                            <td class="small text-muted">{{ $r->created_at->format('M d, Y') }}<br>{{ $r->created_at->format('h:i A') }}</td>
                            <td class="text-end pe-4 position-relative z-2">
                                <a href="{{ route('admin.donation-requests.show', $r) }}" class="btn btn-sm btn-outline-primary border-0"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No donation requests found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($requests->hasPages())
        <div class="card-footer bg-white border-0">
            {{ $requests->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
