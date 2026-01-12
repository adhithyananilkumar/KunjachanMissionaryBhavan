<x-app-layout>
  <x-slot name="header"><h2 class="h5 mb-0">Patient Details</h2></x-slot>
  <style>
    .staff-patient-header img{ width:56px; height:56px; object-fit:cover; }
    .mobile-chip{ display:inline-flex; align-items:center; gap:.25rem; padding:.15rem .5rem; border-radius:999px; font-size:.8rem; background:#f1f3f5; }
  </style>
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="d-flex align-items-center gap-3 flex-wrap staff-patient-header">
        <img src="{{ $inmate->avatar_url }}" class="rounded-circle" alt="avatar">
        <div class="flex-grow-1">
          <div class="fw-semibold">{{ $inmate->full_name }} @if($inmate->registration_number)<span class="badge bg-light text-dark border ms-2">#{{ $inmate->registration_number }}</span>@endif</div>
          <div class="text-muted small">
            @if($inmate->date_of_birth)
              @php $ageYears = $inmate->date_of_birth->age; @endphp
              <span class="mobile-chip" title="DOB: {{ $inmate->date_of_birth?->format('Y-m-d') }}">Age: <strong>{{ $ageYears }}</strong></span>
            @endif
            <span class="mobile-chip" title="Current Allocation">Alloc: <strong>{{ $inmate->currentLocation?->location?->number ?? '—' }}</strong></span>
          </div>
        </div>
        <div class="ms-auto d-flex gap-2">
          <a href="{{ route('staff.allocation.edit',$inmate) }}" class="btn btn-outline-primary btn-sm"><span class="bi bi-door-open me-1"></span> Allocation</a>
          <a href="{{ route('staff.inmates.index') }}" class="btn btn-link btn-sm text-decoration-none"><span class="bi bi-arrow-left me-1"></span> Back to Patients</a>
        </div>
      </div>

      <ul class="nav nav-pills small flex-wrap gap-2 mt-3" role="tablist">
        <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-medical" type="button" role="tab">Medical</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-labs" type="button" role="tab">Lab Tests</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-meds" type="button" role="tab">Medications</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-reportings" type="button" role="tab">Reportings</button></li>
      </ul>

      <div class="tab-content border rounded bg-white p-2 p-md-4 mt-2">
        <div class="tab-pane fade show active" id="tab-medical" role="tabpanel">
          @include('partials.inmates._medical_history', ['inmate'=>$inmate])
        </div>
        <div class="tab-pane fade" id="tab-labs" role="tabpanel">
          @php $labTests = $inmate->labTests()->latest('ordered_date')->get(); @endphp
          @if($labTests->isEmpty())
            <div class="text-muted small">No lab tests.</div>
          @else
            <div class="accordion" id="labTestsAcc">
              @foreach($labTests as $lt)
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#lab-c-{{ $lt->id }}">
                      {{ $lt->test_name }} • {{ optional($lt->ordered_date)->format('Y-m-d') }} @if($lt->status) <span class="ms-2 text-muted small text-capitalize">{{ str_replace('_',' ',$lt->status) }}</span>@endif
                    </button>
                  </h2>
                  <div id="lab-c-{{ $lt->id }}" class="accordion-collapse collapse" data-bs-parent="#labTestsAcc">
                    <div class="accordion-body">
                      <div class="small">Completed: {{ optional($lt->completed_date)->format('Y-m-d H:i') ?: '—' }}</div>
                      <div class="small">Notes: {{ $lt->notes ?: '—' }}</div>
                      <div class="small">Result Notes: {{ $lt->result_notes ?: '—' }}</div>
                      @if($lt->result_file_path)
                        @php $disk = Storage::disk(config('filesystems.default')); $rurl = $lt->result_file_path ? (config('filesystems.default')==='s3' ? $disk->temporaryUrl($lt->result_file_path, now()->addMinutes(5)) : $disk->url($lt->result_file_path)) : null; @endphp
                        @if($rurl)<a target="_blank" href="{{ $rurl }}" class="btn btn-sm btn-outline-primary mt-2">View Report</a>@endif
                      @endif
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>
        <div class="tab-pane fade" id="tab-meds" role="tabpanel">
          @php $activeMeds = $inmate->medications()->where('status','active')->orderBy('name')->get(); @endphp
          @if($activeMeds->isEmpty())
            <div class="text-muted small">No active medications.</div>
          @else
            <div class="list-group">
              @foreach($activeMeds as $m)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <div class="fw-semibold">{{ $m->name }}</div>
                    <div class="text-muted small">{{ $m->dosage }} {{ $m->route }} • {{ $m->frequency }}</div>
                  </div>
                  <span class="text-muted small">{{ optional($m->start_date)->format('Y-m-d') }}</span>
                </div>
              @endforeach
            </div>
          @endif
        </div>
        <div class="tab-pane fade" id="tab-reportings" role="tabpanel">
          <form method="POST" action="{{ route('staff.inmates.examinations.store',$inmate) }}" class="border rounded p-2 p-md-3 bg-light-subtle">
            @csrf
            <div class="row g-2">
              <div class="col-12 col-md-6">
                <label class="form-label">Title (optional)</label>
                <input type="text" name="title" class="form-control" placeholder="e.g., Coughing observation" value="{{ old('title') }}">
              </div>
              <div class="col-6 col-md-3">
                <label class="form-label">Severity</label>
                <select name="severity" class="form-select">
                  <option value="">—</option>
                  <option value="mild">Mild</option>
                  <option value="moderate">Moderate</option>
                  <option value="severe">Severe</option>
                </select>
              </div>
              <div class="col-6 col-md-3">
                <label class="form-label">Observed at</label>
                <input type="datetime-local" name="observed_at" class="form-control" value="{{ old('observed_at') }}">
              </div>
              <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Describe the observation" required>{{ old('notes') }}</textarea>
              </div>
              <div class="col-12 d-flex justify-content-end">
                <button class="btn btn-primary"><span class="bi bi-send me-1"></span> Submit to Doctor</button>
              </div>
            </div>
          </form>
          <div class="mt-3">
            <div class="fw-semibold mb-1">Recent reportings</div>
            @php $exams = $inmate->examinations()->limit(10)->get(); @endphp
            @forelse($exams as $ex)
              <div class="border rounded p-2 mb-2">
                <div class="small text-muted">{{ $ex->observed_at?->format('Y-m-d H:i') ?? $ex->created_at->format('Y-m-d H:i') }} • {{ ucfirst($ex->creator_role) }}: {{ $ex->creator?->name }}</div>
                <div class="fw-semibold">{{ $ex->title ?: 'Reporting' }} @if($ex->severity) <span class="badge bg-light text-dark border ms-1 text-capitalize">{{ $ex->severity }}</span>@endif</div>
                <div class="small">{{ $ex->notes }}</div>
              </div>
            @empty
              <div class="text-muted small">No reportings yet.</div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
