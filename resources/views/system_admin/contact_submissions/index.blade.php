<x-app-layout>
    <x-slot name="header">
        <h2 class="h5 mb-0 text-primary d-flex align-items-center gap-2">
            <i class="bi bi-envelope-paper"></i> Contact Submissions
        </h2>
    </x-slot>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Sender</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $s)
                        <tr class="position-relative">
                            <td class="ps-4">
                                <div class="fw-semibold">{{ $s->name }}</div>
                                <div class="small text-muted">{{ $s->email }}</div>
                            </td>
                            <td>
                                <a href="{{ route('system_admin.contact-submissions.show', $s) }}" class="text-decoration-none text-dark stretched-link">
                                    <div class="text-truncate" style="max-width: 400px;">{{ $s->message }}</div>
                                </a>
                            </td>
                            <td class="small text-muted" style="white-space:nowrap">{{ $s->created_at->format('M d, Y') }}<br><span class="text-xs">{{ $s->created_at->format('h:i A') }}</span></td>
                            <td class="text-end pe-4 position-relative z-2">
                                <form method="POST" action="{{ route('system_admin.contact-submissions.destroy', $s) }}" onsubmit="return confirm('Delete this message?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger border-0"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">No messages found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($submissions->hasPages())
        <div class="card-footer bg-white border-0">
            {{ $submissions->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
