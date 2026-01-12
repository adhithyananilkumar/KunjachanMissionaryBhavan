<x-app-layout>
    <x-slot name="header"><h2 class="h5 mb-0">Bug Reports</h2></x-slot>
  <div class="card mb-4">
    <form method="GET" class="card-header d-flex flex-wrap gap-2 align-items-end">
      <div>
        <label class="form-label small mb-1">Status</label>
        <select name="status" class="form-select form-select-sm">
          <option value="">All</option>
          <option value="open" @selected(request('status')==='open')>Open</option>
          <option value="in_progress" @selected(request('status')==='in_progress')>In Progress</option>
          <option value="closed" @selected(request('status')==='closed')>Closed</option>
        </select>
      </div>
      <div>
        <label class="form-label small mb-1">User</label>
        <input type="text" name="user" value="{{ request('user') }}" class="form-control form-control-sm" placeholder="Search user">
      </div>
      <button class="btn btn-sm btn-primary">Filter</button>
      @if(request()->hasAny(['status','user']))
        <a href="{{ route('developer.bugs.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
      @endif
    </form>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
        <thead><tr><th>ID</th><th>User</th><th>Title</th><th>Description</th><th>Status</th><th>Reply</th><th>Files</th><th></th></tr></thead>
                <tbody>
                    @forelse($bugs as $b)
                        <tr>
                            <td>{{ $b->id }}</td>
                            <td>{{ $b->user->name }}</td>
              <td>{{ \Illuminate\Support\Str::limit($b->title,30) }}</td>
              <td style="max-width:260px; white-space:pre-wrap">{{ \Illuminate\Support\Str::limit($b->description,120) }}</td>
                            <td><span class="badge text-bg-{{ $b->status==='open'?'warning':($b->status==='closed'?'secondary':'info') }}">{{ $b->status }}</span></td>
              <td style="max-width:200px; white-space:pre-wrap">{{ $b->developer_reply? \Illuminate\Support\Str::limit($b->developer_reply,80):'—' }}</td>
              <td>
        @php $disk = Storage::disk(config('filesystems.default')); @endphp
        @if($b->screenshot_path)
          @php $u1 = config('filesystems.default')==='s3' ? $disk->temporaryUrl($b->screenshot_path, now()->addMinutes(5)) : $disk->url($b->screenshot_path); @endphp
          <a href="{{ $u1 }}" target="_blank" class="btn btn-sm btn-outline-dark me-1">User</a>
        @endif
        @if($b->developer_attachment_path)
          @php $u2 = config('filesystems.default')==='s3' ? $disk->temporaryUrl($b->developer_attachment_path, now()->addMinutes(5)) : $disk->url($b->developer_attachment_path); @endphp
          <a href="{{ $u2 }}" target="_blank" class="btn btn-sm btn-outline-success">Dev</a>
        @endif
                @if(!$b->screenshot_path && !$b->developer_attachment_path) — @endif
              </td>
                            <td class="text-end"><button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updateBugModal" data-id="{{ $b->id }}" data-status="{{ $b->status }}" data-reply="{{ $b->developer_reply }}">Update</button></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4">No bug reports.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bugs->hasPages())<div class="card-footer">{{ $bugs->links() }}</div>@endif
    </div>
</x-app-layout>

<div class="modal fade" id="updateBugModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
  <form method="POST" id="updateBugForm" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="modal-header"><h5 class="modal-title">Update Bug Report</h5><button class="btn-close" type="button" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Status</label>
            <select class="form-select" name="status" id="bugStatus" required>
              <option value="open">Open</option>
              <option value="in_progress">In Progress</option>
              <option value="closed">Closed</option>
            </select>
          </div>
          <div class="mb-3"><label class="form-label">Developer Reply</label><textarea name="developer_reply" id="bugReply" class="form-control" rows="4"></textarea></div>
          <div class="mb-3"><label class="form-label">Attach File (optional)</label><input type="file" name="developer_attachment" class="form-control"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-primary" type="submit">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
 document.addEventListener('DOMContentLoaded',()=>{
   const modal=document.getElementById('updateBugModal');
   modal?.addEventListener('show.bs.modal', e=>{
     const btn=e.relatedTarget; if(!btn) return;
     const id=btn.getAttribute('data-id');
     const status=btn.getAttribute('data-status');
     const reply=btn.getAttribute('data-reply');
     document.getElementById('updateBugForm').action='{{ url('developer/bug-reports') }}/'+id;
     document.getElementById('bugStatus').value=status;
     document.getElementById('bugReply').value=reply||'';
   });
 });
</script>
