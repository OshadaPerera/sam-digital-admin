<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetCspHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (app()->environment('local')) {
            // Relaxed CSP for local development
            $csp = "default-src 'self' 'unsafe-inline' 'unsafe-eval' data: blob: https: http: ws: wss:; ".
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' https: http:; ".
                   "style-src 'self' 'unsafe-inline' https: http:; ".
                   "img-src 'self' data: blob: https: http:; ".
                   "font-src 'self' data: https: http:; ".
                   "connect-src 'self' https: http: ws: wss:; ".
                   "media-src 'self' data: https: http:; ".
                   "frame-src 'self' https: http:;";
        } else {
            // Strict CSP for production
            $csp = "default-src 'self'; ".
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; ".
                   "style-src 'self' 'unsafe-inline' https:; ".
                   "img-src 'self' data: blob: https:; ".
                   "font-src 'self' data: https:; ".
                   "connect-src 'self' https: wss:; ".
                   "media-src 'self' data: https:; ".
                   "frame-src 'self' https:;";
        }

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
