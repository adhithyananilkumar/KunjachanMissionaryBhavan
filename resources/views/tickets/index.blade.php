<x-app-layout>
  <x-slot name="header"><h2 class="h5 mb-0">My Support Tickets</h2></x-slot>
  <div class="mb-3 text-end"><a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newTicketModal">New Ticket</a></div>
  <div class="card">
    <div class="table-responsive">
      <table class="table table-striped mb-0">
        <thead><tr><th>ID</th><th>Title</th><th>Status</th><th>Updated</th><th></th></tr></thead>
        <tbody>
          @forelse($tickets as $t)
            <tr>
              <td>{{ $t->id }}</td>
              <td>{{ Str::limit($t->title,70) }}</td>
              <td><span class="badge text-bg-{{ $t->status==='open'?'warning':($t->status==='closed'?'secondary':'info') }}">{{ $t->status }}</span></td>
              <td>{{ $t->last_activity_at? $t->last_activity_at->diffForHumans():'—' }}</td>
              <td class="text-end"><a href="{{ route('tickets.show',$t) }}" class="btn btn-sm btn-outline-primary">Open</a></td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center py-4">No tickets.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($tickets->hasPages())<div class="card-footer">{{ $tickets->links() }}</div>@endif
  </div>

  <div class="modal fade" id="newTicketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
  <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" id="newTicketForm">@csrf
          <div class="modal-header"><h5 class="modal-title">Create Ticket</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <div class="mb-3"><label class="form-label">Title</label><input name="title" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Description</label><textarea name="description" rows="4" class="form-control" required></textarea></div>
            <div class="mb-3"><label class="form-label">Screenshot (optional)</label><input type="file" name="screenshot" class="form-control" accept="image/*"></div>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="newTicketSubmitBtn">Submit</button></div>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>

@push('scripts')
<script>
(function(){
  const m = document.getElementById('newTicketModal');
  const btn = document.getElementById('newTicketSubmitBtn');
  const form = document.getElementById('newTicketForm');
  m?.addEventListener('keydown', function(ev){ if(ev.key==='Enter'){ ev.preventDefault(); }});
  btn?.addEventListener('click', function(){ form?.submit(); });
})();
</script>
@endpush
