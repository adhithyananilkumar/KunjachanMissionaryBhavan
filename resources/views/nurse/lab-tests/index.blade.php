<x-app-layout>
  <x-slot name="header"><h2 class="h5 mb-0">Lab Tests</h2></x-slot>

  <div class="card shadow-sm">
    <div class="list-group list-group-flush">
      @forelse($tests as $t)
        <div class="list-group-item d-flex align-items-start justify-content-between gap-3">
          <div class="flex-grow-1">
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <a href="{{ route('nurse.lab-tests.show',$t) }}" class="fw-semibold text-decoration-none">{{ $t->test_name }}</a>
              @php
                $status = $t->status;
                $badge = 'secondary';
                if($status==='ordered') $badge='warning';
                elseif($status==='in_progress') $badge='info';
                elseif($status==='completed') $badge='primary';
              @endphp
              <span class="badge text-bg-{{ $badge }} text-uppercase">{{ str_replace('_',' ',$status) }}</span>
            </div>
            <div class="text-muted small">
              Inmate: {{ $t->inmate?->full_name ?? ('Inmate #'.$t->inmate_id) }}
              <span class="mx-2">•</span>
              Ordered: {{ optional($t->ordered_date)->format('Y-m-d') ?: '—' }}
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            @if($t->result_file_path)
              @php $disk = Storage::disk(config('filesystems.default')); $url = $t->result_file_path ? (config('filesystems.default')==='s3' ? $disk->temporaryUrl($t->result_file_path, now()->addMinutes(5)) : $disk->url($t->result_file_path)) : null; @endphp
              @if($url)
              <a target="_blank" href="{{ $url }}" class="btn btn-sm btn-outline-primary">View Report</a>
              @endif
            @endif
            <a href="{{ route('nurse.lab-tests.show',$t) }}" class="btn btn-sm btn-outline-secondary">Manage</a>
          </div>
        </div>
      @empty
        <div class="list-group-item text-center text-muted py-4">No lab tests found.</div>
      @endforelse
    </div>
    @if($tests->hasPages())<div class="card-footer">{{ $tests->links() }}</div>@endif
  </div>
</x-app-layout>
