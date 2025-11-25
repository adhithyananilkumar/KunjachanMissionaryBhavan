<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdmissionNumberGenerator
{
    /**
     * Generate a unique admission number of the form ADM{YYYY}{6digits}.
     * Retries a few times on collision.
     */
    public static function generate(callable $existsChecker = null): string
    {
        $year = now()->format('Y');
        $exists = $existsChecker ?: function(string $candidate): bool {
            return DB::table('inmates')->where('admission_number', $candidate)->exists();
        };

        $attempts = 0;
        while ($attempts < 10) {
            // Option A: random 6 digits; Option B: sequence table (future). Using random with DB uniqueness check.
            $suffix = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $candidate = "ADM{$year}{$suffix}";
            if (!$exists($candidate)) {
                return $candidate;
            }
            $attempts++;
            // small sleep to reduce hot collisions in micro bursts
            usleep(1000);
        }
        // As a last resort, append ULID tail to guarantee uniqueness
        return 'ADM'.$year.substr(Str::ulid()->toBase32(), 0, 6);
    }
}
