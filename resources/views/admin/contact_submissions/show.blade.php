<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0 text-primary d-flex align-items-center gap-2">
                <i class="bi bi-envelope-open"></i> Message Details
            </h2>
            <a href="{{ route('admin.contact-submissions.index') }}" class="btn btn-outline-secondary btn-sm">Back to Inbox</a>
        </div>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="mb-1">{{ $submission->name }}</h5>
                            <div class="text-muted small">
                                <a href="mailto:{{ $submission->email }}" class="text-decoration-none">{{ $submission->email }}</a>
                                @if($submission->phone) • {{ $submission->phone }} @endif
                            </div>
                        </div>
                        <div class="text-end text-muted small">
                            <div>{{ $submission->created_at->format('M d, Y h:i A') }}</div>
                            <div>{{ $submission->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="bg-light p-3 rounded" style="white-space: pre-wrap; font-family: inherit;">{{ $submission->message }}</div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                     <span class="text-muted small">IP: {{ $submission->ip_address }}</span>
                     <form method="POST" action="{{ route('admin.contact-submissions.destroy', $submission) }}" onsubmit="return confirm('Delete this message?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm"><i class="bi bi-trash me-1"></i> Delete</button>
                    </form>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="mailto:{{ $submission->email }}?subject=Re: Contact Message - Kunjachan Missionary Bhavan" class="btn btn-primary px-4">
                    <i class="bi bi-reply-fill me-1"></i> Reply via Email
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
