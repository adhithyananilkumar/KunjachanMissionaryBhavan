<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="h5 mb-0">Profile</h2>
            <button id="toggleEdit" class="btn btn-sm btn-primary">Edit</button>
        </div>
    </x-slot>

    <div class="container py-3">
        <div class="card shadow-sm">
            <div class="card-body">
                @if (session('status') === 'profile-updated')
                    <div class="alert alert-success small py-2">Profile updated.</div>
                @elseif (session('status') === 'password-updated')
                    <div class="alert alert-success small py-2">Password updated.</div>
                @endif

                <div class="d-flex align-items-center gap-3 mb-3">
                    <img id="avatarPreview" src="{{ $user->avatar_url }}" class="rounded-circle" style="width:64px;height:64px;object-fit:cover" alt="avatar">
                    <div>
                        <div class="fw-semibold">{{ $user->name }}</div>
                        <div class="text-muted small">{{ $user->email }}</div>
                    </div>
                </div>

                <!-- Read-only view -->
                <div id="profileView" class="mb-3">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <tbody>
                            <tr><th class="text-muted" style="width:180px;">Name</th><td>{{ $user->name }}</td></tr>
                            <tr><th class="text-muted">Email</th><td>{{ $user->email }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Edit view -->
                <div id="profileEdit" class="d-none">
                    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mb-4" id="profileForm">
                        @csrf
                        @method('patch')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Profile Picture</label>
                                <div class="d-flex align-items-center gap-3">
                                    <img id="avatarPreviewInline" src="{{ $user->avatar_url }}" class="rounded-circle" style="width:56px;height:56px;object-fit:cover" alt="avatar">
                                    <input id="profile_picture" name="profile_picture" type="file" accept="image/*" class="form-control" style="max-width:320px;">
                                    <input type="hidden" name="profile_picture_cropped" id="profile_picture_cropped">
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-3">
                            <button class="btn btn-primary" type="submit">Save Changes</button>
                            <button type="button" class="btn btn-light" id="closeEditTop">Close</button>
                        </div>
                    </form>

                    <div class="card border-0 bg-light mb-4">
                        <div class="card-body">
                            <h6 class="mb-3">Update Password</h6>
                            <form method="post" action="{{ route('password.update') }}" class="row g-3">
                                @csrf
                                @method('put')
                                <div class="col-md-4">
                                    <label class="form-label">Current Password</label>
                                    <input name="current_password" type="password" class="form-control" autocomplete="current-password">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">New Password</label>
                                    <input name="password" type="password" class="form-control" autocomplete="new-password">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Confirm Password</label>
                                    <input name="password_confirmation" type="password" class="form-control" autocomplete="new-password">
                                </div>
                                <div class="col-12 d-flex gap-2 mt-1">
                                    <button class="btn btn-outline-primary" type="submit">Save Password</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="text-danger mb-2">Delete Account</h6>
                            <p class="small text-muted mb-3">Once deleted, all your data will be permanently removed. This action cannot be undone.</p>
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">Delete Account</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="small">Please enter your password to confirm account deletion.</p>
                    <form id="deleteAccountForm" method="post" action="{{ route('profile.destroy') }}">
                        @csrf
                        @method('delete')
                        <div class="mb-2">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" autocomplete="current-password" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" form="deleteAccountForm">Delete</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (function(){
            const btn = document.getElementById('toggleEdit');
            const view = document.getElementById('profileView');
            const edit = document.getElementById('profileEdit');
            const closeTop = document.getElementById('closeEditTop');
            const fileInput = document.getElementById('profile_picture');
            const previewTop = document.getElementById('avatarPreview');
            const previewInline = document.getElementById('avatarPreviewInline');

            function wirePreview(){
                if(!fileInput || fileInput.dataset.previewWired) return;
                fileInput.dataset.previewWired = '1';
                fileInput.addEventListener('change', function(){
                    const f = this.files && this.files[0];
                    if(!f) return;
                    const url = URL.createObjectURL(f);
                    // Show cropper modal
                    const img = document.getElementById('cropperImage');
                    img.src = url;
                    const modalEl = document.getElementById('cropperModal');
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                    setTimeout(()=>{ window.__awCropper && window.__awCropper.destroy?.(); window.__awCropper = new Cropper(img, {aspectRatio:1, viewMode:1}); }, 150);
                });
            }

            function toggleEdit(force){
                const editing = force !== undefined ? !force : !edit.classList.contains('d-none');
                if(editing){
                    edit.classList.add('d-none');
                    view.classList.remove('d-none');
                    btn.textContent = 'Edit';
                } else {
                    view.classList.add('d-none');
                    edit.classList.remove('d-none');
                    btn.textContent = 'Close';
                    wirePreview();
                }
            }

            btn?.addEventListener('click', () => toggleEdit());
            closeTop?.addEventListener('click', () => toggleEdit(true));
                        // On submit, if cropper active, replace hidden with cropped blob (base64)
                        document.getElementById('profileForm')?.addEventListener('submit', function(ev){
                                if(window.__awCropper){
                                        ev.preventDefault();
                                        window.__awCropper.getCroppedCanvas({width:512,height:512}).toBlob((blob)=>{
                                                const reader = new FileReader();
                                                reader.onloadend = ()=>{
                                                        document.getElementById('profile_picture_cropped').value = reader.result;
                                                        // Update previews
                                                        if(previewTop) previewTop.src = reader.result;
                                                        if(previewInline) previewInline.src = reader.result;
                                                        // Submit again
                                                        ev.target.submit();
                                                };
                                                reader.readAsDataURL(blob);
                                        }, 'image/jpeg', 0.92);
                                }
                        });
                })();
    </script>
        <!-- Cropper Modal -->
        <div class="modal fade" id="cropperModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">Crop Profile Picture</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <img id="cropperImage" src="#" alt="Crop" style="max-width:100%; display:block;">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="(function(){ const m=document.getElementById('cropperModal'); bootstrap.Modal.getInstance(m)?.hide(); })();">Done</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Cropper.js CDN -->
        <link  href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
    @endpush
</x-app-layout>
