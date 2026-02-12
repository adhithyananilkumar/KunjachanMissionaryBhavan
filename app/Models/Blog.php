<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'featured_image',
        'short_description',
        'content',
        'status',
        'author_id',
        'institution_id',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        $path = (string) ($this->featured_image ?? '');
        if ($path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $diskName = config('filesystems.default');
        $disk = Storage::disk($diskName);

        if ($diskName === 's3') {
            try {
                $cacheKey = 'blog:featured_image_url:'
                    . sha1((string)$this->id.'|'.$path.'|'.(string)optional($this->updated_at)->timestamp.'|'.app()->environment());
                return cache()->remember($cacheKey, now()->addMinutes(30), function () use ($disk, $path) {
                    return $disk->temporaryUrl(
                        $path,
                        now()->addMinutes(90),
                        ['ResponseCacheControl' => 'public, max-age=3600, immutable']
                    );
                });
            } catch (\Throwable $e) {
                // fallback below
            }
        }

        // Local/public: include version param so browsers refresh on updates
        try {
            $url = $disk->url($path);
            $ver = optional($this->updated_at)->timestamp ?: '';
            return $ver ? ($url.(str_contains($url, '?') ? '&' : '?').'v='.$ver) : $url;
        } catch (\Throwable $e) {
            // Legacy fallback used previously in views
            return asset('storage/' . ltrim($path, '/'));
        }
    }
}
