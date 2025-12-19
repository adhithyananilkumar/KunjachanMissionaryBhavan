@extends('layouts.app')
@section('title','Gallery Management')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Gallery Management</h1>
            <p class="text-muted small">Manage images displayed in the public gallery.</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="bi bi-cloud-upload me-2"></i>Add Image
        </button>
    </div>

    <div class="row g-4">
        @forelse($images as $image)
        <div class="col-6 col-md-4 col-lg-3 col-xl-2">
            <div class="card h-100 shadow-sm">
                <div class="position-relative" style="padding-top: 75%; overflow: hidden;">
                    <img src="{{ asset('assets/gallery/'.$image->image_path) }}" class="card-img-top position-absolute top-0 start-0 w-100 h-100 object-fit-cover" alt="Gallery Image">
                </div>
                <div class="card-body p-2 d-flex justify-content-between align-items-center">
                    <small class="text-muted text-truncate" title="{{ $image->caption }}">{{ $image->caption ?? 'No caption' }}</small>
                    <form action="{{ route('admin.gallery.destroy', $image->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this image?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger border-0"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5 text-muted">
            <i class="bi bi-images display-4 mb-3 d-block"></i>
            No images in gallery yet.
        </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $images->links() }}
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Select Image</label>
                        <input type="file" class="form-control" id="imageInput" accept="image/*" required>
                        <input type="hidden" name="image" id="finalImage">
                    </div>
                    
                    <div class="img-container mb-3 d-none" style="max-height: 500px;">
                        <img id="imageToCrop" src="" style="max-width: 100%;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Caption (Optional)</label>
                        <input type="text" name="caption" class="form-control" placeholder="Brief description">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="cropAndUpload">Upload</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
    let cropper;
    const imageInput = document.getElementById('imageInput');
    const imageToCrop = document.getElementById('imageToCrop');
    const imgContainer = document.querySelector('.img-container');

    imageInput.addEventListener('change', function (e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            const file = files[0];
            const reader = new FileReader();
            reader.onload = function (e) {
                imageToCrop.src = e.target.result;
                imgContainer.classList.remove('d-none');
                if (cropper) {
                    cropper.destroy();
                }
                cropper = new Cropper(imageToCrop, {
                    aspectRatio: NaN, // Free crop
                    viewMode: 1,
                    autoCropArea: 1,
                });
            };
            reader.readAsDataURL(file);
        }
    });

    document.getElementById('cropAndUpload').addEventListener('click', function () {
        if (!cropper) return;

        // Get cropped canvas
        const canvas = cropper.getCroppedCanvas({
            maxWidth: 1920,
            maxHeight: 1080,
        });

        // Convert to blob and submit
        canvas.toBlob(function (blob) {
            const formData = new FormData(document.getElementById('uploadForm'));
            formData.set('image', blob, 'gallery-image.jpg');

            const btn = document.getElementById('cropAndUpload');
            btn.disabled = true;
            btn.innerText = 'Uploading...';

            fetch('{{ route("admin.gallery.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    return response.json(); 
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Upload failed. Check console.');
                btn.disabled = false;
                btn.innerText = 'Upload';
            });
        }, 'image/jpeg', 0.8);
    });
</script>
@endpush
@endsection
