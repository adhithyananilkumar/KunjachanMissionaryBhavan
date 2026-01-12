<div class="row g-3">
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header">Appointments</div>
      <div class="card-body small">
        @php $appts = $inmate->appointments()->orderByDesc('scheduled_for')->limit(10)->get(); @endphp
        @if($appts->isEmpty())
          <div class="text-muted">No appointments.</div>
        @else
          <ul class="list-group list-group-flush">
            @foreach($appts as $a)
              <li class="list-group-item d-flex justify-content-between"><span>{{ $a->scheduled_for?->format('Y-m-d H:i') }} {{ $a->title ? '· '.$a->title : '' }}</span><span class="text-muted">{{ $a->status ?? '' }}</span></li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header">Case Log</div>
      <div class="card-body small">
        @php $logs = $inmate->caseLogEntries()->orderByDesc('entry_date')->limit(10)->get(); @endphp
        @if($logs->isEmpty())
          <div class="text-muted">No case entries.</div>
        @else
          <ul class="list-group list-group-flush">
            @foreach($logs as $log)
              <li class="list-group-item">
                <div class="fw-semibold">{{ $log->entry_date?->format('Y-m-d') }} - {{ $log->title ?? 'Entry' }}</div>
                <div class="text-muted">{{ Str::limit($log->details ?? '', 160) }}</div>
              </li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="card">
      <div class="card-header">Education</div>
      <div class="card-body small">
        @php $edus = $inmate->educationalRecords()->orderByDesc('academic_year')->orderByDesc('id')->limit(10)->get(); @endphp
        @if($edus->isEmpty())
          <div class="text-muted">No educational records.</div>
        @else
          <ul class="list-group list-group-flush">
            @foreach($edus as $er)
              <li class="list-group-item d-flex justify-content-between"><span>{{ $er->school_name ?? 'Institute' }}</span><span class="text-muted">{{ $er->academic_year ?? '' }}</span></li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>
</div>
