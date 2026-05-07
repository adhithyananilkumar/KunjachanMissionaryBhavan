<x-app-layout>
    <x-slot name="header"><h2 class="h5 mb-0">Staff</h2></x-slot>
    <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
        <h1 class="h4 mb-0">Staff</h1>
        <a href="{{ route('admin.staff.create') }}" class="btn btn-primary btn-sm">Add Staff</a>
    </div>
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form class="row gy-2 gx-2 align-items-end" method="GET" action="{{ route('admin.staff.index') }}">
                <div class="col-12 col-md-5">
                    <label class="form-label small text-muted">Search</label>
                    <input type="search" name="search" value="{{ $search ?? request('search') }}" class="form-control" placeholder="Name or email">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted">Role</label>
                    <select name="role" class="form-select">
                        <option value="">All</option>
                        @foreach(($roles ?? ['doctor','nurse','staff','admin']) as $r)
                            <option value="{{ $r }}" @selected(($role ?? request('role'))===$r) class="text-capitalize">{{ ucfirst($r) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted">Sort</label>
                    @php($opts=['name_asc'=>'Name A→Z','name_desc'=>'Name Z→A','created_desc'=>'Recently added','created_asc'=>'Oldest first'])
                    <select name="sort" class="form-select">
                        @foreach($opts as $val=>$label)
                            <option value="{{ $val }}" @selected(($sort ?? request('sort','name_asc'))===$val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-1 d-grid">
                    <button class="btn btn-primary"><i class="bi bi-funnel me-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>
    <style>
        .user-item{transition:background-color .15s ease, box-shadow .15s ease, transform .15s ease; cursor:pointer;}
        .user-item:hover{background:#f8f9fa; box-shadow:0 2px 6px rgba(0,0,0,0.08); transform:translateY(-2px);} 
        .user-avatar{width:52px;height:52px;object-fit:cover;border:2px solid #fff;box-shadow:0 0 0 1px rgba(0,0,0,.1);} 
    </style>
    <div class="list-group shadow-sm mb-4">
        @forelse($users as $user)
            <div class="list-group-item user-item d-flex align-items-center gap-3 py-3 position-relative">
                <img src="{{ $user->avatar_url }}" class="rounded-circle flex-shrink-0 user-avatar" alt="avatar" loading="lazy">
                <div class="flex-grow-1">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                        <span class="fw-semibold">{{ $user->name }}</span>
                        <span class="badge bg-secondary text-capitalize">{{ $user->role }}</span>
                    </div>
                    <div class="text-muted small d-flex flex-wrap gap-3">
                        <span><span class="bi bi-envelope me-1"></span>{{ $user->email }}</span>
                        <span><span class="bi bi-hash me-1"></span>ID {{ $user->id }}</span>
                    </div>
                </div>
                <div class="dropdown ms-1 position-relative" style="z-index:2;">
                    <button class="btn btn-sm btn-outline-secondary border-0" data-bs-toggle="dropdown" aria-expanded="false" type="button"><span class="bi bi-three-dots"></span></button>
                    <div class="dropdown-menu dropdown-menu-end shadow-sm">
                        <a class="dropdown-item" href="{{ route('admin.staff.edit',$user) }}"><span class="bi bi-pencil-square me-2"></span>Edit</a>
                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#requestActionModal" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}"><span class="bi bi-send me-2"></span>Request Action</button>
                    </div>
                </div>
                <a href="{{ route('admin.staff.show',$user) }}" class="stretched-link" aria-label="View {{ $user->name }}"></a>
            </div>
        @empty
            <div class="list-group-item text-center text-muted py-5">No staff found.</div>
        @endforelse
    </div>
    <div class="d-flex justify-content-center">{{ $users->appends(request()->query())->links() }}</div>
</x-app-layout>

<!-- Action Request Modal -->
<div class="modal fade" id="requestActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.requests.store') }}" id="requestActionForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Request Developer Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" id="requestSubject" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note / Details</label>
                        <textarea name="note" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="button" id="requestActionSubmit">Send Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', ()=>{
    const modalEl = document.getElementById('requestActionModal');
    const form = document.getElementById('requestActionForm');
    const submitBtn = document.getElementById('requestActionSubmit');
    modalEl?.addEventListener('keydown', function(ev){ if(ev.key==='Enter'){ ev.preventDefault(); }});
    submitBtn?.addEventListener('click', function(){ form?.submit(); });
    modalEl?.addEventListener('show.bs.modal', (e)=>{
         const btn = e.relatedTarget;
         if(!btn) return;
         const id = btn.getAttribute('data-user-id');
         const name = btn.getAttribute('data-user-name');
         document.getElementById('requestSubject').value = `Request to delete user ID: ${id} (${name})`;
    });
});
</script>
