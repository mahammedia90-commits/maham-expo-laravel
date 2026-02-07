<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from Accept-Language header or default to 'ar'
        $locale = $request->header('Accept-Language', 'ar');

        // Support both 'ar' and 'en' locales
        if (!in_array($locale, ['ar', 'en'])) {
            // Try to extract language code from full locale (e.g., 'ar-SA' -> 'ar')
            $locale = substr($locale, 0, 2);

            if (!in_array($locale, ['ar', 'en'])) {
                $locale = 'ar'; // Default to Arabic
            }
        }

        app()->setLocale($locale);

        $response = $next($request);

        // Add Content-Language header to response
        $response->headers->set('Content-Language', app()->getLocale());

        return $response;
    }
}
