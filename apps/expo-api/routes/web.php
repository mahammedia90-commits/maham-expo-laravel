<?php

use Illuminate\Support\Facades\Route;

Route::get('/docs', function () {
    // Cache rendered HTML for 1 hour — the 250KB template is static content
    $html = cache()->remember('docs-page-v1', 3600, function () {
        return view('welcome')->render();
    });
    return response($html)->header('Content-Type', 'text/html');
});

Route::get('/', function () {
    try {
        return view('home');
    } catch (\Exception $e) {
        // Fallback to simple response if view fails
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
});
