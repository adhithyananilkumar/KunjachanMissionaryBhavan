<x-app-layout>
  <x-slot name="header"><h2 class="h5 mb-0">Ticket {{ $ticket->public_id }} - {{ $ticket->title }}</h2></x-slot>
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card mb-3">
        <div class="card-body">
          <h6 class="fw-semibold mb-2">Original Description</h6>
          <p style="white-space:pre-wrap" class="mb-2">{{ $ticket->description }}</p>
          @php $disk = Storage::disk(config('filesystems.default')); @endphp
          @php $screens = $ticket->screenshot_paths ?: ($ticket->screenshot_path ? [$ticket->screenshot_path] : []); @endphp
          @if(!empty($screens))
            <div class="d-flex flex-wrap gap-2">
              @foreach($screens as $p)
                @php $url = $p ? (config('filesystems.default')==='s3' ? $disk->temporaryUrl($p, now()->addMinutes(5)) : $disk->url($p)) : null; @endphp
                @if($url)
                  <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-outline-dark">View Attachment</a>
                @endif
              @endforeach
            </div>
          @endif
        </div>
      </div>
      <div class="card mb-3">
        <div class="card-header">Conversation</div>
        <div class="card-body" style="max-height:400px; overflow-y:auto">
          @forelse($ticket->replies as $r)
            <div class="mb-3 d-flex {{ $r->user_id==auth()->id() ? 'justify-content-end':'' }}">
              <div class="p-3 rounded-3 {{ $r->user->role==='developer' ? 'bg-primary text-white':'bg-light border' }}" style="max-width:70%">
                <div class="small fw-semibold mb-1">{{ $r->user->name }} <span class="text-muted fw-normal">• {{ $r->created_at instanceof \Illuminate\Support\Carbon ? $r->created_at->diffForHumans() : (method_exists($r->created_at,'diffForHumans') ? $r->created_at->diffForHumans() : (string) $r->created_at) }}</span></div>
                <div style="white-space:pre-wrap">{{ $r->message }}</div>
                @php $att = $r->attachments ?: ($r->attachment_path ? [$r->attachment_path] : []); @endphp
                @if(!empty($att))
                  <div class="mt-2 d-flex flex-wrap gap-2">
                    @foreach($att as $p)
                      @php $rurl = $p ? (config('filesystems.default')==='s3' ? $disk->temporaryUrl($p, now()->addMinutes(5)) : $disk->url($p)) : null; @endphp
                      @if($rurl)
                        <a href="{{ $rurl }}" target="_blank" class="btn btn-xs btn-outline-light">Attachment</a>
                      @endif
                    @endforeach
                  </div>
                @endif
              </div>
            </div>
          @empty
            <p class="text-muted">No replies yet.</p>
          @endforelse
        </div>
        <div class="card-footer">
          @if(in_array($ticket->status, ['resolved','closed'], true) || $ticket->archived_at)
            <div class="text-muted small">This ticket is solved. Replies are disabled.</div>
          @else
          <form method="POST" action="{{ route('tickets.reply',$ticket) }}" enctype="multipart/form-data" class="d-flex flex-column gap-2">
            @csrf
            <textarea name="message" class="form-control" rows="3" placeholder="Type your reply..." required></textarea>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
              <input type="file" name="attachments[]" class="form-control" style="max-width:300px" multiple accept="image/*,.webp,.heic,.heif,.pdf">
              <button class="btn btn-primary">Send Reply</button>
            </div>
          </form>
          @endif
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between"><span class="text-muted">Status</span><span class="badge text-bg-{{ $ticket->status==='open'?'warning':($ticket->status==='in_progress'?'info':($ticket->status==='resolved'?'success':($ticket->status==='closed'?'secondary':'secondary'))) }}">{{ $ticket->status }}</span></div>
          <div class="mt-3 small"><strong>Last Activity:</strong> {{ $ticket->last_activity_at? $ticket->last_activity_at->diffForHumans():'—' }}</div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
