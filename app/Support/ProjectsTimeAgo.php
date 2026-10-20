<?php

namespace App\Support;

/** timeAgo() helper that admin/system-logs defined at the bottom of the page. */
class ProjectsTimeAgo
{
    public static function timeAgo($timestamp)
    {
        $time = strtotime($timestamp);
        $diff = time() - $time;

        if ($diff < 60) {
            return $diff.'s ago';
        } elseif ($diff < 3600) {
            return floor($diff / 60).'m ago';
        } elseif ($diff < 86400) {
            return floor($diff / 3600).'h ago';
        } elseif ($diff < 604800) {
            return floor($diff / 86400).'d ago';
        } else {
            return date('M d', $time);
        }
    }
}
