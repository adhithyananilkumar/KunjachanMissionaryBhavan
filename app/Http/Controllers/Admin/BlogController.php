<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Http\Requests\Admin\StoreBlogRequest;
use App\Http\Requests\Admin\UpdateBlogRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    private function getRoutePrefix()
    {
        return request()->routeIs('system_admin.*') ? 'system_admin.blogs.' : 'admin.blogs.';
    }

    public function index()
    {
        $query = Blog::with('author');
        
        // If admin role, only show their institution's blogs
        if (auth()->user()->role === 'admin') {
            $query->where('institution_id', auth()->user()->institution_id);
        }
        
        $blogs = $query->latest()->paginate(10);
        $prefix = $this->getRoutePrefix();
        return view('admin.blogs.index', compact('blogs', 'prefix'));
    }

    public function create()
    {
        $prefix = $this->getRoutePrefix();
        return view('admin.blogs.create', compact('prefix'));
    }

    public function store(StoreBlogRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']) . '-' . uniqid();
        $data['author_id'] = auth()->id();
        
        // Assign institution_id for admin users
        if (auth()->user()->role === 'admin') {
            $data['institution_id'] = auth()->user()->institution_id;
        }
        // if system_admin, it's already in validated data if provided
        
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('blogs', 'public');
        }

        if ($data['status'] === 'published') {
            $data['published_at'] = now();
        }

        $blog = Blog::create($data);

        if (request()->routeIs('system_admin.*') && $blog->institution_id) {
            return redirect()->to(route('system_admin.institutions.show', $blog->institution_id) . '#blogs')->with('success', 'Blog post created successfully.');
        }

        return redirect()->route($this->getRoutePrefix() . 'index')->with('success', 'Blog post created successfully.');
    }

    public function edit(Blog $blog)
    {
        $prefix = $this->getRoutePrefix();
        return view('admin.blogs.edit', compact('blog', 'prefix'));
    }

    public function update(UpdateBlogRequest $request, Blog $blog)
    {
        $data = $request->validated();
        
        if ($data['title'] !== $blog->title) {
             $data['slug'] = Str::slug($data['title']) . '-' . uniqid();
        }

        if ($request->hasFile('featured_image')) {
            if ($blog->featured_image) {
                Storage::disk('public')->delete($blog->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('blogs', 'public');
        }

        if ($data['status'] === 'published' && $blog->status !== 'published') {
            $data['published_at'] = now();
        }

        $blog->update($data);

        return redirect()->route($this->getRoutePrefix() . 'index')->with('success', 'Blog post updated successfully.');
    }

    public function destroy(Blog $blog)
    {
        $institutionId = $blog->institution_id;
        if ($blog->featured_image) {
            Storage::disk('public')->delete($blog->featured_image);
        }
        $blog->delete();

        if (request()->routeIs('system_admin.*') && $institutionId) {
            return redirect()->to(route('system_admin.institutions.show', $institutionId) . '#blogs')->with('success', 'Blog post deleted successfully.');
        }

        return redirect()->route($this->getRoutePrefix() . 'index')->with('success', 'Blog post deleted successfully.');
    }
}
