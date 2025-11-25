<div class="row g-3">
  <div class="col-lg-8">
    <form id="settingsForm" class="card shadow-sm">
      <div class="card-header">Settings</div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label">Name</label>
          <input class="form-control" name="name" value="{{ $institution->name }}" />
        </div>
        <div class="mb-3">
          <label class="form-label">Address</label>
          <textarea class="form-control" rows="2" name="address">{{ $institution->address }}</textarea>
        </div>
        <div class="row g-2">
          <div class="col-md-6"><label class="form-label">Phone</label><input class="form-control" name="phone" value="{{ $institution->phone }}" /></div>
          <div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="{{ $institution->email }}" /></div>
        </div>
        <hr />
        @php $features = $institution->enabled_features ?? []; @endphp
        <div class="mb-2 fw-semibold">Enabled Features</div>
        <div class="row row-cols-1 row-cols-md-2 g-2">
          @foreach(['orphan_care','elderly_care','mental_health','rehabilitation','undefined_inmate'] as $f)
          <div class="col">
            <label class="form-check small">
              <input class="form-check-input" type="checkbox" name="features[]" value="{{ $f }}" {{ in_array($f,$features)?'checked':'' }}>
              <span class="form-check-label text-capitalize">{{ str_replace('_',' ',$f) }}</span>
            </label>
          </div>
          @endforeach
        </div>
        <div class="form-check form-switch mt-3">
          <input class="form-check-input" type="checkbox" role="switch" id="docAssign" name="doctor_assignment_enabled" value="1" {{ $institution->doctor_assignment_enabled?'checked':'' }}>
          <label class="form-check-label" for="docAssign">Enable doctor-patient assignment</label>
        </div>
      </div>
      <div class="card-footer d-flex gap-2">
        <button class="btn btn-primary" type="submit"><span class="bi bi-save me-1"></span>Save</button>
      </div>
    </form>
  </div>
</div>
<script>
  document.getElementById('settingsForm').addEventListener('submit', function(e){
    e.preventDefault();
    const form = e.currentTarget;
    const data = new FormData(form);
    data.append('_method','PUT');
    fetch('{{ route('system_admin.institutions.update',$institution) }}', {
      method: 'POST',
      headers:{
        'X-Requested-With':'XMLHttpRequest',
        'X-CSRF-TOKEN':'{{ csrf_token() }}',
        'Accept':'application/json'
      },
      body: data
    }).then(r=>r.json()).then(resp=>{
      const msg = document.createElement('div');
      msg.className = 'alert alert-success small mt-2';
      msg.textContent = (resp && resp.message) ? resp.message : 'Saved.';
      form.appendChild(msg);
      setTimeout(()=>msg.remove(), 3000);
    }).catch(()=>{
      const msg = document.createElement('div');
      msg.className = 'alert alert-danger small mt-2';
      msg.textContent = 'Save failed';
      form.appendChild(msg);
      setTimeout(()=>msg.remove(), 3000);
    });
  });
</script>
