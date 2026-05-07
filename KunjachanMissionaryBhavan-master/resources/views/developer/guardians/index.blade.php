<x-app-layout>
    <x-slot name="header"><h2 class="h5 mb-0">Guardians</h2></x-slot>
    <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
        <a href="{{ route('developer.guardians.create') }}" class="btn btn-primary btn-sm">Add Guardian</a>
        <form method="GET" action="{{ route('developer.guardians.index') }}" class="card card-body py-2 px-3 shadow-sm small">
            <div class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="form-label mb-0">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="name or phone">
                </div>
                <div class="col-auto">
                    <label class="form-label mb-0">Sort</label>
                    <select name="sort" class="form-select form-select-sm">
                        <option value="created_desc" @selected(request('sort')=='created_desc')>Newest</option>
                        <option value="created_asc" @selected(request('sort')=='created_asc')>Oldest</option>
                        <option value="name_asc" @selected(request('sort')=='name_asc')>Name A-Z</option>
                        <option value="name_desc" @selected(request('sort')=='name_desc')>Name Z-A</option>
                    </select>
                </div>
                <div class="col-auto d-flex gap-2">
                    <button class="btn btn-secondary btn-sm">Apply</button>
                    <a href="{{ route('developer.guardians.index') }}" class="btn btn-light btn-sm">Reset</a>
                </div>
            </div>
        </form>
    </div>
    @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    <div class="card"><div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead class="table-light"><tr><th>Name</th><th>Phone</th><th>Address</th><th style="width:140px;">Actions</th></tr></thead>
            <tbody>
            @forelse($guardians as $g)
                <tr>
                    <td>{{ $g->full_name }}</td>
                    <td>{{ $g->phone_number ?: '—' }}</td>
                    <td>{{ $g->address ?: '—' }}</td>
                    <td class="d-flex gap-1">
                        <a href="{{ route('developer.guardians.edit',$g) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('developer.guardians.destroy',$g) }}" method="POST" onsubmit="return confirm('Delete guardian?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Del</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center py-4">No guardians found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $guardians->links() }}</div>
    </div>
</x-app-layout>
