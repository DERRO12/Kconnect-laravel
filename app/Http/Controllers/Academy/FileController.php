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

        // PHP's file type guess says text/plain for .css/.js, which browsers refuse to use
        $types = [
            'css' => 'text/css', 'js' => 'application/javascript', 'svg' => 'image/svg+xml', 'json' => 'application/json',
            'woff' => 'font/woff', 'woff2' => 'font/woff2', 'ttf' => 'font/ttf', 'mp4' => 'video/mp4', 'webm' => 'video/webm',
            'ogg' => 'video/ogg', 'm4v' => 'video/mp4', 'mov' => 'video/quicktime', 'pdf' => 'application/pdf',
        ];
        $headers = ['Cache-Control' => 'public, max-age=604800'];
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (isset($types[$ext])) {
            $headers['Content-Type'] = $types[$ext];
        }

        return response()->file($file, $headers);
    }
}
