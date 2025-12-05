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
    public function index()
    {
        $blogs = Blog::with('author')->latest()->paginate(10);
        return view('admin.blogs.index', compact('blogs'));
    }

    public function create()
    {
        return view('admin.blogs.create');
    }

    public function store(StoreBlogRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']) . '-' . uniqid();
        $data['author_id'] = auth()->id();
        
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('blogs', 'public');
        }

        if ($data['status'] === 'published') {
            $data['published_at'] = now();
        }

        Blog::create($data);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post created successfully.');
    }

    public function edit(Blog $blog)
    {
        return view('admin.blogs.edit', compact('blog'));
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

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post updated successfully.');
    }

    public function destroy(Blog $blog)
    {
        if ($blog->featured_image) {
            Storage::disk('public')->delete($blog->featured_image);
        }
        $blog->delete();

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post deleted successfully.');
    }
}
