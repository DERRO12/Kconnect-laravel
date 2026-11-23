<?php

namespace App\Http\Controllers;

use App\Support\Surveys;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The public side of satisfaction surveys, used by the form on dashboard.kibokogroup.com:
 *   GET  api/survey?s=<link code>   the survey and its questions
 *   POST api/survey?s=<link code>   send answers (JSON: {"answers": {"<question id>": "value" | ["a", "b"]}})
 * These routes run without sessions or cookies (routes/public.php), and nothing about the
 * person answering is stored.
 */
class SurveyPublicController extends Controller
{
    /**
     * The form lives on another site (dashboard.kibokogroup.com), so the browser needs CORS headers.
     * Any site may read an open survey: no cookies or logins are used, so there is nothing private to expose.
     */
    private function cors(Request $request, Response $response): Response
    {
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');
        $response->headers->set('Access-Control-Max-Age', '86400');
        $response->headers->set('Cache-Control', 'no-store');
        $response->headers->set('Referrer-Policy', 'no-referrer');

        return $response;
    }

    private function json(Request $request, array $data, int $status = 200): Response
    {
        return $this->cors($request, response()->json($data, $status, [], JSON_UNESCAPED_UNICODE));
    }

    private function survey(Request $request): ?object
    {
        $slug = (string) $request->query('s', '');
        if ($slug === '' || strlen($slug) > 40 || ! Surveys::tablesExist()) {
            return null;
        }

        return Surveys::one('SELECT * FROM surveys WHERE slug = ?', [$slug]);
    }

    public function preflight(Request $request): Response
    {
        return $this->cors($request, response('', 204));
    }

    public function show(Request $request): Response
    {
        $survey = $this->survey($request);
        if (! $survey) {
            return $this->json($request, ['ok' => false, 'message' => 'This survey link is not valid. Please check the link you were given.'], 404);
        }
        if ($survey->status !== 'open') {
            return $this->json($request, ['ok' => false, 'title' => $survey->title,
                'message' => $survey->status === 'closed' ? 'This survey is closed. Thank you for your interest.' : 'This survey is not open yet.'], 403);
        }

        return $this->json($request, ['ok' => true, 'survey' => Surveys::publicPayload($survey)]);
    }

    public function submit(Request $request): Response
    {
        $survey = $this->survey($request);
        if (! $survey || $survey->status !== 'open') {
            return $this->json($request, ['ok' => false, 'message' => 'This survey is not accepting answers.'], 403);
        }
        if (strlen((string) $request->getContent()) > 200000) {
            return $this->json($request, ['ok' => false, 'message' => 'The answers are too long.'], 413);
        }
        $data = json_decode((string) $request->getContent(), true);
        if (! is_array($data)) {
            $data = $request->post();   // also accept a normal form post
        }
        if (! empty($data['website'])) {   // hidden field only robots fill in
            return $this->json($request, ['ok' => true]);
        }
        $answers = is_array($data['answers'] ?? null) ? $data['answers'] : [];
        [$ok, $errors] = Surveys::saveResponse($survey, $answers);
        if (! $ok) {
            return $this->json($request, ['ok' => false, 'message' => 'Please check the highlighted questions.', 'errors' => $errors], 422);
        }

        return $this->json($request, ['ok' => true, 'message' => $survey->thank_you ?: 'Thank you! Your answers have been received anonymously.']);
    }

    /** The same form, served from K-Connect too (handy for testing, or if the dashboard site is down). */
    public function form(Request $request): Response
    {
        $html = (string) file_get_contents(resource_path('survey-form/satisfaction.php'));
        $html = str_replace("var KCONNECT_API = 'https://kconnect.kibokogroup.com/api/survey';",
            'var KCONNECT_API = '.json_encode(url('api/survey')).';', $html);

        return response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8', 'Cache-Control' => 'no-store', 'Referrer-Policy' => 'no-referrer']);
    }
}
