<div class="row g-3">
  <!-- Left column removed to avoid duplicate header info -->
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
      <div class="col-lg-4 col-md-6">
        <div class="card h-100">
          <div class="card-header">Demographics</div>
          <div class="card-body small">
            <dl class="row mb-0">
              <dt class="col-5">Admission #</dt><dd class="col-7">{{ $inmate->admission_number ?: '—' }}</dd>
              <dt class="col-5">Reg #</dt><dd class="col-7">{{ $inmate->registration_number ?: '—' }}</dd>
              <dt class="col-5">Type</dt><dd class="col-7 text-capitalize">{{ $inmate->type ?: '—' }}</dd>
              <dt class="col-5">Institution</dt><dd class="col-7">{{ $inmate->institution?->name ?: '—' }}</dd>
              <dt class="col-5">Gender</dt><dd class="col-7">{{ $inmate->gender ?: '—' }}</dd>
              <dt class="col-5">DOB / Age</dt><dd class="col-7">{{ $inmate->date_of_birth?->format('Y-m-d') ?: '—' }} @if($inmate->age)<span class="text-muted">({{ $inmate->age }})</span>@endif</dd>
              <dt class="col-5">Marital</dt><dd class="col-7">{{ $inmate->marital_status ?: '—' }}</dd>
              <dt class="col-5">Blood</dt><dd class="col-7">{{ $inmate->blood_group ?: '—' }}</dd>
              <dt class="col-5">Religion</dt><dd class="col-7">{{ $inmate->religion ?: '—' }}</dd>
              <dt class="col-5">Caste</dt><dd class="col-7">{{ $inmate->caste ?: '—' }}</dd>
              <dt class="col-5">Nationality</dt><dd class="col-7">{{ $inmate->nationality ?: '—' }}</dd>
            </dl>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="card h-100">
          <div class="card-header">Physical</div>
          <div class="card-body small">
            <dl class="row mb-0">
              <dt class="col-5">Height</dt><dd class="col-7">{{ $inmate->height ? $inmate->height.' cm' : '—' }}</dd>
              <dt class="col-5">Weight</dt><dd class="col-7">{{ $inmate->weight ? $inmate->weight.' kg' : '—' }}</dd>
              <dt class="col-5">Ident. Marks</dt><dd class="col-7">
                @php $marks = collect(explode('|', (string)$inmate->identification_marks))->filter(fn($m)=>trim($m)!==''); @endphp
                @if($marks->isEmpty())
                  <span class="text-muted">—</span>
                @else
                  <ul class="mb-0 ps-3">
                    @foreach($marks as $m)<li>{{ $m }}</li>@endforeach
                  </ul>
                @endif
              </dd>
              <dt class="col-5">Aadhaar</dt><dd class="col-7">{{ $inmate->aadhaar_number ?: '—' }}</dd>
              <dt class="col-5">Admission Date</dt><dd class="col-7">{{ $inmate->admission_date?->format('Y-m-d') ?: '—' }}</dd>
            </dl>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-12">
        <div class="card h-100">
          <div class="card-header">Address</div>
          <div class="card-body small">
            @php $addr = is_array($inmate->address) ? $inmate->address : (array)($inmate->address ?? []); @endphp
            <div>{{ $addr['line1'] ?? '—' }}</div>
            <div>{{ $addr['line2'] ?? '' }}</div>
            <div class="text-muted">{{ $addr['city'] ?? '' }} {{ $addr['state'] ?? '' }} {{ $addr['pincode'] ?? '' }}</div>
          </div>
        </div>
      </div>
      <div class="col-lg-6 col-md-6">
        <div class="card h-100">
          <div class="card-header">Family</div>
          <div class="card-body small">
            <dl class="row mb-0">
              <dt class="col-5">Father</dt><dd class="col-7">{{ $inmate->father_name ?: '—' }}</dd>
              <dt class="col-5">Mother</dt><dd class="col-7">{{ $inmate->mother_name ?: '—' }}</dd>
              <dt class="col-5">Spouse</dt><dd class="col-7">{{ $inmate->spouse_name ?: '—' }}</dd>
              <dt class="col-5">Guardian</dt><dd class="col-7">{{ $inmate->guardian_name ?: '—' }}</dd>
              <dt class="col-5">Guardian Rel.</dt><dd class="col-7">{{ $inmate->guardian_relation ?: '—' }}</dd>
              <dt class="col-5">Guardian Phone</dt><dd class="col-7">{{ $inmate->guardian_phone ?: '—' }}</dd>
              <dt class="col-5">Guardian Email</dt><dd class="col-7">{{ $inmate->guardian_email ?: '—' }}</dd>
              <dt class="col-5">Guardian Address</dt><dd class="col-7">{!! $inmate->guardian_address ? nl2br(e($inmate->guardian_address)) : '<span class="text-muted">—</span>' !!}</dd>
            </dl>
          </div>
        </div>
      </div>
      <div class="col-lg-6 col-md-6">
        <div class="card h-100">
          <div class="card-header">Health</div>
          <div class="card-body small">
            @php $hi = $inmate->health_info; if(is_string($hi)){ $dec = json_decode($hi,true); if(json_last_error()===JSON_ERROR_NONE){ $hi=$dec; } }
            @endphp
            @if(is_array($hi) && !empty($hi))
              <dl class="row mb-0">
                @foreach($hi as $k=>$v)
                  <dt class="col-5 text-truncate">{{ Str::of($k)->replace(['_','-'],' ')->title() }}</dt>
                  <dd class="col-7">@if(is_array($v))<code class="small">{{ implode(', ', array_map(fn($x)=> is_scalar($x)? $x : json_encode($x), $v)) }}</code>@else{{ $v }}@endif</dd>
                @endforeach
              </dl>
            @elseif(is_string($hi) && trim($hi)!=='')
              <div>{{ $hi }}</div>
            @else
              <span class="text-muted">—</span>
            @endif
          </div>
        </div>
      </div>
      <div class="col-12 col-md-6">
        <div class="card h-100">
          <div class="card-header">Critical Alert</div>
          <div class="card-body small">{!! $inmate->critical_alert ? nl2br(e($inmate->critical_alert)) : '<span class="text-muted">—</span>' !!}</div>
        </div>
      </div>
      <div class="col-12 col-md-6">
        <div class="card h-100">
          <div class="card-header">Education</div>
          <div class="card-body small">
            @php $edu = $inmate->education_details; if(is_string($edu)){ $d=json_decode($edu,true); if(json_last_error()===JSON_ERROR_NONE) $edu=$d; } @endphp
            @if(is_array($edu) && !empty($edu))
              <ul class="mb-0 ps-3">
                @foreach($edu as $ek=>$ev)
                  <li><strong>{{ Str::of($ek)->replace(['_','-'],' ')->title() }}:</strong> @if(is_array($ev)){{ implode(', ', array_map(fn($x)=> is_scalar($x)? $x : json_encode($x), $ev)) }}@else{{ $ev }}@endif</li>
                @endforeach
              </ul>
            @elseif(is_string($edu) && trim($edu)!=='')
              <div>{{ $edu }}</div>
            @else
              <span class="text-muted">—</span>
            @endif
          </div>
        </div>
      </div>
      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <span>Notes</span>
            <span class="small text-muted">Case Notes Included</span>
          </div>
          <div class="card-body small">
            <div class="mb-2"><strong>General:</strong> {!! $inmate->notes ? nl2br(e($inmate->notes)) : '<span class="text-muted">—</span>' !!}</div>
            <div><strong>Case:</strong> {!! $inmate->case_notes ? nl2br(e($inmate->case_notes)) : '<span class="text-muted">—</span>' !!}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>