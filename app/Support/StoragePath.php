<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class StoragePath
{
    public static function basePrefix(): string
    {
        $prefix = env('FILES_BASE_PREFIX', app()->environment());
        return trim($prefix, '/');
    }

    public static function userAvatarDir(int|string $userId): string
    {
        return self::basePrefix()."/users/{$userId}/avatar";
    }

    public static function inmatePhotoDir(int|string $inmateId): string
    {
        return self::basePrefix()."/inmates/{$inmateId}/photos";
    }

    public static function inmateDocDir(int|string $inmateId): string
    {
        return self::basePrefix()."/inmates/{$inmateId}/documents";
    }

    // New: admission-number based directories
    public static function inmatePhotoDirByAdmission(string $admissionNumber): string
    {
        return self::basePrefix()."/inmates/{$admissionNumber}/photos";
    }

    public static function inmateDocDirByAdmission(string $admissionNumber): string
    {
        return self::basePrefix()."/inmates/{$admissionNumber}/documents";
    }

    public static function labReportDir(int|string $labTestId): string
    {
        return self::basePrefix()."/lab-tests/{$labTestId}/reports";
    }

    public static function bugReportScreenshotDir(int|string $bugId): string
    {
        return self::basePrefix()."/bugs/{$bugId}/screenshots";
    }

    public static function bugReportResponseDir(int|string $bugId): string
    {
        return self::basePrefix()."/bugs/{$bugId}/responses";
    }

    public static function ticketScreenshotDir(int|string $ticketId): string
    {
        return self::basePrefix()."/tickets/{$ticketId}/screenshots";
    }

    public static function ticketReplyDir(int|string $ticketId): string
    {
        return self::basePrefix()."/tickets/{$ticketId}/attachments";
    }

    public static function uniqueName(UploadedFile $file, int $keepSlugChars = 40): string
    {
        $orig = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) ?: 'file';
        $slug = Str::slug($orig);
        $slug = Str::limit($slug, $keepSlugChars, '');
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->guessClientExtension() ?: 'bin');
        return Str::ulid()->toBase32().($slug ? "-{$slug}" : '').".{$ext}";
    }
}
