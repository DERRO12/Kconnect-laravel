<?php

namespace App\Support;

/**
 * Small helpers shared by the nominations area (rewards/ and admin/ nomination pages).
 */
class NominationsLegacy
{
    /**
     * A database (or input) error on an admin nomination form: log it, show the message on
     * the page we return to, and redirect there. (The old pages died with a blank page.)
     */
    public static function fail(string $redirectTo, string $message, ?\Throwable $e = null, bool $sharedMessage = false): never
    {
        if ($e) {
            report($e);
        }
        if ($sharedMessage) {
            // all-nominations / manage-quarters show the message/message_type alert
            session(['message' => e($message), 'message_type' => 'danger']);
        } else {
            session()->flash('nomination_error', $message);
        }
        kc_redirect($redirectTo);
    }

    /** Same for the JSON endpoints: {"success": false, "message": ...} with an error status. */
    public static function failJson(string $message, ?\Throwable $e = null, int $status = 500): never
    {
        if ($e) {
            report($e);
        }
        kc_exit(['success' => false, 'message' => $message], $status);
    }

    /** Old manage-questions getTypeIcon() */
    public static function getTypeIcon($type)
    {
        $icons = [
            'text' => 'font',
            'long_text' => 'align-left',
            'number' => 'hashtag',
            'date' => 'calendar',
            'rating' => 'star',
            'radio' => 'dot-circle',
            'checkbox' => 'check-square',
            'attachment' => 'paperclip'
        ];
        return $icons[$type] ?? 'question';
    }

    /** Quarter names used by several pages */
    public const QUARTER_NAMES = [
        1 => 'Q1 (January - March)',
        2 => 'Q2 (April - June)',
        3 => 'Q3 (July - September)',
        4 => 'Q4 (October - December)',
    ];
}
