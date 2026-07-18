<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class StorageLinkCopy extends Command
{
    protected $signature   = 'storage:link-copy {--force : Overwrite existing files}';
    protected $description = 'Copy storage/app/public contents into public/storage (for hosts that do not support symlinks)';

    public function handle(): int
    {
        $src  = storage_path('app/public');
        $dest = public_path('storage');

        if (! File::isDirectory($src)) {
            $this->error("Source directory does not exist: {$src}");
            return self::FAILURE;
        }

        // Remove old dest if it's a symlink, keep if it's a real directory
        if (is_link($dest)) {
            unlink($dest);
            $this->line('Removed existing symlink.');
        }

        if (! File::isDirectory($dest)) {
            File::makeDirectory($dest, 0755, true);
        }

        $this->copyRecursive($src, $dest);

        $this->info("✅ Copied storage/app/public → public/storage successfully.");
        $this->line("   Run this command again after uploading new files.");

        return self::SUCCESS;
    }

    private function copyRecursive(string $src, string $dest): void
    {
        foreach (File::allFiles($src) as $file) {
            $relative  = $file->getRelativePathname();           // e.g. treatments/abc.jpg
            $destFile  = $dest . DIRECTORY_SEPARATOR . $relative;
            $destDir   = dirname($destFile);

            if (! File::isDirectory($destDir)) {
                File::makeDirectory($destDir, 0755, true);
            }

            $shouldCopy = $this->option('force')
                || ! file_exists($destFile)
                || filemtime($file->getRealPath()) > filemtime($destFile);

            if ($shouldCopy) {
                File::copy($file->getRealPath(), $destFile);
                $this->line("  copied: {$relative}");
            }
        }
    }
}
