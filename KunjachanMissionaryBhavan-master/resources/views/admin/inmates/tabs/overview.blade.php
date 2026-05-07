<div class="row g-3">
  <div class="col-12">
    <div class="row g-3">
      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span>Today</span>
            <span class="small"><span class="text-muted">Current allocation:</span> <strong>{{ optional($inmate->currentLocation?->location)->name ?? 'Not assigned' }}</strong></span>
          </div>
          <div class="card-body small">
            @php
              $today = \Carbon\Carbon::today();
              $medsLoggedToday = \App\Models\MedicationLog::whereHas('medicalRecord', function($q) use($inmate){ $q->where('inmate_id',$inmate->id); })
                ->whereDate('administration_time', $today)->count();
              $hasMeds = $inmate->medications()->exists();
              $upcoming = $inmate->appointments()->where('scheduled_for','>=', now())->orderBy('scheduled_for')->first();
            @endphp
            <div class="d-flex flex-wrap gap-3">
              <div>
                <div class="text-muted">Medication taken</div>
                <div class="fw-semibold">
                  @if(!$hasMeds)
                    <span class="badge text-bg-secondary">No active prescriptions</span>
                  @elseif($medsLoggedToday>0)
                    <span class="badge text-bg-success">Yes ({{ $medsLoggedToday }})</span>
                  @else
                    <span class="badge text-bg-warning">Not recorded</span>
                  @endif
                </div>
              </div>
              <div>
                <div class="text-muted">Birthday</div>
                <div class="fw-semibold">
                  @if($inmate->date_of_birth && $inmate->date_of_birth->isBirthday())
                    <span class="badge text-bg-info text-dark"><span class="bi bi-cake me-1"></span>Today</span>
                  @elseif($inmate->date_of_birth)
                    {{ $inmate->date_of_birth->format('M d') }}
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </div>
              </div>
              <div>
                <div class="text-muted">Next appointment</div>
                <div class="fw-semibold">
                  @if($upcoming)
                    {{ $upcoming->scheduled_for?->format('Y-m-d H:i') }} <span class="text-muted">{{ $upcoming->title ?? '' }}</span>
                  @else
                    <span class="text-muted">None</span>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card h-100">
          <div class="card-header">Personal</div>
          <div class="card-body">
            <dl class="row mb-0 small">
              <dt class="col-5">Gender</dt><dd class="col-7">{{ $inmate->gender ?: '—' }}</dd>
              <dt class="col-5">DOB</dt><dd class="col-7">{{ $inmate->date_of_birth?->format('Y-m-d') ?: '—' }}</dd>
              <dt class="col-5">Admission</dt><dd class="col-7">{{ $inmate->admission_date?->format('Y-m-d') ?: '—' }}</dd>
              <dt class="col-5">Aadhaar</dt><dd class="col-7">{{ $inmate->aadhaar_number ?: '—' }}</dd>
            </dl>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card h-100">
          <div class="card-header">Guardian</div>
          <div class="card-body small">
            @if($inmate->guardian_first_name || $inmate->guardian_last_name)
              <div class="mb-1"><span class="text-muted">Relation:</span> {{ $inmate->guardian_relation ?: '—' }}</div>
              <div class="mb-1"><span class="text-muted">Name:</span> {{ trim($inmate->guardian_first_name.' '.$inmate->guardian_last_name) }}</div>
              <div class="mb-1"><span class="text-muted">Email:</span> {{ $inmate->guardian_email ?: '—' }}</div>
              <div class="mb-1"><span class="text-muted">Phone:</span> {{ $inmate->guardian_phone ?: '—' }}</div>
              <div><span class="text-muted">Address:</span> {{ $inmate->guardian_address ?: '—' }}</div>
            @else
              <p class="text-muted mb-0">No guardian details.</p>
            @endif
          </div>
        </div>
      </div>
      <div class="col-12">
        <div class="card">
          <div class="card-header">Notes</div>
          <div class="card-body small">{!! $inmate->notes ? nl2br(e($inmate->notes)) : '<span class="text-muted">—</span>' !!}</div>
        </div>
      </div>
    </div>
  </div>
</div>
