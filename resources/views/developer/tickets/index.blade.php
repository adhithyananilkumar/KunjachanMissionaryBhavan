<x-app-layout>
  <x-slot name="header"><h2 class="h5 mb-0">Support Tickets</h2></x-slot>
  <div class="card mb-3">
    <form class="card-header d-flex gap-2 flex-wrap" method="GET">
      <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search title">
      <select name="status" class="form-select form-select-sm" style="max-width:140px">
        <option value="">All</option>
        <option value="open" @selected(request('status')==='open')>Open</option>
        <option value="in_progress" @selected(request('status')==='in_progress')>In Progress</option>
        <option value="closed" @selected(request('status')==='closed')>Closed</option>
      </select>
      <button class="btn btn-sm btn-primary">Filter</button>
      @if(request()->hasAny(['search','status']))<a class="btn btn-sm btn-outline-secondary" href="{{ route('developer.tickets.index') }}">Reset</a>@endif
    </form>
    <div class="table-responsive">
      <table class="table table-hover mb-0 align-middle">
        <thead><tr><th>ID</th><th>Title</th><th>User</th><th>Status</th><th>Updated</th><th></th></tr></thead>
        <tbody>
          @forelse($tickets as $t)
            <tr>
              <td>{{ $t->id }}</td>
              <td>{{ Str::limit($t->title,60) }}</td>
              <td>{{ $t->user->name }}</td>
              <td><span class="badge text-bg-{{ $t->status==='open'?'warning':($t->status==='closed'?'secondary':'info') }}">{{ $t->status }}</span></td>
              <td>{{ $t->last_activity_at? $t->last_activity_at->diffForHumans() : '—' }}</td>
              <td class="text-end"><a href="{{ route('developer.tickets.show',$t) }}" class="btn btn-sm btn-outline-primary">Open</a></td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center py-4">No tickets.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($tickets->hasPages())<div class="card-footer">{{ $tickets->links() }}</div>@endif
  </div>
</x-app-layout>
