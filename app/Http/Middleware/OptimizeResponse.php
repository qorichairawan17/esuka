<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OptimizeResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Use headers->set() instead of header() for compatibility with all response types
        // Add performance headers
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Remove unnecessary headers
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        // Minify HTML in production (only for non-streamed responses)
        if (
            app()->environment('production') &&
            $response->headers->get('Content-Type') === 'text/html; charset=UTF-8' &&
            method_exists($response, 'getContent')
        ) {
            $content = $response->getContent();
            if ($content) {
                // Basic HTML minification
                $content = preg_replace('/\s+/', ' ', $content);
                $content = preg_replace('/>\s+</', '><', $content);
                $response->setContent($content);
            }
        }

        return $response;
    }
}
