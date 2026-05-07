<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="card-title mb-0">Blogs</h5>
    <a href="{{ route('system_admin.blogs.create', ['institution_id' => $institution->id]) }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i> New Blog Post
    </a>
</div>

<div class="mb-3">
    <form class="input-group" onsubmit="event.preventDefault(); load('blogs', '{{ route('system_admin.institutions.tabs.blogs', $institution) }}?q=' + encodeURIComponent(this.q.value));">
        <input type="text" name="q" class="form-control form-control-sm" placeholder="Search blogs..." value="{{ $q }}">
        <button class="btn btn-outline-secondary btn-sm" type="submit"><i class="bi bi-search"></i></button>
    </form>
</div>

@if($blogs->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Post Title</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody class="small">
                @foreach($blogs as $blog)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $blog->title }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">{{ Str::limit($blog->short_description, 60) }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $blog->status === 'published' ? 'bg-success' : 'bg-secondary' }} bg-opacity-10 text-{{ $blog->status === 'published' ? 'success' : 'secondary' }} border border-{{ $blog->status === 'published' ? 'success' : 'secondary' }} rounded-pill">
                                {{ ucfirst($blog->status) }}
                            </span>
                        </td>
                        <td>{{ $blog->created_at->format('M d, Y') }}</td>
                        <td class="text-end">
                            <a href="{{ route('system_admin.blogs.edit', $blog) }}" class="btn btn-icon text-primary p-0"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $blogs->links('pagination::bootstrap-5') }}
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-newspaper text-muted fs-1"></i>
        <p class="text-muted mt-2">No blogs found for this institution.</p>
    </div>
@endif
