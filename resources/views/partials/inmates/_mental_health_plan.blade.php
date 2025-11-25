<div class="card mt-4" data-section="mental-health">
  <div class="card-header py-2"><strong>Mental Health Plan</strong></div>
  <div class="card-body small">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Diagnosis</label>
        <input type="text" name="mh_diagnosis" class="form-control form-control-sm" value="{{ old('mh_diagnosis') }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Therapy Frequency</label>
        <input type="text" name="mh_therapy_frequency" class="form-control form-control-sm" value="{{ old('mh_therapy_frequency') }}">
      </div>
      <div class="col-12">
        <label class="form-label">Current Medications</label>
        <textarea name="mh_current_meds" rows="3" class="form-control form-control-sm">{{ old('mh_current_meds') }}</textarea>
      </div>
    </div>
  </div>
</div>
