<div class="row g-3">
  <div class="col-lg-6">
    <div class="card h-100">
  <div class="card-header d-flex justify-content-between align-items-center">Medications <a class="btn btn-sm btn-outline-secondary" href="{{ route('system_admin.inmates.edit',$inmate) }}#medications">Edit</a></div>
      <div class="card-body small">
        @php $meds = $inmate->medications()->latest()->limit(10)->get(); @endphp
        @if($meds->isEmpty())
          <div class="text-muted">No medications recorded.</div>
        @else
          <ul class="list-group list-group-flush">
            @foreach($meds as $m)
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-semibold">{{ $m->name }}</div>
                  <div class="text-muted">{{ $m->dosage ?? '' }} {{ $m->frequency ? '· '.$m->frequency : '' }}</div>
                </div>
                @if($m->status)
                  <span class="badge text-bg-light border text-capitalize">{{ $m->status }}</span>
                @endif
              </li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header">Lab Tests</div>
      <div class="card-body small">
        @php $labs = $inmate->labTests()->latest()->limit(10)->get(); @endphp
        @if($labs->isEmpty())
          <div class="text-muted">No lab tests.</div>
        @else
          <ul class="list-group list-group-flush">
            @foreach($labs as $t)
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-semibold">{{ $t->test_name }}</div>
                  <div class="text-muted">{{ $t->ordered_at?->format('Y-m-d') }} {{ $t->result_status ? '· '.$t->result_status : '' }}</div>
                </div>
                <span class="badge text-bg-light border text-capitalize">{{ $t->status ?? 'view' }}</span>
              </li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">Therapy & Counseling <a class="btn btn-sm btn-outline-primary" href="{{ route('system_admin.inmates.edit',$inmate) }}#therapy">Log</a></div>
      <div class="card-body small">
        @php $logs = $inmate->therapySessionLogs()->limit(5)->get(); @endphp
        @if($logs->isEmpty())
          <div class="text-muted">No therapy sessions.</div>
        @else
          <ul class="list-group list-group-flush">
            @foreach($logs as $log)
              <li class="list-group-item">
                <div class="d-flex justify-content-between"><span class="fw-semibold">{{ $log->session_date?->format('Y-m-d') }}</span><span class="text-muted">{{ $log->therapist_name ?? '' }}</span></div>
                <div class="text-muted">{{ Str::limit($log->notes ?? '', 140) }}</div>
              </li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>
  @if($inmate->geriatricCarePlan || $inmate->mentalHealthPlan || $inmate->rehabilitationPlan)
  <div class="col-12">
    <div class="card">
      <div class="card-header">Care Plans</div>
      <div class="card-body small">
        <div class="row g-3">
          @if($inmate->geriatricCarePlan)
          <div class="col-md-4">
            <div class="border rounded p-3 h-100">
              <div class="fw-semibold mb-2">Geriatric Plan</div>
              <div class="mb-1"><span class="text-muted">Mobility:</span> {{ $inmate->geriatricCarePlan->mobility_status ?? '—' }}</div>
              <div class="mb-1"><span class="text-muted">Dietary:</span> {{ $inmate->geriatricCarePlan->dietary_needs ?? '—' }}</div>
            </div>
          </div>
          @endif
          @if($inmate->mentalHealthPlan)
          <div class="col-md-4">
            <div class="border rounded p-3 h-100">
              <div class="fw-semibold mb-2">Mental Health</div>
              <div class="mb-1"><span class="text-muted">Diagnosis:</span> {{ $inmate->mentalHealthPlan->diagnosis ?? '—' }}</div>
              <div class="mb-1"><span class="text-muted">Therapy:</span> {{ $inmate->mentalHealthPlan->therapy_frequency ?? '—' }}</div>
            </div>
          </div>
          @endif
          @if($inmate->rehabilitationPlan)
          <div class="col-md-4">
            <div class="border rounded p-3 h-100">
              <div class="fw-semibold mb-2">Rehabilitation</div>
              <div class="mb-1"><span class="text-muted">Primary Issue:</span> {{ $inmate->rehabilitationPlan->primary_issue ?? '—' }}</div>
              <div class="mb-1"><span class="text-muted">Phase:</span> {{ $inmate->rehabilitationPlan->program_phase ?? '—' }}</div>
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
  @endif
</div>