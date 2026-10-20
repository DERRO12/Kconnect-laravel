<?php

namespace App\Support;

use Illuminate\Http\Request;

/** Small helpers shared by the admin content pages (App\Http\Controllers\Admin\ContentController). */
class AdminContentHelpers
{
    /** $companies from admin/manage-brand-assets.php */
    public const BRAND_COMPANIES = [
        'kiboko_enterprises' => 'Kiboko Enterprises Ltd',
        'kibocell' => 'Kibocell',
        'kfs' => 'Kiboko Financial Services',
        'kibs' => 'KIBS',
        'general' => 'General Assets',
    ];

    /** $companyKeywords from admin/manage-brand-assets.php */
    public const BRAND_COMPANY_KEYWORDS = [
        'kiboko_enterprises' => ['kiboko enterprise', 'kel', 'kiboko group', 'kiboko enterprises'],
        'kibocell' => ['kibocell', 'kibocel'],
        'kfs' => ['kfs', 'kiboko financial', 'financial services'],
        'kibs' => ['kibs'],
        'general' => [],
    ];

    /** detectCompany() from admin/manage-brand-assets.php */
    public static function detectCompany($title, $description = ''): string
    {
        $text = strtolower($title.' '.$description);

        foreach (self::BRAND_COMPANY_KEYWORDS as $companyKey => $keywords) {
            if ($companyKey === 'general') {
                continue;
            }
            foreach ($keywords as $keyword) {
                if (strpos($text, $keyword) !== false) {
                    return $companyKey;
                }
            }
        }

        return 'general';
    }

    /**
     * move_uploaded_file($tmp, $target): true on success, false on failure
     * (like the old call, it does not create a missing folder).
     */
    public static function move($file, string $target): bool
    {
        $dir = dirname($target);
        if (! is_dir($dir)) {
            return false;
        }
        try {
            $file->move($dir, basename($target));

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Files of a multiple input (name="x[]") with their original indexes kept
     * (entries without a file are null). Null when the field was not sent at all.
     */
    public static function fileList(Request $request, string $key): ?array
    {
        $files = $request->file($key);
        if ($files === null) {
            return null;
        }

        return is_array($files) ? array_values($files) : null;
    }

    /** Read-then-unset of $_SESSION['message'] / $_SESSION['message_type'] (done by the old views). */
    public static function pullFlash(): array
    {
        if (session('message') === null) {
            return [null, null];
        }

        return [session()->pull('message'), session()->pull('message_type')];
    }
}
