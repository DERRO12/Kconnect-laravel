<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * php artisan kconnect:import-files "C:\xampp\htdocs\kconnect.kibokogroup.com"
 *
 * Copies the uploaded files and images from the old site folder into public/, keeping the same
 * relative paths so every stored file name in the database still points to the right file.
 * Copies only (the old folder is not changed). PHP files are skipped.
 */
class ImportFiles extends Command
{
    protected $signature = 'kconnect:import-files {old : Path of the old K-Connect site folder}';

    protected $description = 'Copy uploads and images from the old site into public/ (copy only)';

    /** Folders and files the old pages served directly. */
    private array $items = [
        'uploads', 'admin/uploads', 'intranet/uploads', 'rewards/uploads',
        'K-CONNECT.png', 'K CONNECT PNG.png', 'favi.png',
    ];

    public function handle(): int
    {
        $old = rtrim($this->argument('old'), '/\\');
        if (! is_file($old.DIRECTORY_SEPARATOR.'config.php')) {
            $this->error("$old does not look like the old K-Connect folder (no config.php).");

            return self::FAILURE;
        }
        $copied = $skipped = 0;
        foreach ($this->items as $item) {
            $src = $old.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $item);
            $dst = public_path($item);
            if (is_file($src)) {
                $this->copy($src, $dst, $copied, $skipped);
            } elseif (is_dir($src)) {
                foreach (File::allFiles($src, true) as $file) {
                    $this->copy($file->getPathname(), $dst.DIRECTORY_SEPARATOR.$file->getRelativePathname(), $copied, $skipped);
                }
            }
        }
        $this->info("Copied $copied files ($skipped PHP files skipped).");

        return self::SUCCESS;
    }

    private function copy(string $src, string $dst, int &$copied, int &$skipped): void
    {
        if (preg_match('/\.(php\d?|phtml|phar)$/i', $src) || basename($src) === 'error_log') {
            $skipped++;

            return;
        }
        File::ensureDirectoryExists(dirname($dst));
        if (! is_file($dst) || filesize($dst) !== filesize($src) || filemtime($dst) < filemtime($src)) {
            File::copy($src, $dst);
            @touch($dst, filemtime($src));
        }
        $copied++;
    }
}
