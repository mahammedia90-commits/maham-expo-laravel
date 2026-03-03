<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class WebController extends Controller
{
    /**
     * Home page.
     */
    public function home()
    {
        try {
            return view('home');
        } catch (\Exception $e) {
            if (config('app.debug')) {
                return response()->json([
                    'error' => 'View Error',
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ], 500);
            }
            return response()->json([
                'service' => 'Maham Expo API',
                'version' => config('app.version', '1.0.0'),
                'status' => 'running',
                'api_docs' => '/docs',
                'health' => '/api/health',
            ]);
        }
    }

    /**
     * Docs page (cached HTML rendering).
     */
    public function docs()
    {
        $html = Cache::remember('docs-page-v2', 3600, function () {
            return view('welcome')->render();
        });
        return response($html)->header('Content-Type', 'text/html');
    }
}
