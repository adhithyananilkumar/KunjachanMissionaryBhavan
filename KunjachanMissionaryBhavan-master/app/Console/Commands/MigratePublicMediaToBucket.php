<?php

namespace App\Console\Commands;

use App\Models\Blog;
use App\Models\GalleryImage;
use App\Support\StoragePath;
use Illuminate\Console\Command;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class MigratePublicMediaToBucket extends Command
{
    protected $signature = 'media:migrate-to-bucket
        {--dry-run : Show what would change without uploading/updating}
        {--limit=0 : Limit number of records processed per type (0 = no limit)}
        {--only=* : Restrict to specific types (blogs,gallery)}';

    protected $description = 'Move legacy blog/gallery images from local public storage into the default (bucket) filesystem disk and update DB paths.';

    public function handle(): int
    {
        $diskName = (string) config('filesystems.default');
        if ($diskName !== 's3') {
            $this->warn("Default filesystem disk is '{$diskName}'. This command is intended for bucket migrations (s3).");
            $this->line('Set FILESYSTEM_DISK=s3 and configure AWS_* env vars, then re-run.');
            return self::SUCCESS;
        }

        $dryRun = (bool) $this->option('dry-run');
        $limit = (int) $this->option('limit');
        $only = $this->option('only');
        $only = is_array($only) ? array_values(array_filter(array_map('strtolower', $only))) : [];

        $doBlogs = empty($only) || in_array('blogs', $only, true) || in_array('blog', $only, true);
        $doGallery = empty($only) || in_array('gallery', $only, true);

        if (!$doBlogs && !$doGallery) {
            $this->error('Nothing to do. Valid values for --only are: blogs, gallery');
            return self::FAILURE;
        }

        $this->line('Bucket media migration');
        $this->line('- Disk: s3');
        $this->line('- Base prefix: '.StoragePath::basePrefix());
        $this->line('- Dry run: '.($dryRun ? 'yes' : 'no'));

        if ($doBlogs) {
            $this->migrateBlogImages($dryRun, $limit);
        }

        if ($doGallery) {
            $this->migrateGalleryImages($dryRun, $limit);
        }

        $this->info('Done.');
        return self::SUCCESS;
    }

    private function ensureDir(string $dir): void
    {
        try {
            Storage::makeDirectory($dir);
        } catch (\Throwable $e) {
            // ignore
        }
    }

    private function migrateBlogImages(bool $dryRun, int $limit): void
    {
        $this->newLine();
        $this->info('Migrating blog featured images...');

        $dir = StoragePath::blogFeaturedImageDir();
        $this->ensureDir($dir);

        $query = Blog::query()->whereNotNull('featured_image')->where('featured_image', '!=', '');
        if ($limit > 0) {
            $query->limit($limit);
        }

        $count = 0;
        $skipped = 0;
        $missing = 0;

        foreach ($query->cursor() as $blog) {
            $path = (string) $blog->featured_image;

            // Skip if already stored under the bucket prefix
            if (str_starts_with($path, StoragePath::basePrefix().'/')) {
                $skipped++;
                continue;
            }

            // Legacy public-disk path: storage/app/public/<path>
            $src = storage_path('app/public/'.ltrim($path, '/'));
            if (!is_file($src)) {
                $missing++;
                $this->warn("[blog {$blog->id}] missing local file: {$src}");
                continue;
            }

            $destName = StoragePath::uniqueName(new UploadedFile($src, basename($src), null, UPLOAD_ERR_OK, true));

            if ($dryRun) {
                $this->line("[blog {$blog->id}] would upload '{$src}' => '{$dir}/{$destName}' and update DB");
                $count++;
                continue;
            }

            $stored = Storage::putFileAs($dir, new File($src), $destName);
            $blog->featured_image = $stored;
            $blog->save();

            $count++;
        }

        $this->line("Blogs migrated: {$count}, skipped: {$skipped}, missing: {$missing}");
    }

    private function migrateGalleryImages(bool $dryRun, int $limit): void
    {
        $this->newLine();
        $this->info('Migrating gallery images...');

        $dir = StoragePath::galleryImageDir();
        $this->ensureDir($dir);

        $query = GalleryImage::query()->whereNotNull('image_path')->where('image_path', '!=', '');
        if ($limit > 0) {
            $query->limit($limit);
        }

        $count = 0;
        $skipped = 0;
        $missing = 0;

        foreach ($query->cursor() as $image) {
            $path = (string) $image->image_path;

            // Skip if already stored under the bucket prefix
            if (str_starts_with($path, StoragePath::basePrefix().'/')) {
                $skipped++;
                continue;
            }

            // Legacy: public/assets/gallery/<filename>
            if (str_contains($path, '/')) {
                // Some older entries might have other prefixes; treat as already migrated
                $skipped++;
                continue;
            }

            $src = public_path('assets/gallery/'.ltrim($path, '/'));
            if (!is_file($src)) {
                $missing++;
                $this->warn("[gallery {$image->id}] missing local file: {$src}");
                continue;
            }

            $destName = StoragePath::uniqueName(new UploadedFile($src, basename($src), null, UPLOAD_ERR_OK, true));

            if ($dryRun) {
                $this->line("[gallery {$image->id}] would upload '{$src}' => '{$dir}/{$destName}' and update DB");
                $count++;
                continue;
            }

            $stored = Storage::putFileAs($dir, new File($src), $destName);
            $image->image_path = $stored;
            $image->save();

            $count++;
        }

        $this->line("Gallery migrated: {$count}, skipped: {$skipped}, missing: {$missing}");
    }
}
