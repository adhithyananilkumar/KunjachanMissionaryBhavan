<x-app-layout>
    <x-slot name="header">
        <h2 class="h5 mb-0">Gallery Management</h2>
    </x-slot>

    <div class="container py-4">
        @if (session('success'))
            <div class="alert alert-success mb-3">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">Upload New Image</h5>
                <form action="{{ route('system_admin.gallery.store') }}" method="POST" enctype="multipart/form-data" class="row g-3 align-items-end">
                    @csrf
                    <div class="col-md-5">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="image" name="image" required accept="image/*">
                    </div>
                    <div class="col-md-5">
                        <label for="title" class="form-label">Title (Optional)</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Image description">
                    </div>
                    <!-- 
                    <div class="col-md-2">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured">
                            <label class="form-check-label" for="is_featured">Featured</label>
                        </div>
                    </div>
                    -->
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Upload</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-4">
            @forelse($images as $image)
                <div class="col-md-3 col-sm-6">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ asset($image->image_path) }}" class="card-img-top" alt="{{ $image->title }}" style="height: 200px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            @if($image->title)
                                <p class="card-text text-truncate">{{ $image->title }}</p>
                            @endif
                            <form action="{{ route('system_admin.gallery.destroy', $image) }}" method="POST" class="mt-auto text-end" onsubmit="return confirm('Are you sure you want to delete this image?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger border-0"><span class="bi bi-trash"></span> Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-secondary text-center">No images in gallery yet.</div>
                </div>
            @endforelse
        </div>
        
        <div class="mt-4">
            {{ $images->links() }}
        </div>
    </div>
</x-app-layout>
