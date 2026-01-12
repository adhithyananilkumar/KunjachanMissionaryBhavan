@php $plan = $inmate->geriatricCarePlan; $contact = $plan->emergency_contact_details ?? []; @endphp
<div class="card mt-4">
  <div class="card-header">Geriatric Care Plan</div>
  <div class="card-body">
    <form method="POST" action="{{ route('admin.geriatric-care.save',$inmate) }}">@csrf
      <div class="row g-3 mb-3">
        <div class="col-md-4">
          <label class="form-label small">Mobility Status</label>
          <select name="mobility_status" class="form-select form-select-sm">
            @foreach(['Independent','Needs Assistance','Bedridden'] as $opt)
              <option value="{{ $opt }}" @selected(old('mobility_status',$plan->mobility_status ?? '')===$opt)>{{ $opt }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-8">
          <label class="form-label small">Dietary Needs</label>
          <textarea name="dietary_needs" rows="2" class="form-control form-control-sm" placeholder="Special diets, allergies, etc.">{{ old('dietary_needs',$plan->dietary_needs ?? '') }}</textarea>
        </div>
      </div>
      <h6 class="fw-semibold">Emergency Contact</h6>
      <div class="row g-3 mb-3">
        <div class="col-md-4"><label class="form-label small">Name</label><input name="emergency_name" class="form-control form-control-sm" value="{{ old('emergency_name',$contact['name'] ?? '') }}"></div>
        <div class="col-md-4"><label class="form-label small">Phone</label><input name="emergency_phone" class="form-control form-control-sm" value="{{ old('emergency_phone',$contact['phone'] ?? '') }}"></div>
        <div class="col-md-4"><label class="form-label small">Relationship</label><input name="emergency_relationship" class="form-control form-control-sm" value="{{ old('emergency_relationship',$contact['relationship'] ?? '') }}"></div>
      </div>
      <div class="text-end"><button class="btn btn-sm btn-success">Save Plan</button></div>
    </form>
    @if($plan && $plan->updated_at)
      <p class="text-muted small mb-0 mt-2">Last updated: {{ $plan->updated_at->diffForHumans() }}</p>
    @endif
  </div>
</div>
