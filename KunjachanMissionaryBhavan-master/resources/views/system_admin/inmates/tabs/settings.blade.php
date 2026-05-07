<form action="{{ route('system_admin.inmates.update',$inmate) }}" method="POST" class="card shadow-sm" data-ajax>
  @csrf
  @method('PUT')
  <div class="card-header">Quick Settings</div>
  <div class="card-body">
    <div class="row g-2">
      <div class="col-md-6">
        <label class="form-label">First Name</label>
        <input class="form-control" name="first_name" value="{{ $inmate->first_name }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Last Name</label>
        <input class="form-control" name="last_name" value="{{ $inmate->last_name }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">DOB</label>
        <input type="date" class="form-control" name="date_of_birth" value="{{ optional($inmate->date_of_birth)->format('Y-m-d') }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Admission</label>
        <input type="date" class="form-control" name="admission_date" value="{{ optional($inmate->admission_date)->format('Y-m-d') }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Gender</label>
        <select class="form-select" name="gender">
          <option value="Male" @selected($inmate->gender==='Male')>Male</option>
          <option value="Female" @selected($inmate->gender==='Female')>Female</option>
          <option value="Other" @selected($inmate->gender==='Other')>Other</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Aadhaar #</label>
        <input class="form-control" name="aadhaar_number" value="{{ $inmate->aadhaar_number }}">
      </div>
      <div class="col-12">
        <label class="form-label">Notes</label>
        <textarea class="form-control" rows="3" name="notes">{{ $inmate->notes }}</textarea>
      </div>
    </div>
  </div>
  <div class="card-footer d-flex gap-2">
    <button class="btn btn-primary" type="submit"><span class="bi bi-save me-1"></span>Save</button>
    <a class="btn btn-outline-secondary" href="{{ route('system_admin.inmates.edit',$inmate) }}">Open full editor</a>
  </div>
</form>

{{-- Room assignment moved to dedicated "Allocation" tab for clarity --}}