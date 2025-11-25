<x-app-layout>
  <x-slot name="header"><h2 class="h5 mb-0">Doctor: {{ $doctor->name }}</h2></x-slot>
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>Assign Inmates</span>
      <a href="{{ route('developer.doctors.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('developer.doctors.assignments',$doctor) }}" id="assignForm">@csrf
        <div class="row g-2 mb-2">
          <div class="col-md-6"><input type="text" id="searchInmates" class="form-control form-control-sm" placeholder="Search inmates..."></div>
          <div class="col-md-6 text-end"><span class="small text-muted" id="selectedCount">Selected: {{ count($assignedIds) }}</span></div>
        </div>
        <div class="border rounded p-2" style="max-height:460px; overflow:auto">
          @foreach($inmates as $itm)
          <label class="d-flex align-items-center gap-2 small py-1">
            <input type="checkbox" class="form-check-input inmate-checkbox" name="inmate_ids[]" value="{{ $itm->id }}" @checked(in_array($itm->id,$assignedIds))>
            <span>{{ $itm->full_name }}</span>
          </label>
          @endforeach
        </div>
        <div class="d-flex justify-content-end gap-2 mt-2">
          <button class="btn btn-sm btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
  <script>
  document.addEventListener('DOMContentLoaded', function(){
    const search = document.getElementById('searchInmates');
    const container = document.querySelector('#assignForm .border');
    const selectedCount = document.getElementById('selectedCount');
    function updateSelected(){ selectedCount.textContent = 'Selected: ' + container.querySelectorAll('.inmate-checkbox:checked').length; }
    container.addEventListener('change', updateSelected);
    search.addEventListener('input', function(){
      const term = this.value.toLowerCase();
      container.querySelectorAll('label').forEach(lbl=>{
        const txt = lbl.textContent.toLowerCase();
        lbl.style.display = txt.includes(term) ? '' : 'none';
      });
    });
  });
  </script>
</x-app-layout>
