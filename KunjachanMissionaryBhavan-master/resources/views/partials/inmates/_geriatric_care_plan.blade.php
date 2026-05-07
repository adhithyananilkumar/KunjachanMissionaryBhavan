<div class="card mt-4" data-section="geriatric-care">
  <div class="card-header py-2"><strong>Geriatric Care Plan</strong></div>
  <div class="card-body small">
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Mobility Status</label>
        <input type="text" name="mobility_status" class="form-control form-control-sm" value="{{ old('mobility_status') }}">
      </div>
      <div class="col-md-4">
        <label class="form-label">Dietary Needs</label>
        <input type="text" name="dietary_needs" class="form-control form-control-sm" value="{{ old('dietary_needs') }}">
      </div>
      <div class="col-md-4">
        <label class="form-label">Emergency Contact (JSON)</label>
        <textarea name="emergency_contact_details" rows="2" class="form-control form-control-sm" placeholder='{"name":"","phone":""}'>{{ old('emergency_contact_details') }}</textarea>
      </div>
    </div>
  </div>
</div>
