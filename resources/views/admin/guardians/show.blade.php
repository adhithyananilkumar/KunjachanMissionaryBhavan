<x-app-layout>
  <x-slot name="header">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
      <h2 class="h5 mb-0 d-flex align-items-center gap-2">
        <i class="bi bi-person-badge fs-5 text-primary"></i>
        <span>{{ $guardian->full_name }}</span>
        <span class="badge bg-secondary">Guardian</span>
      </h2>
      <div class="d-flex gap-2">
        <a href="{{ route('admin.guardians.edit',$guardian) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil-square me-1"></i>Edit</a>
        <a href="{{ route('admin.guardians.index') }}" class="btn btn-light btn-sm">Back</a>
      </div>
    </div>
  </x-slot>

  <div class="card shadow-sm">
    <div class="card-header border-0 pb-0">
      <ul class="nav nav-tabs card-header-tabs" role="tablist">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-overview" type="button">Overview</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-portal" type="button">Guardian Portal</button></li>
      </ul>
    </div>
    <div class="card-body tab-content">
      <div id="tab-overview" class="tab-pane fade show active">
        <div class="row g-3">
          <div class="col-md-6">
            <div class="card h-100 border-0 bg-light">
              <div class="card-body">
                <div class="fw-semibold mb-2">Contact</div>
                <div class="small"><span class="text-muted">Phone:</span> {{ $guardian->phone_number ?: '—' }}</div>
                <div class="small"><span class="text-muted">Address:</span> {{ $guardian->address ?: '—' }}</div>
                @if($user)
                <hr class="my-2"><div class="small"><span class="text-muted">Email:</span> {{ $user->email }}</div>
                @endif
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card h-100 border-0 bg-light">
              <div class="card-body">
                <div class="fw-semibold mb-2">Linked Inmate</div>
                @if($inmate)
                  <div class="d-flex align-items-center gap-2">
                    <img src="{{ $inmate->avatar_url }}" width="36" height="36" class="rounded-circle" alt>
                    <div>
                      <div><a class="text-decoration-none" href="{{ route('admin.inmates.show',$inmate) }}">{{ $inmate->full_name }}</a></div>
                      <div class="small text-muted">{{ $inmate->institution->name ?? '—' }}</div>
                    </div>
                  </div>
                @else
                  <div class="text-muted small">Not linked</div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>

      <div id="tab-portal" class="tab-pane fade">
        <div class="row g-3">
          <div class="col-lg-7">
            <div class="card h-100">
              <div class="card-header d-flex align-items-center justify-content-between">
                <div class="fw-semibold">Secure Messages</div>
              </div>
              <div class="card-body" style="max-height:420px;overflow:auto">
                <div class="vstack gap-2">
                  @forelse($messages as $m)
                  <div class="d-flex {{ $m->sent_by_guardian ? 'justify-content-start' : 'justify-content-end' }}">
                    <div class="p-2 rounded {{ $m->sent_by_guardian ? 'bg-light' : 'bg-primary text-white' }}" style="max-width:80%">
                      <div class="small">{{ $m->message_text }}</div>
                      <div class="text-muted small mt-1">{{ $m->created_at->format('Y-m-d H:i') }}</div>
                    </div>
                  </div>
                  @empty
                  <div class="text-muted small">No messages yet.</div>
                  @endforelse
                </div>
              </div>
              <div class="card-footer">
                <form method="POST" action="{{ route('admin.guardian-messages.reply', $guardian) }}" id="adminPortalReplyForm" class="d-flex gap-2">
                  @csrf
                  <textarea name="message_text" class="form-control" rows="1" placeholder="Type your reply..." required></textarea>
                  <button type="button" class="btn btn-primary" id="adminPortalReplyBtn">Send</button>
                </form>
              </div>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="card h-100">
              <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                <span>Shared Documents</span>
                @if($inmate)
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.inmates.show',$inmate) }}">Manage All</a>
                @endif
              </div>
              <div class="card-body">
                @if(!$inmate)
                  <div class="text-muted small">Link an inmate to manage sharing.</div>
                @else
                  @php($docs = $inmate->documents()->latest()->get())
                  @if($docs->isEmpty())
                    <div class="text-muted small">No extra documents.</div>
                  @else
                    <div class="vstack gap-2">
                      @foreach($docs as $d)
                        <div class="d-flex justify-content-between align-items-center border rounded p-2">
                          @php $disk = Storage::disk(config('filesystems.default')); $url = config('filesystems.default')==='s3' ? $disk->temporaryUrl($d->file_path, now()->addMinutes(5)) : $disk->url($d->file_path); @endphp
                          <a class="text-decoration-none d-flex align-items-center gap-2" href="{{ $url }}" target="_blank">
                            <span class="bi bi-file-earmark-text text-muted"></span>{{ $d->document_name }}
                          </a>
                          <form method="POST" action="{{ route('admin.inmates.documents.toggle-share', [$inmate, $d]) }}" class="m-0">@csrf
                            <button type="submit" class="btn btn-sm {{ $d->is_sharable_with_guardian ? 'btn-success' : 'btn-outline-secondary' }}">
                              <span class="bi bi-share me-1"></span>{{ $d->is_sharable_with_guardian ? 'Shared' : 'Share' }}
                            </button>
                          </form>
                        </div>
                      @endforeach
                    </div>
                  @endif
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script>
  (function(){
    const body = document.querySelector('#tab-portal .card-body');
    if(body){ body.scrollTop = body.scrollHeight; }
    const form = document.getElementById('adminPortalReplyForm');
    const btn = document.getElementById('adminPortalReplyBtn');
    form?.closest('.card')?.addEventListener('keydown', function(ev){ if(ev.key==='Enter'){ ev.preventDefault(); }});
    btn?.addEventListener('click', function(){ form?.submit(); });
  })();
  </script>
  @endpush
</x-app-layout>
