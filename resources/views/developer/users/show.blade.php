<x-app-layout>
    <x-slot name="header">
        <h2 class="h5 mb-0">User Profile</h2>
    </x-slot>

    <div class="d-flex flex-wrap gap-4 align-items-start mb-4">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ $user->avatar_url }}" alt="avatar" class="rounded-circle shadow" style="width:90px;height:90px;object-fit:cover;">
            <div>
                <h1 class="h4 mb-1">{{ $user->name }}</h1>
                <div class="text-muted small mb-1">{{ $user->email }}</div>
                <span class="badge bg-secondary text-capitalize">{{ $user->role }}</span>
                @if($user->can_report_bugs)
                    <span class="badge bg-info ms-1">Bug Reports</span>
                @endif
            </div>
        </div>
        <div class="ms-auto d-flex flex-wrap gap-2">
            <a href="{{ route('developer.users.edit',$user) }}" class="btn btn-outline-primary"><span class="bi bi-pencil-square me-1"></span>Edit</a>
            @if($user->role !== 'developer')
            <form method="POST" action="{{ route('developer.users.destroy',$user) }}" onsubmit="return confirm('Delete this user?');">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger"><span class="bi bi-trash me-1"></span>Delete</button>
            </form>
            @endif
            <button type="button" class="btn btn-outline-secondary" id="toggleBugBtn" data-id="{{ $user->id }}">
                <span class="bi bi-bug me-1"></span><span id="bugBtnLabel">{{ $user->can_report_bugs ? 'Disable Bug Reports' : 'Enable Bug Reports' }}</span>
            </button>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header">Meta</div>
                <div class="card-body small">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">ID</dt><dd class="col-sm-8">{{ $user->id }}</dd>
                        <dt class="col-sm-4">Institution</dt><dd class="col-sm-8">{{ $user->institution?->name ?? '—' }}</dd>
                        <dt class="col-sm-4">Created</dt><dd class="col-sm-8">{{ $user->created_at?->format('Y-m-d H:i') }}</dd>
                        <dt class="col-sm-4">Updated</dt><dd class="col-sm-8">{{ $user->updated_at?->format('Y-m-d H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Quick Actions</span>
                </div>
                <div class="card-body small">
                    <p class="text-muted">Use these administrative actions for this account.</p>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><a href="{{ route('developer.users.edit',$user) }}" class="text-decoration-none"><span class="bi bi-pencil-square me-2"></span>Edit Account Details</a></li>
                        @if($user->role !== 'developer')
                        <li class="mb-2"><a href="#" onclick="if(confirm('Delete this user?')) document.querySelector('#deleteInlineForm').submit(); return false;" class="text-decoration-none text-danger"><span class="bi bi-trash me-2"></span>Delete Account</a></li>
                        @endif
                        <li class="mb-2"><a href="{{ route('developer.users.index') }}" class="text-decoration-none"><span class="bi bi-arrow-left-circle me-2"></span>Back to List</a></li>
                    </ul>
                    @if($user->role !== 'developer')
                    <form id="deleteInlineForm" method="POST" action="{{ route('developer.users.destroy',$user) }}" class="d-none">@csrf @method('DELETE')</form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const btn = document.getElementById('toggleBugBtn');
        btn?.addEventListener('click', function(){
            const id = this.getAttribute('data-id');
            fetch(`{{ url('developer/users') }}/${id}/toggle-bug-reporting`, {
                method:'POST', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'}
            }).then(r=>r.json()).then(data=>{
                if(data.ok){
                    const label = document.getElementById('bugBtnLabel');
                    label.textContent = data.can_report_bugs ? 'Disable Bug Reports' : 'Enable Bug Reports';
                }
            });
        });
    });
    </script>
</x-app-layout>
