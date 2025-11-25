<x-app-layout>
  <x-slot name="header"><h2 class="h5 mb-0">Manage Locations · {{ $block->name }} {{ $block->prefix ? '(' . $block->prefix . ')' : '' }}</h2></x-slot>
  <div class="d-flex align-items-center mb-3">
    <a class="btn btn-sm btn-outline-secondary" href="{{ route('developer.blocks.index') }}">Back to Blocks</a>
    <button class="btn btn-sm btn-primary ms-auto" data-bs-toggle="modal" data-bs-target="#createLocation">Create New Location</button>
  </div>
  <div class="card">
    <div class="table-responsive">
      <table class="table table-striped mb-0">
        <thead class="table-light"><tr><th>Type</th><th>Number</th><th>Display Name</th><th>Status</th></tr></thead>
        <tbody>
          @forelse($locations as $loc)
          <tr>
            <td class="text-capitalize">{{ $loc->type }}</td>
            <td>{{ $loc->number }}</td>
            <td>{{ $loc->name }}</td>
            <td>{{ $loc->status }}</td>
          </tr>
          @empty
          <tr><td colspan="4" class="text-center py-4">No locations created yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($locations->hasPages())<div class="card-footer">{{ $locations->links() }}</div>@endif
  </div>

  <div class="modal fade" id="createLocation" tabindex="-1">
    <div class="modal-dialog">
      <form class="modal-content" method="POST" action="{{ route('developer.blocks.locations.store',$block) }}">
        @csrf
        <div class="modal-header"><h5 class="modal-title">Create Location in {{ $block->name }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Type</label>
            <select name="type" class="form-select" required>
              <option value="room">Room</option>
              <option value="bed">Bed</option>
              <option value="cell">Cell</option>
            </select>
          </div>
          <div class="mb-3"><label class="form-label">Number</label><input name="number" class="form-control" placeholder="e.g., 102" required></div>
        </div>
        <div class="modal-footer"><button class="btn btn-primary">Create</button></div>
      </form>
    </div>
  </div>
</x-app-layout>
