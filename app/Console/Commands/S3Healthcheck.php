<?php

namespace App\Console\Commands;

use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class S3Healthcheck extends Command
{
    protected $signature = 's3:health {--keep : Keep the uploaded test file instead of deleting it} {--debug : Enable verbose HTTP debug logs}';
    protected $description = 'Check S3 connectivity: write, read, URL generation, and delete with detailed diagnostics';

    public function handle(): int
    {
        $diskName = config('filesystems.default');
        $this->info("Default disk: {$diskName}");
        if ($diskName !== 's3') {
            $this->warn('Default disk is not s3. Using s3 disk explicitly for this check.');
        }

    $disk = Storage::disk('s3');
    $bucket = config('filesystems.disks.s3.bucket');
    $region = config('filesystems.disks.s3.region');
    $endpoint = config('filesystems.disks.s3.endpoint');
        $this->line("Bucket: {$bucket} | Region: {$region}");

        $key = 'healthchecks/'.now()->format('Ymd_His').'_'.bin2hex(random_bytes(3)).'.txt';
        $content = 'ok:'.now()->toIso8601String();

        try {
            // Low-level AWS checks for clearer diagnostics
            $cfg = [
                'version' => 'latest',
                'region' => $region,
                'credentials' => [
                    'key' => (string) env('AWS_ACCESS_KEY_ID'),
                    'secret' => (string) env('AWS_SECRET_ACCESS_KEY'),
                ],
                // Align HTTP options (TLS verify) with filesystem config to avoid cURL 60 on Windows
                'http' => [
                    'verify' => config('filesystems.disks.s3.http.verify')
                        ?? (env('AWS_CA_BUNDLE') ?: base_path('storage/certs/cacert.pem')),
                    'connect_timeout' => 5,
                    'timeout' => 20,
                ],
            ];
            if (!empty($endpoint)) {
                $cfg['endpoint'] = $endpoint;
                $cfg['use_path_style_endpoint'] = (bool) config('filesystems.disks.s3.use_path_style_endpoint');
            }
            if ($this->option('debug')) {
                $cfg['debug'] = true;
            }
            $client = new S3Client($cfg);

            $this->line('HEAD Bucket...');
            try { $client->headBucket(['Bucket' => $bucket]); $this->line('headBucket() OK'); }
            catch (AwsException $e) { $this->error('headBucket ERROR: '.$e->getAwsErrorCode().' - '.$e->getAwsErrorMessage()); }

            $this->line('List objects (prefix healthchecks/)...');
            try { $client->listObjectsV2(['Bucket'=>$bucket,'Prefix'=>'healthchecks/','MaxKeys'=>1]); $this->line('listObjectsV2() OK'); }
            catch (AwsException $e) { $this->error('listObjectsV2 ERROR: '.$e->getAwsErrorCode().' - '.$e->getAwsErrorMessage()); }

            $this->line("Uploading {$key} ...");
            $put = $disk->put($key, $content);
            $this->line('put() => '.var_export($put, true));

            $exists = $disk->exists($key);
            $this->line('exists() => '.var_export($exists, true));
            if (!$exists) {
                $this->error('File does not exist after upload.');
                return self::FAILURE;
            }

            $read = $disk->get($key);
            $this->line('get() length => '.strlen($read));
            if ($read !== $content) {
                $this->warn('Content mismatch.');
            }

            // URL generation
            try {
                $tmpUrl = $disk->temporaryUrl($key, now()->addMinutes(2));
                $this->line('temporaryUrl() => '.$tmpUrl);
            } catch (\Throwable $e) {
                $this->warn('temporaryUrl error: '.$e->getMessage());
            }

            if (!$this->option('keep')) {
                $deleted = $disk->delete($key);
                $this->line('delete() => '.var_export($deleted, true));
            }

            $this->info('S3 healthcheck PASS');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('S3 healthcheck FAILED: '.$e->getMessage());
            if (method_exists($e, 'getPrevious') && $e->getPrevious()) {
                $prev = $e->getPrevious();
                $this->line('Previous: '.get_class($prev).' - '.$prev->getMessage());
                if ($prev instanceof AwsException) {
                    $this->line('AWS Error Code: '.$prev->getAwsErrorCode());
                    $this->line('AWS Error Message: '.$prev->getAwsErrorMessage());
                }
            }
            $this->line('Tip: Verify AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_BUCKET, AWS_DEFAULT_REGION.');
            return self::FAILURE;
        }
    }
}
