<?php

namespace App\Support;

use Illuminate\Http\Request;

/** Small helpers shared by the admin content pages (App\Http\Controllers\Admin\ContentController). */
class AdminContentHelpers
{
    /** $companies of admin/manage-brand-assets: key => name, from the companies table (HomeData::brandCompanies) */
    public static function brandCompanies(): array
    {
        return array_map(fn ($c) => $c['name'], HomeData::brandCompanies());
    }

    /** Colour of each company group (the admin page's distribution dots and badges) */
    public static function brandCompanyColors(): array
    {
        return array_map(fn ($c) => $c['color'], HomeData::brandCompanies());
    }

    /** detectCompany() from admin/manage-brand-assets: first company whose keyword is in the title/description */
    public static function detectCompany($title, $description = ''): string
    {
        $text = strtolower($title.' '.$description);

        foreach (HomeData::brandCompanies() as $companyKey => $company) {
            if ($companyKey === 'general') {
                continue;
            }
            foreach ($company['keywords'] as $keyword) {
                if (strpos($text, $keyword) !== false) {
                    return $companyKey;
                }
            }
        }

        return 'general';
    }

    /** A text field of the request ('' when missing or sent as an array by a tampered form). */
    public static function str(Request $request, string $key): string
    {
        $value = $request->input($key);

        return is_string($value) || is_numeric($value) ? (string) $value : '';
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
