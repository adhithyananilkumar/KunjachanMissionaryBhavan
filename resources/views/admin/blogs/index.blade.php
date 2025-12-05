@extends('layouts.app')
@section('title', 'Blog Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Blog Management</h1>
        <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>New Post
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Status</th>
                            <th>Published At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($blogs as $blog)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $blog->title }}</div>
                                <small class="text-muted">{{ Str::limit($blog->short_description, 50) }}</small>
                            </td>
                            <td>{{ $blog->author->name }}</td>
                            <td>
                                @if($blog->status === 'published')
                                    <span class="badge bg-success">Published</span>
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            </td>
                            <td>{{ $blog->published_at ? $blog->published_at->format('M d, Y') : '-' }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.blogs.destroy', $blog) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                No blog posts found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $blogs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
