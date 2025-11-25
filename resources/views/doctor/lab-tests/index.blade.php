<x-app-layout>
  <x-slot name="header"><h2 class="h5 mb-0">Lab Tests</h2></x-slot>

  <div class="card shadow-sm">
    <div class="list-group list-group-flush">
      @forelse($tests as $t)
  <div class="list-group-item position-relative d-flex align-items-start justify-content-between gap-3">
          <a href="{{ route('doctor.lab-tests.show', $t) }}" class="stretched-link" aria-label="Open {{ $t->test_name }} details"></a>
          <div class="flex-grow-1">
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <div class="fw-semibold">{{ $t->test_name }}</div>
            </div>
            <div class="text-muted small d-flex flex-wrap gap-2">
              <span>Patient: <a href="{{ route('doctor.inmates.show',$t->inmate_id) }}" class="text-decoration-none">{{ $t->inmate?->full_name ?? ('Patient #'.$t->inmate_id) }}</a></span>
              <span>Ordered: {{ optional($t->ordered_date)->format('Y-m-d') ?: '—' }}</span>
              @if($t->completed_date)<span>Completed: {{ optional($t->completed_date)->format('Y-m-d') }}</span>@endif
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <div class="dropdown">
              <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">Actions</button>
              <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item" href="{{ route('doctor.lab-tests.show', $t) }}"><span class="bi bi-eye me-2"></span>Details</a>
                @if($t->result_file_path)
                  @php $disk = Storage::disk(config('filesystems.default')); $url = $t->result_file_path ? (config('filesystems.default')==='s3' ? $disk->temporaryUrl($t->result_file_path, now()->addMinutes(5)) : $disk->url($t->result_file_path)) : null; @endphp
                  @if($url)
                  <a class="dropdown-item" target="_blank" href="{{ $url }}"><span class="bi bi-file-earmark-pdf me-2"></span>View Report</a>
                  @endif
                @endif
                @if(!$t->reviewed_at && $t->status==='completed')
                  <form method="POST" action="{{ route('doctor.lab-tests.update',$t) }}" onsubmit="return confirm('Accept and lock this report?');">
                    @csrf @method('PUT')
                    <input type="hidden" name="accept" value="1">
                    <input type="hidden" name="status" value="{{ $t->status }}">
                    <button class="dropdown-item text-success" type="submit"><span class="bi bi-check2-circle me-2"></span>Accept</button>
                  </form>
                @endif
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="list-group-item text-center text-muted py-4">No lab tests found.</div>
      @endforelse
    </div>
    @if($tests->hasPages())<div class="card-footer">{{ $tests->links() }}</div>@endif
  </div>
</x-app-layout>
