<div class="row g-3">
  <div class="col-md-6">
    <div class="card h-100 shadow-sm">
      <div class="card-header">Contact</div>
      <div class="card-body small">
        <div><span class="text-muted">Address:</span> {{ $institution->address ?: '—' }}</div>
        <div><span class="text-muted">Phone:</span> {{ $institution->phone ?: '—' }}</div>
        <div><span class="text-muted">Email:</span> {{ $institution->email ?: '—' }}</div>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card h-100 shadow-sm">
      <div class="card-header">Features</div>
      <div class="card-body small">
    @php $features = $institution->enabled_features ?? []; @endphp
        @if(empty($features))
          <div class="text-muted">No additional features enabled.</div>
        @else
          @foreach($features as $f)
      <span class="badge bg-info-subtle border text-info-emphasis me-1 mb-1 text-capitalize">{{ str_replace('_',' ', $f) }}</span>
          @endforeach
        @endif
        <div class="mt-2">
          <span class="badge text-bg-light border"><span class="bi bi-person-workspace me-1"></span>Doctor assignment: {{ $institution->doctor_assignment_enabled ? 'On' : 'Off' }}</span>
        </div>
      </div>
    </div>
  </div>
</div>
