<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED_LOCALES = ['ar', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->header('Accept-Language');

        if ($locale) {
            // Extract primary language (e.g., "ar-SA" -> "ar", "en-US,en;q=0.9" -> "en")
            $locale = strtolower(substr($locale, 0, 2));
        }

        if ($locale && in_array($locale, self::SUPPORTED_LOCALES)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
