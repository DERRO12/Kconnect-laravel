<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Laravel checks a CSRF token on every POST. The K-Connect pages post forms and send
 * fetch()/XHR/jQuery requests from many inline scripts, so instead of editing each script
 * this adds the token to every HTML page once: a <meta name="csrf-token"> tag plus a tiny
 * script that attaches it to every same-site POST (forms, fetch, XMLHttpRequest).
 */
class InjectCsrfToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $type = (string) $response->headers->get('Content-Type', '');
        if ($response->isRedirection() || ($type !== '' && ! str_contains($type, 'text/html'))) {
            return $response;
        }
        if (! method_exists($response, 'getContent') || $response instanceof \Symfony\Component\HttpFoundation\StreamedResponse
            || $response instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse) {
            return $response;
        }
        $html = $response->getContent();
        if (! is_string($html) || stripos($html, '<head') === false || ! $request->hasSession()) {
            return $response;
        }

        $snippet = $this->snippet(csrf_token());
        $html = preg_replace('/<head\b[^>]*>/i', '$0'.$snippet, $html, 1);
        $response->setContent($html);

        return $response;
    }

    private function snippet(string $token): string
    {
        $t = htmlspecialchars($token, ENT_QUOTES);

        return '<!--kc-csrf--><meta name="csrf-token" content="'.$t.'"><script>'
            .'(function(){var T=document.querySelector(\'meta[name="csrf-token"]\').content;'
            .'function same(u){try{return new URL(u,location.href).origin===location.origin}catch(e){return true}}'
            .'function add(f){if(f&&(f.method||"").toLowerCase()==="post"&&!f.querySelector(\'input[name="_token"]\')){var i=document.createElement("input");i.type="hidden";i.name="_token";i.value=T;f.appendChild(i)}}'
            .'document.addEventListener("submit",function(e){add(e.target)},true);'
            .'var s=HTMLFormElement.prototype.submit;HTMLFormElement.prototype.submit=function(){add(this);return s.apply(this,arguments)};'
            .'var rs=HTMLFormElement.prototype.requestSubmit;if(rs){HTMLFormElement.prototype.requestSubmit=function(){add(this);return rs.apply(this,arguments)}}'
            .'if(window.fetch){var F=window.fetch;window.fetch=function(i,o){o=o||{};var u=(typeof i==="string"||i instanceof URL)?String(i):(i&&i.url);var m=(o.method||(i&&i.method)||"GET").toUpperCase();'
            .'if(m!=="GET"&&m!=="HEAD"&&same(u)){var h=new Headers(o.headers||(i&&i.headers)||{});if(!h.has("X-CSRF-TOKEN"))h.set("X-CSRF-TOKEN",T);o.headers=h}return F.call(this,i,o)}}'
            .'var O=XMLHttpRequest.prototype.open,S=XMLHttpRequest.prototype.send;XMLHttpRequest.prototype.open=function(m,u){this._kcM=String(m).toUpperCase();this._kcU=u;return O.apply(this,arguments)};'
            .'XMLHttpRequest.prototype.send=function(){if(this._kcM!=="GET"&&this._kcM!=="HEAD"&&same(this._kcU)){try{this.setRequestHeader("X-CSRF-TOKEN",T)}catch(e){}}return S.apply(this,arguments)};'
            .'if(navigator.sendBeacon){var B=navigator.sendBeacon.bind(navigator);navigator.sendBeacon=function(u,d){if(d instanceof FormData&&!d.has("_token"))d.append("_token",T);return B(u,d)}}'
            .'})();</script><!--/kc-csrf-->';
    }
}
