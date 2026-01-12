<x-app-layout>
  <x-slot name="header"><h2 class="h5 mb-0">Medication Attendance</h2></x-slot>
  <div class="row g-3 mb-3">
    <div class="col-auto"><div class="card p-3"><div class="small text-muted">Taken</div><div class="h5 mb-0">{{ $summary['taken'] }}</div></div></div>
    <div class="col-auto"><div class="card p-3"><div class="small text-muted">Missed</div><div class="h5 mb-0">{{ $summary['missed'] }}</div></div></div>
  </div>
  <div class="card">
    <div class="table-responsive">
      <table class="table table-sm align-middle mb-0">
        <thead class="table-light"><tr><th>Time</th><th>Patient</th><th>Status</th><th>Logged By</th></tr></thead>
        <tbody>
          @foreach($logs as $log)
            <tr>
              <td>{{ $log->administration_time->format('Y-m-d H:i') }}</td>
              <td>{{ $log->medicalRecord?->inmate?->full_name ?? '—' }}</td>
              <td class="text-capitalize">{{ $log->status ?? 'taken' }}</td>
              <td>{{ $log->nurse?->name ?? '—' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @if($logs->hasPages())<div class="card-footer">{{ $logs->links() }}</div>@endif
  </div>
</x-app-layout>
