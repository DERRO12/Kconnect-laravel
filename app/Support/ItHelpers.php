<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Helper functions that the old IT pages (it-support, it-admin, it-ticket-detail,
 * admin/it-admin ...) each declared at the top of the file.
 */
class ItHelpers
{
    /** Per-request memo of the schema checks below. */
    private static array $schemaMemo = [];

    /**
     * Schema::hasColumn(), cached (10 minutes) so pages do not query information_schema on
     * every request. Some optional columns (it_tickets.feedback, key_contacts.photo ...) exist
     * on some installations only; callers degrade gracefully when they are absent.
     */
    public static function hasColumn(string $table, string $column): bool
    {
        return self::schemaCheck("col:$table.$column", fn () => Schema::hasColumn($table, $column));
    }

    /** Schema::hasTable(), cached like hasColumn(). */
    public static function hasTable(string $table): bool
    {
        return self::schemaCheck("table:$table", fn () => Schema::hasTable($table));
    }

    private static function schemaCheck(string $key, callable $check): bool
    {
        if (! array_key_exists($key, self::$schemaMemo)) {
            try {
                self::$schemaMemo[$key] = (bool) Cache::remember('kc_schema:'.DB::getDatabaseName().':'.$key, 600, fn () => $check() ? 1 : 0);
            } catch (\Throwable $e) {
                report($e);
                self::$schemaMemo[$key] = false;
            }
        }

        return self::$schemaMemo[$key];
    }

    /**
     * The values of an ENUM column, read from the database (cached), so select lists always
     * match what the column accepts. $fallback is used when the column cannot be read.
     */
    public static function enumValues(string $table, string $column, array $fallback = []): array
    {
        $key = "enum:$table.$column";
        if (! array_key_exists($key, self::$schemaMemo)) {
            try {
                self::$schemaMemo[$key] = Cache::remember('kc_schema:'.DB::getDatabaseName().':'.$key, 600, function () use ($table, $column) {
                    $row = DB::selectOne('SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?', [$table, $column]);
                    if (! $row || ! preg_match('/^enum\((.*)\)$/i', (string) $row['COLUMN_TYPE'], $m)) {
                        return [];
                    }
                    $values = str_getcsv($m[1], ',', "'", "\\");

                    return array_map(fn ($v) => str_replace("''", "'", $v), $values);
                });
            } catch (\Throwable $e) {
                report($e);
                self::$schemaMemo[$key] = [];
            }
        }

        return self::$schemaMemo[$key] ?: $fallback;
    }

    /** Ticket categories accepted by it_tickets.category. */
    public static function ticketCategories(): array
    {
        return self::enumValues('it_tickets', 'category', ['Hardware', 'Software', 'Network', 'ERP', 'Printer', 'SFA', 'Email', 'Other']);
    }

    /** Ticket feedback columns (feedback, feedback_rating) are optional. */
    public static function hasTicketFeedback(): bool
    {
        return self::hasColumn('it_tickets', 'feedback') && self::hasColumn('it_tickets', 'feedback_rating');
    }

    /**
     * Public URL of a backup screenshot. The employee logger stored a bare file name,
     * the admin logger "uploads/backup_screenshots/<file>"; both live in public/uploads/backup_screenshots.
     * Returns a root-relative path ("uploads/backup_screenshots/x.png").
     */
    public static function backupScreenshotUrl($screenshot): string
    {
        $screenshot = ltrim((string) $screenshot, './');
        if ($screenshot === '') {
            return '';
        }
        if (! str_starts_with($screenshot, 'uploads/')) {
            $screenshot = 'uploads/backup_screenshots/'.$screenshot;
        }

        return implode('/', array_map('rawurlencode', explode('/', $screenshot)));
    }

    /** A Y-m-d date from the query string, or $default when it is not one (the value is echoed into pages). */
    public static function validDate($value, string $default = ''): string
    {
        $value = self::str($value);

        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) && strtotime($value) !== false ? $value : $default;
    }

    /** $value when it is one of $allowed, else ''. */
    public static function oneOf($value, array $allowed): string
    {
        $value = self::str($value);

        return in_array($value, $allowed, true) ? $value : '';
    }

    /** A request value as a string ('' for missing values and arrays such as ?search[]=x). */
    public static function str($value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }

    /** getAdminEmailsForNotify($conn) */
    public static function getAdminEmailsForNotify(): array
    {
        $emails = [];
        foreach (DB::select("SELECT email FROM admin_users WHERE status=1 AND email IS NOT NULL AND email!=''") as $row) {
            $emails[] = $row['email'];
        }
        foreach (DB::select("SELECT email FROM employees WHERE role IN ('admin','manager') AND status=1 AND email IS NOT NULL AND email!=''") as $row) {
            $emails[] = $row['email'];
        }

        return array_unique($emails);
    }

    /** getAdminEmployees($conn) */
    public static function getAdminEmployees(): array
    {
        return DB::select("SELECT id, full_name, employee_code, email FROM employees WHERE role IN ('admin','manager','director','hr') AND status=1 ORDER BY full_name");
    }

    /** getAllEmployees($conn, $search) */
    public static function getAllEmployees($search = ''): array
    {
        $query = 'SELECT id, full_name, employee_code, email, department FROM employees WHERE status=1';
        $bindings = [];
        if (! empty($search)) {
            $query .= ' AND (full_name LIKE ? OR employee_code LIKE ? OR email LIKE ?)';
            $like = '%'.$search.'%';
            $bindings = [$like, $like, $like];
        }
        $query .= ' ORDER BY full_name';

        return DB::select($query, $bindings);
    }

    public static function formatTicketTime($datetime)
    {
        if (empty($datetime)) {
            return '';
        }

        return date('M d, Y h:i A', strtotime($datetime));
    }

    public static function isImageFile($filePath)
    {
        if (empty($filePath)) {
            return false;
        }
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']);
    }

    public static function getFileIconHtml($filePath)
    {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $icons = [
            'pdf' => 'fa-file-pdf', 'doc' => 'fa-file-word', 'docx' => 'fa-file-word',
            'xls' => 'fa-file-excel', 'xlsx' => 'fa-file-excel',
            'jpg' => 'fa-file-image', 'jpeg' => 'fa-file-image', 'png' => 'fa-file-image',
            'gif' => 'fa-file-image', 'webp' => 'fa-file-image', 'svg' => 'fa-file-image',
            'bmp' => 'fa-file-image', 'zip' => 'fa-file-archive', 'rar' => 'fa-file-archive',
            'txt' => 'fa-file-alt',
        ];

        return '<i class="fas '.($icons[$ext] ?? 'fa-file').'"></i>';
    }

    public static function formatFileSize($bytes)
    {
        if (! $bytes) {
            return '';
        }
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 1).' '.$units[$i];
    }

    public static function getFileUrl($filePath)
    {
        if (empty($filePath)) {
            return '';
        }

        return ltrim(ltrim($filePath, './'), '/');
    }

    /**
     * fileExistsCheck(): the old code tried the path relative to the site root
     * (the script's folder) and a few parent folders; inside the site that is public/.
     *
     * @param  string  $scriptDir  folder of the old script ('' for root pages, 'admin' for admin/)
     */
    public static function fileExistsCheck($filePath, $scriptDir = '')
    {
        if (empty($filePath)) {
            return false;
        }
        $candidates = [$filePath, './'.$filePath, '../'.$filePath];
        if ($scriptDir !== '') {
            $candidates[] = '../../'.$filePath;
        }
        foreach ($candidates as $p) {
            if (file_exists(kc_public_file($p, $scriptDir))) {
                return true;
            }
        }

        return false;
    }
}
