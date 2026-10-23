<?php

namespace App\Http\Controllers\Academy;

use App\Http\Controllers\Controller;

/**
 * /academy/assets/... when the web server does not rewrite it to public/academy-files/assets/
 * itself (public/.htaccess does on Apache/cPanel). Video seeking (Range requests) is supported.
 */
class FileController extends Controller
{
    public function asset(string $path)
    {
        $root = realpath(public_path('academy-files/assets'));
        $file = $root ? realpath($root.'/'.$path) : false;
        if (! $file || ! str_starts_with($file, $root.DIRECTORY_SEPARATOR) || ! is_file($file)
            || preg_match('/\.(php\d?|phtml|phar)$/i', $file) || str_starts_with(basename($file), '.')) {
            abort(404);
        }

        return response()->file($file, ['Cache-Control' => 'public, max-age=604800']);
    }
}
