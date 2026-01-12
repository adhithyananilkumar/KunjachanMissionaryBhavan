<x-app-layout>
    <x-slot name="header"><h2 class="h5 mb-0">User Profile</h2></x-slot>
    <div class="d-flex flex-wrap gap-4 align-items-start mb-4">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ $user->avatar_url }}" alt="avatar" class="rounded-circle shadow" style="width:90px;height:90px;object-fit:cover;">
            <div>
                <h1 class="h4 mb-1">{{ $user->name }}</h1>
                <div class="text-muted small mb-1">{{ $user->email }}</div>
                <span class="badge bg-secondary text-capitalize">{{ $user->role }}</span>
            </div>
        </div>
        <div class="ms-auto d-flex flex-wrap gap-2">
            <a href="{{ route('admin.staff.edit',$user) }}" class="btn btn-primary"><span class="bi bi-pencil-square me-1"></span>Edit</a>
            <form method="POST" action="{{ route('admin.staff.destroy',$user) }}" onsubmit="return confirm('Delete this user?');">@csrf @method('DELETE')
                <button class="btn btn-outline-danger"><span class="bi bi-trash me-1"></span>Delete</button>
            </form>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header">Meta</div>
                <div class="card-body small">
                    <dl class="row mb-0">
                        <dt class="col-5 col-sm-4">ID</dt><dd class="col-7 col-sm-8">{{ $user->id }}</dd>
                        <dt class="col-5 col-sm-4">Institution</dt><dd class="col-7 col-sm-8">{{ $user->institution?->name ?? '—' }}</dd>
                        <dt class="col-5 col-sm-4">Created</dt><dd class="col-7 col-sm-8">{{ $user->created_at?->format('Y-m-d H:i') }}</dd>
                        <dt class="col-5 col-sm-4">Updated</dt><dd class="col-7 col-sm-8">{{ $user->updated_at?->format('Y-m-d H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>Recent Activity</span>
                    <span class="small text-muted">Last 12</span>
                </div>
                <div class="card-body">
                    @php($items = $activities ?? [])
                    @if(empty($items))
                        <div class="text-muted small">No recent activity for this user.</div>
                    @else
                        <ul class="list-unstyled mb-0">
                            @foreach($items as $a)
                                <li class="d-flex align-items-start gap-3 py-2 border-bottom small">
                                    <span class="bi bi-{{ $a['icon'] ?? 'dot' }} text-primary mt-1"></span>
                                    <div class="flex-grow-1">
                                        <div>
                                            @if(!empty($a['url']))
                                                <a href="{{ $a['url'] }}" class="text-decoration-none">{{ $a['text'] }}</a>
                                            @else
                                                {{ $a['text'] }}
                                            @endif
                                        </div>
                                        <div class="text-muted">{{ optional($a['at'])->format('Y-m-d H:i') }}</div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <style>@media (max-width: 576px){ dl.row dt{font-weight:600} }</style>
</x-app-layout>
