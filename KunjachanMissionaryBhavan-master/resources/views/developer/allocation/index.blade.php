<x-app-layout>
  <x-slot name="header"><h2 class="h5 mb-0">Allocation</h2></x-slot>
  <div class="d-flex align-items-center gap-2 mb-3">
    <form method="GET" class="d-flex align-items-center gap-2">
      <select name="institution_id" class="form-select form-select-sm" onchange="this.form.submit()">
        <option value="">All Institutions</option>
        @foreach($institutions as $inst)
          <option value="{{ $inst->id }}" @selected(($institutionId ?? null)==$inst->id)>{{ $inst->name }}</option>
        @endforeach
      </select>
      <select name="block_id" class="form-select form-select-sm" @disabled(empty($institutionId)) onchange="this.form.submit()">
        <option value="">All Blocks</option>
        @foreach(($blocks ?? collect()) as $b)
          <option value="{{ $b->id }}" @selected(($blockId ?? null)==$b->id)>{{ $b->name }}{{ $b->prefix ? ' ('.$b->prefix.')' : '' }}</option>
        @endforeach
      </select>
      <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
        <option value="">All Types</option>
        <option value="room" @selected(($type ?? '')==='room')>Room</option>
        <option value="bed" @selected(($type ?? '')==='bed')>Bed</option>
        <option value="cell" @selected(($type ?? '')==='cell')>Cell</option>
      </select>
      <noscript><button class="btn btn-sm btn-primary">Filter</button></noscript>
    </form>
    @if(!empty($institutionId))
      <button class="btn btn-sm btn-primary ms-auto" data-bs-toggle="modal" data-bs-target="#createLocation">Create New Location</button>
    @endif
  </div>
  <div class="card">
    <div class="table-responsive">
      <table class="table table-striped mb-0">
        <thead class="table-light"><tr><th>Institution</th><th>Block</th><th>Type</th><th>No.</th><th>Capacity</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
          @forelse($locations as $loc)
          <tr>
            <td>{{ $loc->institution?->name ?? '—' }}</td>
            <td>{{ $loc->block?->name }}{{ $loc->block?->prefix ? ' ('.$loc->block->prefix.')' : '' }}</td>
            <td class="text-capitalize">{{ $loc->type }}</td>
            <td>{{ $loc->number }}</td>
            <td>{{ $loc->capacity }}</td>
            <td>{{ $loc->status }}</td>
            <td class="text-end">
              <form method="POST" action="{{ route('developer.allocation.update',$loc) }}" class="d-inline">
                @csrf @method('PUT')
                <input type="hidden" name="capacity" value="{{ $loc->capacity }}">
                <input type="hidden" name="status" value="{{ $loc->status==='available'?'unavailable':'available' }}">
                <button class="btn btn-sm btn-outline-secondary">Toggle Status</button>
              </form>
              <form method="POST" action="{{ route('developer.allocation.destroy',$loc) }}" class="d-inline" onsubmit="return confirm('Delete this location?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="text-center py-4">No locations yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($locations->hasPages())<div class="card-footer">{{ $locations->links() }}</div>@endif
  </div>

  @if(!empty($institutionId))
  <div class="modal fade" id="createLocation" tabindex="-1">
    <div class="modal-dialog">
      <form class="modal-content" method="POST" action="{{ route('developer.allocation.store') }}">
        @csrf
        <div class="modal-header"><h5 class="modal-title">Create Location</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Block</label>
            <select name="block_id" class="form-select" required>
              @forelse(($blocks ?? collect()) as $b)
                <option value="{{ $b->id }}">{{ $b->name }}{{ $b->prefix ? ' ('.$b->prefix.')' : '' }}</option>
              @empty
                <option value="" disabled>No blocks found for selected institution</option>
              @endforelse
            </select>
          </div>
          <div class="mb-3"><label class="form-label">Type</label>
            <select name="type" class="form-select" required>
              <option value="room">Room</option>
              <option value="bed">Bed</option>
              <option value="cell">Cell</option>
            </select>
          </div>
          <div class="mb-3"><label class="form-label">Number</label><input name="number" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Capacity</label><input type="number" name="capacity" class="form-control" min="1" value="1"></div>
        </div>
        <div class="modal-footer"><button class="btn btn-primary">Create</button></div>
      </form>
    </div>
  </div>
  @endif
</x-app-layout>
