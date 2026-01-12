<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h2 class="h5 mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-person-badge fs-5 text-primary"></i>
                    <span>{{ $guardian->full_name }}</span>
                    <span class="badge rounded-pill bg-secondary">Guardian</span>
                </h2>
                <div class="small text-muted mt-1">
                    @if($inmate)
                        Linked to inmate: <a href="{{ route('system_admin.inmates.show',$inmate) }}" class="text-decoration-none">{{ $inmate->full_name }}</a>
                        @if($inmate->institution)
                            <span class="ms-1 text-muted">• {{ $inmate->institution->name }}</span>
                        @endif
                    @else
                        Not linked to any inmate yet
                    @endif
                </div>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary btn-sm" id="edit-inline-btn"><i class="bi bi-pencil-square me-1"></i>Edit</button>
                <div class="dropdown">
                    <button class="btn btn-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">More</button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('system_admin.guardians.edit',$guardian) }}">Open full page</a></li>
                    </ul>
                </div>
                <a href="{{ route('system_admin.guardians.index') }}" class="btn btn-light btn-sm">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="card shadow-sm">
        <div class="card-header border-0 pb-0">
            <ul class="nav nav-tabs card-header-tabs" id="guardianTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview-pane" type="button" role="tab">Overview</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-pane" type="button" role="tab">Profile</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="portal-tab" data-bs-toggle="tab" data-bs-target="#portal-pane" type="button" role="tab">Guardian Portal</button>
                </li>
            </ul>
        </div>
        <div class="card-body tab-content">
            <div class="tab-pane fade show active" id="overview-pane" role="tabpanel">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <div class="card h-100 border-0 bg-light">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-person-circle fs-3 text-primary me-2"></i>
                                    <div>
                                        <div class="fw-semibold">{{ $guardian->full_name }}</div>
                                        <div class="small text-muted">{{ $guardian->phone_number ?: '—' }}</div>
                                    </div>
                                </div>
                                <div class="small">
                                    <div class="text-muted mb-1">Address</div>
                                    <div>{{ $guardian->address ?: '—' }}</div>
                                </div>
                                @if($user)
                                    <hr>
                                    <div class="small">
                                        <div class="text-muted mb-1">User Account</div>
                                        <div>Email: {{ $user->email }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="card h-100 border-0 bg-light">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-people fs-4 text-primary me-2"></i>
                                    <div class="fw-semibold">Linked Inmate</div>
                                </div>
                                @if($inmate)
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $inmate->avatar_url }}" class="rounded-circle" width="36" height="36" alt="avatar">
                                        <div>
                                            <div><a href="{{ route('system_admin.inmates.show',$inmate) }}" class="text-decoration-none">{{ $inmate->full_name }}</a></div>
                                            <div class="small text-muted">{{ $inmate->institution->name ?? '—' }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-muted small">No inmate linked.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="profile-pane" role="tabpanel">
                <div id="profile-view">
                    <dl class="row mb-0 small">
                        <dt class="col-sm-3">Full name</dt><dd class="col-sm-9">{{ $guardian->full_name }}</dd>
                        <dt class="col-sm-3">Phone</dt><dd class="col-sm-9">{{ $guardian->phone_number ?: '—' }}</dd>
                        <dt class="col-sm-3">Address</dt><dd class="col-sm-9">{{ $guardian->address ?: '—' }}</dd>
                        @if($user)
                            <dt class="col-sm-3">Email</dt><dd class="col-sm-9">{{ $user->email }}</dd>
                        @endif
                    </dl>
                </div>
                <div id="profile-edit" class="d-none">
                    @include('system_admin.guardians.edit', ['guardian' => $guardian])
                </div>
            </div>

            <div class="tab-pane fade" id="portal-pane" role="tabpanel">
                <div class="row g-3">
                    <div class="col-lg-7">
                        <div class="card h-100">
                            <div class="card-header fw-semibold">Secure Messages</div>
                            <div class="card-body" style="max-height:420px;overflow:auto">
                                <div class="vstack gap-2">
                                    @forelse(($messages ?? collect()) as $m)
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
                                <form method="POST" action="{{ route('system_admin.guardians.messages.reply', $guardian) }}" id="saReplyForm" class="d-flex gap-2">
                                    @csrf
                                    <textarea name="message_text" class="form-control" rows="1" placeholder="Type your reply..." required></textarea>
                                    <button type="button" class="btn btn-primary" id="saReplyBtn">Send</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card h-100">
                            <div class="card-header fw-semibold">Shared Documents</div>
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
                                                    <form method="POST" action="{{ route('system_admin.inmates.documents.toggle-share', [$inmate, $d]) }}">@csrf
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
            const editBtn = document.getElementById('edit-inline-btn');
            const profileTabBtn = document.getElementById('profile-tab');
            const profilePane = document.getElementById('profile-pane');
            const profileView = document.getElementById('profile-view');
            const profileEdit = document.getElementById('profile-edit');

            function ensureProfileTab(){
                if(!profilePane.classList.contains('show')){
                    new bootstrap.Tab(profileTabBtn).show();
                }
            }

            editBtn?.addEventListener('click', function(){
                ensureProfileTab();
                profileView.classList.add('d-none');
                profileEdit.classList.remove('d-none');
                // Convert inner edit form to AJAX submit
                const form = profileEdit.querySelector('form');
                if(form && !form.dataset.wired){
                    form.dataset.wired = '1';
                    form.addEventListener('submit', async function(e){
                        e.preventDefault();
                        const fd = new FormData(form);
                        fd.set('_method','PUT');
                        const resp = await fetch(form.action, {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':fd.get('_token')}, body: fd});
                        if(resp.ok){
                            try{ await resp.json(); }catch(e){}
                            toastr.success('Updated');
                            // reload profile pane to reflect changes without full page refresh
                            window.location.reload();
                        }else{
                            toastr.error('Update failed');
                        }
                    });
                }
            });
        })();
    </script>
    @endpush
</x-app-layout>
