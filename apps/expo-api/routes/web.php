<?php

use Illuminate\Support\Facades\Route;

Route::get('/docs', function () {
    return view('welcome');
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
