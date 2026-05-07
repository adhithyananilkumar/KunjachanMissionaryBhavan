@php
  // Show only doctor-created medical records; exclude lab and medication change events from history.
  $timeline = $inmate->medicalRecords()->with(['doctor','medications','labTest'])->latest()->get()->map(function($r){ return [
    'type' => 'record',
    'when' => $r->created_at,
    'by' => $r->doctor?->name ?? 'Doctor',
    'payload' => $r,
  ];});
@endphp

@if($timeline->isEmpty())
  <div class="text-center text-muted py-4">No medical history yet.</div>
@else
  <div class="vstack gap-2">
    @foreach($timeline as $item)
      @php $record = $item['payload']; @endphp
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
            <div>
              <div class="fw-semibold mb-1">Diagnosis</div>
              <div class="text-wrap" style="white-space:pre-wrap">{{ $record->diagnosis }}</div>
              @if($record->labTest)
                <div class="mt-2">
                  <div class="fw-semibold">Linked Lab Test</div>
                  <div class="small">
                    <a href="{{ route('doctor.lab-tests.show', $record->labTest) }}" class="text-decoration-none">
                      <span class="bi bi-beaker me-1"></span>{{ $record->labTest->test_name }}
                    </a>
                    @if($record->labTest->completed_date)
                      <span class="text-muted"> · Completed: {{ optional($record->labTest->completed_date)->format('Y-m-d') }}</span>
                    @endif
                  </div>
                  @if($record->labTest->result_notes)
                    <div class="text-wrap small mt-1" style="white-space:pre-wrap">Result: {{ $record->labTest->result_notes }}</div>
                  @elseif($record->labTest->result_file_path)
                    @php $disk = Storage::disk(config('filesystems.default')); $rurl = $record->labTest && $record->labTest->result_file_path ? (config('filesystems.default')==='s3' ? $disk->temporaryUrl($record->labTest->result_file_path, now()->addMinutes(5)) : $disk->url($record->labTest->result_file_path)) : null; @endphp
                    @if($rurl)<div class="small mt-1"><a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ $rurl }}"><span class="bi bi-file-earmark-pdf me-1"></span>View Report</a></div>@endif
                  @endif
                </div>
              @endif
              @if($record->prescription)
                <div class="mt-2">
                  <div class="fw-semibold">Prescription</div>
                  <div class="text-wrap" style="white-space:pre-wrap">{{ $record->prescription }}</div>
                </div>
              @endif
              @if($record->medications && $record->medications->count())
                <div class="mt-2">
                  <div class="fw-semibold small text-uppercase text-muted">Medications</div>
                  <ul class="small mb-0">
                    @foreach($record->medications as $m)
                      <li><strong>{{ $m->name }}</strong>@if($m->dosage) — {{ $m->dosage }} @endif @if($m->route) ({{ $m->route }}) @endif @if($m->frequency) — {{ $m->frequency }} @endif</li>
                    @endforeach
                  </ul>
                </div>
              @endif
            </div>
            <div class="text-md-end text-muted small">
              <div>{{ $item['by'] }}</div>
              <div>{{ $item['when']->format('Y-m-d H:i') }}</div>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endif
