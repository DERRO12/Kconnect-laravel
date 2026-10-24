<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * php artisan kconnect:import-academy-files "/home/kiboko/kacademy.kibokogroup.com"
 *
 * Copies the old K-Academy uploads (course thumbnails, lesson videos, attachments, profile photos)
 * from <old>/assets/uploads into public/academy-files/assets/uploads, keeping the same file names
 * so every file name stored in the kiboko_academy database still points to the right file.
 * Copies only (the old folder is not changed). PHP files are skipped.
 */
class ImportAcademyFiles extends Command
{
    protected $signature = 'kconnect:import-academy-files {old : Path of the old K-Academy site folder}';

    protected $description = 'Copy the old K-Academy uploads into public/academy-files (copy only)';

    public function handle(): int
    {
        $old = rtrim($this->argument('old'), '/\\');
        if (! is_file($old.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php')) {
            $this->error("$old does not look like the old K-Academy folder (no config/config.php).");

            return self::FAILURE;
        }
        $src = $old.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'uploads';
        if (! is_dir($src)) {
            $this->warn("Nothing to copy: $src does not exist.");

            return self::SUCCESS;
        }
        $dst = public_path('academy-files'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'uploads');
        $copied = $skipped = 0;
        foreach (File::allFiles($src, true) as $file) {
            $name = $file->getRelativePathname();
            if (preg_match('/\.(php\d?|phtml|phar|htaccess)$/i', $name) || basename($name) === 'error_log') {
                $skipped++;

                continue;
            }
            $target = $dst.DIRECTORY_SEPARATOR.$name;
            File::ensureDirectoryExists(dirname($target));
            if (! is_file($target) || filesize($target) !== $file->getSize() || filemtime($target) < $file->getMTime()) {
                File::copy($file->getPathname(), $target);
                @touch($target, $file->getMTime());
            }
            $copied++;
        }
        $this->info("Copied $copied files into public/academy-files/assets/uploads ($skipped skipped).");

        return self::SUCCESS;
    }
}
