<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GalleryImage extends Model
{
    protected $fillable = ['image_path', 'caption', 'institution_id'];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        $path = (string) ($this->image_path ?? '');
        if ($path === '') {
            return null;
        }

        // Legacy storage: public/assets/gallery/<filename>
        if (!str_contains($path, '/')) {
            return asset('assets/gallery/' . ltrim($path, '/'));
        }

        $diskName = config('filesystems.default');
        $disk = Storage::disk($diskName);

        if ($diskName === 's3') {
            try {
                $cacheKey = 'gallery:image_url:'
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

        try {
            $url = $disk->url($path);
            $ver = optional($this->updated_at)->timestamp ?: '';
            return $ver ? ($url.(str_contains($url, '?') ? '&' : '?').'v='.$ver) : $url;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
