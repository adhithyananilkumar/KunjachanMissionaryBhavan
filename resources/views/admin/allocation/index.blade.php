<x-app-layout>
  <x-slot name="header"><h2 class="h5 mb-0">Allocation</h2></x-slot>
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card shadow-sm mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>Locations</span>
      <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createLocation"><span class="bi bi-plus-lg me-1"></span>Create New Location</button>
    </div>
    <div class="list-group list-group-flush">
      @forelse($locations as $loc)
        @php $isAvail = $loc->status === 'available'; @endphp
        <div class="list-group-item d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-3">
            <div>
              <div class="fw-semibold">{{ $loc->name }}</div>
              <div class="text-muted small text-capitalize">Type: {{ $loc->type }} · Capacity: {{ $loc->capacity }}</div>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge {{ $isAvail ? 'bg-success' : ($loc->status==='maintenance' ? 'bg-warning text-dark' : 'bg-secondary') }} text-capitalize">{{ $loc->status }}</span>
            <div class="dropdown">
              <button class="btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="bi bi-three-dots"></span></button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li>
                  <form method="POST" action="{{ route('admin.allocation.update',$loc) }}">
                    @csrf @method('PUT')
                    <input type="hidden" name="name" value="{{ $loc->name }}">
                    <input type="hidden" name="type" value="{{ $loc->type }}">
                    <input type="hidden" name="capacity" value="{{ $loc->capacity }}">
                    <input type="hidden" name="status" value="{{ $isAvail ? 'unavailable' : 'available' }}">
                    <button class="dropdown-item" type="submit"><span class="bi bi-arrow-repeat me-2"></span>Toggle Status</button>
                  </form>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <form method="POST" action="{{ route('admin.allocation.destroy',$loc) }}" onsubmit="return confirm('Delete this location?')">
                    @csrf @method('DELETE')
                    <button class="dropdown-item text-danger" type="submit"><span class="bi bi-trash me-2"></span>Delete</button>
                  </form>
                </li>
              </ul>
            </div>
          </div>
        </div>
      @empty
        <div class="list-group-item text-center text-muted py-4">No locations yet.</div>
      @endforelse
    </div>
    @if($locations->hasPages())<div class="card-footer">{{ $locations->links() }}</div>@endif
  </div>

  <div class="modal fade" id="createLocation" tabindex="-1">
    <div class="modal-dialog">
      <form class="modal-content" method="POST" action="{{ route('admin.allocation.store') }}">
        @csrf
        <div class="modal-header"><h5 class="modal-title">Create Location</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Type</label>
            <select name="type" class="form-select" required>
              <option value="room">Room</option>
              <option value="bed">Bed</option>
              <option value="cell">Cell</option>
            </select>
          </div>
          <div class="mb-3"><label class="form-label">Capacity</label><input type="number" name="capacity" class="form-control" min="1" value="1"></div>
        </div>
        <div class="modal-footer"><button class="btn btn-primary">Create</button></div>
      </form>
    </div>
  </div>
</x-app-layout>
