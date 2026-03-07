<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

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
                'service' => 'Maham Auth API',
                'version' => config('auth-service.service_version', '1.0.0'),
                'status' => 'running',
                'api_docs' => '/docs',
                'health' => '/api/health',
            ]);
        }
    }

    /**
     * Docs page.
     */
    public function docs()
    {
        return view('welcome');
    }

    /**
     * Reset password form.
     */
    public function resetPasswordForm()
    {
        return view('reset-password');
    }
}
