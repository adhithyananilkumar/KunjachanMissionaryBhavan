<x-app-layout>
  <x-slot name="header"><h2 class="h5 mb-0">Doctors</h2></x-slot>

  <div class="card shadow-sm">
    <div class="list-group list-group-flush">
      @forelse($doctors as $doc)
        @php $assigned = $doc->assignedInmates()->where('institution_id', auth()->user()->institution_id)->count(); @endphp
        <div class="list-group-item d-flex align-items-center justify-content-between position-relative">
          <div class="d-flex align-items-center gap-3 flex-grow-1">
            <img src="{{ $doc->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($doc->name) }}" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;" alt="avatar">
            <div class="flex-grow-1">
              <div class="fw-semibold">{{ $doc->name }}</div>
              <div class="text-muted small">{{ $doc->email }}</div>
            </div>
            <span class="badge text-bg-light"><span class="bi bi-people me-1"></span>{{ $assigned }} patients</span>
            <a href="{{ route('admin.doctors.show',$doc) }}" class="stretched-link"></a>
          </div>
          <div class="dropdown ms-2">
            <button class="btn btn-sm btn-light border-0" data-bs-toggle="dropdown" type="button" aria-expanded="false"><span class="bi bi-three-dots"></span></button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="{{ route('admin.doctors.show',$doc) }}"><span class="bi bi-person-lines-fill me-2"></span>View Profile & Schedule</a></li>
              {{-- Add more admin actions for doctor here if needed --}}
            </ul>
          </div>
        </div>
      @empty
        <div class="list-group-item text-center text-muted py-4">No doctors found.</div>
      @endforelse
    </div>
    @if($doctors->hasPages())<div class="card-footer">{{ $doctors->links() }}</div>@endif
  </div>
</x-app-layout>
