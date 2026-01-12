<x-app-layout>
  <x-slot name="header"><h2 class="h5 mb-0">Doctors</h2></x-slot>
  <div class="d-flex align-items-center gap-2 mb-3">
    <form method="GET" class="d-flex align-items-center gap-2">
      <select name="institution_id" class="form-select form-select-sm" onchange="this.form.submit()">
        <option value="">All Institutions</option>
        @foreach($institutions as $inst)
          <option value="{{ $inst->id }}" @selected($institutionId==$inst->id)>{{ $inst->name }}</option>
        @endforeach
      </select>
      <noscript><button class="btn btn-sm btn-primary">Filter</button></noscript>
    </form>
  </div>
  <div class="card">
    <div class="table-responsive">
      <table class="table table-striped mb-0">
        <thead class="table-light"><tr><th>Name</th><th>Institution</th><th>Email</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
          @forelse($doctors as $doc)
          <tr>
            <td>{{ $doc->name }}</td>
            <td>{{ $doc->institution?->name ?? '—' }}</td>
            <td>{{ $doc->email }}</td>
            <td class="text-end"><a href="{{ route('developer.doctors.show',$doc) }}" class="btn btn-sm btn-outline-primary">View & Assign</a></td>
          </tr>
          @empty
          <tr><td colspan="4" class="text-center py-4">No doctors found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($doctors->hasPages())<div class="card-footer">{{ $doctors->links() }}</div>@endif
  </div>
</x-app-layout>
