<div class="card mt-4" data-section="rehab-plan">
  <div class="card-header py-2"><strong>Rehabilitation Plan</strong></div>
  <div class="card-body small">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Primary Substance / Issue</label>
        <input type="text" name="rehab_primary_issue" class="form-control form-control-sm" value="{{ old('rehab_primary_issue') }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Program Phase</label>
        <input type="text" name="rehab_program_phase" class="form-control form-control-sm" value="{{ old('rehab_program_phase') }}">
      </div>
      <div class="col-12">
        <label class="form-label">Goals</label>
        <textarea name="rehab_goals" rows="3" class="form-control form-control-sm">{{ old('rehab_goals') }}</textarea>
      </div>
    </div>
  </div>
</div>
