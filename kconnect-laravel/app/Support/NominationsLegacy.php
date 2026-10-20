<?php

namespace App\Support;

use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Small helpers shared by the nominations area (rewards/ and admin/ nomination pages).
 */
class NominationsLegacy
{
    /**
     * The old pages ran their queries with mysqli, which throws on an SQL error (PHP 8.1+).
     * The uncaught exception ended the script with the "Fatal error" text (display_errors is on,
     * so the status stayed 200), after whatever had already been echoed. Reproduce that: stop
     * with the output so far (the error text itself is not reproduced).
     */
    public static function fatal(string $outputSoFar = '', array $headers = []): never
    {
        throw new HttpResponseException(response($outputSoFar, 200, $headers));
    }

    /** Old: header('Content-Type: application/json') then a fatal error */
    public static function fatalJson(): never
    {
        self::fatal('', ['Content-Type' => 'application/json']);
    }

    /**
     * Called from inside a view where the old page died with a fatal error mid-page:
     * stops rendering, keeping what the view had output so far.
     */
    public static function halt(): never
    {
        throw new NominationsHalt((string) ob_get_contents());
    }

    /** Render a view that may call halt(): the response is then the output up to that point. */
    public static function render(string $view, array $data = [])
    {
        try {
            return response(view($view, $data)->render());
        } catch (\Throwable $e) {
            for ($x = $e; $x; $x = $x->getPrevious()) {
                if ($x instanceof NominationsHalt) {
                    return response($x->output);
                }
            }
            throw $e;
        }
    }

    /** Old manage-questions.php getTypeIcon() */
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

/** Thrown by NominationsLegacy::halt() */
class NominationsHalt extends \RuntimeException
{
    public function __construct(public string $output)
    {
        parent::__construct('halt');
    }
}
