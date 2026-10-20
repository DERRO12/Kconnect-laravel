<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Helper functions that the old IT pages (it-support.php, it-admin.php, it-ticket-detail.php,
 * admin/it-admin.php ...) each declared at the top of the file.
 */
class ItHelpers
{
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
