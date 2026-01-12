<div class="card shadow-sm">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span>Room Assignment</span>
    <small class="text-muted">Assign or transfer room</small>
  </div>
  <div class="card-body">
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
      <div class="flex-grow-1">
        <input id="allocRoomSearch" type="text" class="form-control form-control-sm" placeholder="Search rooms by name">
      </div>
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" role="switch" id="allocShowOcc">
        <label class="form-check-label small" for="allocShowOcc">Show occupied</label>
      </div>
      <button type="button" id="allocReload" class="btn btn-outline-secondary btn-sm"><span class="bi bi-arrow-clockwise me-1"></span>Reload</button>
    </div>

    <div class="row g-3">
      <div class="col-12 col-md-8">
        <div id="allocRoomsList" class="list-group small">
          <div class="text-muted text-center py-3">Loading rooms…</div>
        </div>
      </div>
      <div class="col-12 col-md-4">
        <form id="allocAssignForm" method="POST" action="{{ route('admin.inmates.assign-location', $inmate) }}" data-inst="{{ (int)auth()->user()->institution_id }}">
          @csrf
          <input type="hidden" name="location_id" id="allocLocationId">
          <div class="border rounded p-3 small">
            <div class="mb-2 text-muted">Selected</div>
            <div class="fw-semibold mb-2" id="allocSelected">None</div>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-outline-secondary btn-sm" id="allocClear">Clear</button>
              <button type="submit" class="btn btn-primary btn-sm">Update Allocation</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- JS initializer lives in the host view (admin/inmates/show.blade.php) to bind events after AJAX load. --}}
