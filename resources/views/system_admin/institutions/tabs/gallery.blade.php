<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="card-title mb-0">Gallery Management</h5>
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadImageModal">
        <i class="bi bi-upload me-1"></i> Upload Image
    </button>
</div>

@if($images->count() > 0)
    <div class="row g-3">
        @foreach($images as $image)
            <div class="col-6 col-sm-4 col-md-3">
                <div class="card h-100 border-0 shadow-sm overflow-hidden group">
                    <div class="position-relative">
                        <img src="{{ $image->image_url }}" class="card-img-top object-fit-cover" style="height: 140px;">
                        <div class="position-absolute top-0 end-0 p-2">
                            <form action="{{ route('system_admin.gallery.destroy', $image) }}" method="POST" onsubmit="return confirm('Delete this image?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm rounded-circle p-1 shadow-sm" style="width:24px;height:24px;line-height:1;">
                                    <i class="bi bi-x small"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @if($image->caption)
                        <div class="card-body p-2">
                            <p class="small text-muted mb-0 text-truncate" title="{{ $image->caption }}">{{ $image->caption }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-images text-muted fs-1"></i>
        <p class="text-muted mt-2">No images in the gallery yet.</p>
    </div>
@endif

<!-- Upload Modal (System Admin Version) -->
<div class="modal fade" id="uploadImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('system_admin.gallery.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="institution_id" value="{{ $institution->id }}">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title h6">Upload Image for {{ $institution->name }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-toggle="modal" data-bs-target="#uploadImageModal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Select Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                        <div class="form-text small">Recommended size: 1200x800px. Max 10MB.</div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold">Caption (Optional)</label>
                        <input type="text" name="caption" class="form-control" placeholder="Enter a brief description">
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-light text-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Upload Now</button>
                </div>
            </form>
        </div>
    </div>
</div>
