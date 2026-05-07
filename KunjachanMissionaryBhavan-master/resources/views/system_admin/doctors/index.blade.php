<x-app-layout>
  <x-slot name="header"><h2 class="h5 mb-0">Doctors</h2></x-slot>
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>Doctors</span>
      <div class="d-flex align-items-center gap-2">
        <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#doctorFilters">
          <span class="bi bi-funnel"></span>
        </button>
        <form method="GET" class="d-none d-lg-flex align-items-center gap-2">
          <select name="institution_id" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All Institutions</option>
            @foreach($institutions as $inst)
              <option value="{{ $inst->id }}" @selected(($institutionId ?? null)==$inst->id)>{{ $inst->name }}</option>
            @endforeach
          </select>
        </form>
      </div>
    </div>
    <div class="collapse d-lg-none border-bottom" id="doctorFilters">
      <form method="GET" class="p-3">
        <label class="form-label small">Institution</label>
        <select name="institution_id" class="form-select form-select-sm" onchange="this.form.submit()">
          <option value="">All Institutions</option>
          @foreach($institutions as $inst)
            <option value="{{ $inst->id }}" @selected(($institutionId ?? null)==$inst->id)>{{ $inst->name }}</option>
          @endforeach
        </select>
      </form>
    </div>
    <div class="list-group list-group-flush">
      @forelse($doctors as $doc)
        <a href="{{ route('system_admin.doctors.show',$doc) }}" class="list-group-item list-group-item-action d-flex justify-content-between">
          <span>{{ $doc->name }} <span class="text-muted">— {{ $doc->institution->name ?? 'N/A' }}</span></span>
          <span class="bi bi-chevron-right"></span>
        </a>
      @empty
        <div class="list-group-item text-muted">No doctors found.</div>
      @endforelse
    </div>
    <div class="card-footer">{{ $doctors->links() }}</div>
  </div>
</x-app-layout>
