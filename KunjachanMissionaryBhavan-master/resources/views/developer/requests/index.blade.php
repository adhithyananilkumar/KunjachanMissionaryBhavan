<x-app-layout>
    <x-slot name="header"><h2 class="h5 mb-0">Incoming Action Requests</h2></x-slot>
    <div class="card mb-4">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead><tr><th>ID</th><th>Admin</th><th>Subject</th><th>Status</th><th>Updated</th><th></th></tr></thead>
                <tbody>
                    @forelse($requests as $r)
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td>{{ $r->admin->name }}</td>
                            <td>{{ $r->subject }}</td>
                            <td><span class="badge text-bg-{{ $r->status === 'pending' ? 'warning' : ($r->status === 'Approved' ? 'success':'secondary') }}">{{ $r->status }}</span></td>
                            <td>{{ $r->updated_at->diffForHumans() }}</td>
                            <td class="text-end"><button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updateReqModal" data-id="{{ $r->id }}" data-status="{{ $r->status }}" data-reply="{{ $r->developer_reply }}">Update</button></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4">No requests.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())<div class="card-footer">{{ $requests->links() }}</div>@endif
    </div>
</x-app-layout>

<div class="modal fade" id="updateReqModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" id="updateReqForm">
        @csrf
        @method('PATCH')
        <div class="modal-header"><h5 class="modal-title">Update Request</h5><button class="btn-close" type="button" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Status</label>
            <select name="status" id="reqStatus" class="form-select" required>
              <option value="pending">Pending</option>
              <option value="Approved">Approved</option>
              <option value="Rejected">Rejected</option>
            </select>
          </div>
          <div class="mb-3"><label class="form-label">Developer Reply</label><textarea class="form-control" name="developer_reply" id="reqReply" rows="4" placeholder="Your reply / action details..."></textarea></div>
        </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      <button class="btn btn-primary" type="button" id="updateReqSubmit">Save</button>
    </div>
      </form>
    </div>
  </div>
</div>
<script>
 document.addEventListener('DOMContentLoaded',()=>{
   const modal=document.getElementById('updateReqModal');
  const btn=document.getElementById('updateReqSubmit');
  const form=document.getElementById('updateReqForm');
  modal?.addEventListener('keydown', function(ev){ if(ev.key==='Enter'){ ev.preventDefault(); }});
  btn?.addEventListener('click', function(){ form?.submit(); });
   modal?.addEventListener('show.bs.modal', e=>{
     const btn=e.relatedTarget; if(!btn) return;
     const id=btn.getAttribute('data-id');
     const status=btn.getAttribute('data-status');
     const reply=btn.getAttribute('data-reply');
     document.getElementById('updateReqForm').action='{{ url('developer/requests') }}/'+id;
     document.getElementById('reqStatus').value=status;
     document.getElementById('reqReply').value=reply||'';
   });
 });
</script>
