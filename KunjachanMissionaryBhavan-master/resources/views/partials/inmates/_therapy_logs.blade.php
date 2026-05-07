@php $logs = $inmate->therapySessionLogs; @endphp
<div class="card mt-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span>Therapy Session Logs</span>
    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#newTherapyLogForm">Add Session</button>
  </div>
  <div class="card-body">
    @if($logs->count())
      <div class="table-responsive">
        <table class="table table-sm table-striped mb-0">
          <thead class="table-light"><tr><th>Date</th><th>Doctor</th><th>Notes</th><th>Created</th></tr></thead>
          <tbody>
            @foreach($logs as $log)
              <tr>
                <td>{{ $log->session_date->format('Y-m-d') }}</td>
                <td>{{ $log->doctor?->name }}</td>
                <td style="white-space:pre-wrap">{{ Str::limit($log->session_notes,120) }}</td>
                <td class="small text-muted">{{ $log->created_at->diffForHumans() }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @else
      <p class="text-muted mb-0">No therapy sessions logged yet.</p>
    @endif
    <div id="newTherapyLogForm" class="collapse mt-3">
      <form method="POST" action="{{ route('doctor.therapy-logs.store',$inmate) }}" class="border rounded p-3 bg-light">@csrf
        <div class="row g-2 mb-2">
          <div class="col-md-3"><label class="form-label small">Session Date</label><input type="date" name="session_date" value="{{ now()->format('Y-m-d') }}" class="form-control form-control-sm" required></div>
          <div class="col-md-9"><label class="form-label small">Notes</label><textarea name="session_notes" rows="3" class="form-control form-control-sm" required></textarea></div>
        </div>
        <div class="text-end"><button class="btn btn-sm btn-success">Save Session</button></div>
      </form>
    </div>
  </div>
</div>
