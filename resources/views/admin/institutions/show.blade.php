<x-app-layout>
    <x-slot name="header">
        <h2 class="h5 mb-0">My Institution</h2>
    </x-slot>

    <div class="row g-4">
        <div class="col-md-4">
            <!-- Profile Card -->
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        @if($institution->logo)
                            <img src="{{ asset('storage/'.$institution->logo) }}" class="rounded-circle w-100 h-100 object-fit-cover">
                        @else
                            <i class="bi bi-building fs-1 text-secondary"></i>
                        @endif
                    </div>
                    <h5 class="card-title">{{ $institution->name }}</h5>
                    <p class="text-muted small">{{ $institution->address }}</p>
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $institution->users()->count() }} Staff</span>
                        <span class="badge bg-info-subtle text-info border border-info-subtle">{{ $institution->inmates()->count() }} Inmates</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Tabbed Interface -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="donations-tab" data-bs-toggle="tab" data-bs-target="#donations" type="button" role="tab">
                                <i class="bi bi-currency-rupee me-1"></i> Donations
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="blogs-tab" data-bs-toggle="tab" data-bs-target="#blogs" type="button" role="tab">
                                <i class="bi bi-newspaper me-1"></i> Blogs
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="gallery-tab" data-bs-toggle="tab" data-bs-target="#gallery" type="button" role="tab">
                                <i class="bi bi-images me-1"></i> Gallery
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Donations Tab -->
                        <div class="tab-pane fade show active" id="donations" role="tabpanel">
                            <p class="text-muted small mb-4">Set the donation amounts for different meal types. These values will be displayed on the public donation page.</p>

                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('admin.institutions.donations.update', $institution->id) }}">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Breakfast Amount (₹)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" step="0.01" name="breakfast_amount" class="form-control" value="{{ old('breakfast_amount', $settings->breakfast_amount) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Lunch Amount (₹)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" step="0.01" name="lunch_amount" class="form-control" value="{{ old('lunch_amount', $settings->lunch_amount) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Dinner Amount (₹)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" step="0.01" name="dinner_amount" class="form-control" value="{{ old('dinner_amount', $settings->dinner_amount) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Other/Custom Default (₹)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" step="0.01" name="other_amount" class="form-control" value="{{ old('other_amount', $settings->other_amount) }}" placeholder="Optional">
                                        </div>
                                        <div class="form-text small">Default amount for custom donations.</div>
                                    </div>
                                </div>

                                <div class="mt-4 text-end">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="bi bi-save me-1"></i> Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Blogs Tab -->
                        <div class="tab-pane fade" id="blogs" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <p class="text-muted small mb-0">Manage your institution's blog posts</p>
                                <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-circle me-1"></i> New Blog Post
                                </a>
                            </div>

                            @php
                                $blogs = $institution->blogs()->latest()->take(5)->get();
                            @endphp

                            @if($blogs->count() > 0)
                                <div class="list-group">
                                    @foreach($blogs as $blog)
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $blog->title }}</h6>
                                                    <p class="text-muted small mb-1">{{ Str::limit($blog->short_description, 100) }}</p>
                                                    <small class="text-muted">
                                                        <span class="badge bg-{{ $blog->status === 'published' ? 'success' : 'secondary' }}">{{ ucfirst($blog->status) }}</span>
                                                        • {{ $blog->created_at->format('M d, Y') }}
                                                    </small>
                                                </div>
                                                <div class="ms-3">
                                                    <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-3 text-center">
                                    <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary btn-sm">View All Blogs</a>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-newspaper text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-3">No blog posts yet. Create your first blog post!</p>
                                </div>
                            @endif
                        </div>

                        <!-- Gallery Tab -->
                        <div class="tab-pane fade" id="gallery" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <p class="text-muted small mb-0">Manage your institution's gallery images</p>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadImageModal">
                                    <i class="bi bi-upload me-1"></i> Upload Image
                                </button>
                            </div>

                            @php
                                $galleryImages = $institution->galleryImages()->latest()->take(6)->get();
                            @endphp

                            @if($galleryImages->count() > 0)
                                <div class="row g-3">
                                    @foreach($galleryImages as $image)
                                        <div class="col-md-4">
                                            <div class="card">
                                                <img src="{{ asset('assets/gallery/' . $image->image_path) }}" class="card-img-top" style="height: 150px; object-fit: cover;">
                                                <div class="card-body p-2">
                                                    <p class="small mb-0 text-truncate">{{ $image->caption ?? 'No caption' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-3 text-center">
                                    <a href="{{ route('admin.gallery.index') }}" class="btn btn-outline-secondary btn-sm">View All Images</a>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-images text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-3">No images yet. Upload your first image!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Image Modal -->
    <div class="modal fade" id="uploadImageModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Upload Image</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Caption (Optional)</label>
                            <input type="text" name="caption" class="form-control" placeholder="Enter image caption">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

