<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

/**
 * AuthGatewayController
 *
 * Proxies auth requests from the admin frontend to the maham-auth-expo-api service.
 * This allows the frontend to call /auth/* endpoints on the same domain/base URL.
 */
class AuthGatewayController extends Controller
{
    protected Client $httpClient;
    protected string $authServiceUrl;

    public function __construct()
    {
        $this->httpClient = new Client([
            'timeout' => config('app.auth_service_timeout', 5),
        ]);
        $this->authServiceUrl = config('app.auth_service_url', 'http://localhost:8001');
    }

    /**
     * Login endpoint
     * Proxies POST /auth/login to auth service
     * Transforms response to match frontend's expected format
     */
    public function login(Request $request): JsonResponse
    {
        try {
            // Convert 'phone' field to 'identifier' if present
            $loginData = $request->all();
            if (isset($loginData['phone']) && !isset($loginData['identifier'])) {
                $loginData['identifier'] = $loginData['phone'];
                unset($loginData['phone']);
            }

            $response = $this->httpClient->post("{$this->authServiceUrl}/api/v1/auth/login", [
                'json' => $loginData,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            // Transform the response to match frontend's expected format
            if ($data['success'] ?? false) {
                return response()->json([
                    'requires_otp' => false,
                    'user' => $data['data']['user'],
                    'token' => $data['data']['token'] ?? null,
                ], $response->getStatusCode());
            }

            return response()->json([
                'requires_otp' => false,
                'message' => $data['message'] ?? 'Failed to login',
            ], $response->getStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'requires_otp' => false,
                'message' => 'خطأ في المصادقة',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify OTP endpoint
     * Proxies POST /auth/verify-otp to auth service
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        try {
            $response = $this->httpClient->post("{$this->authServiceUrl}/api/v1/auth/verify-otp", [
                'json' => $request->all(),
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            return response()->json($data, $response->getStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطأ في التحقق من رمز OTP',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Current user endpoint
     * Proxies GET /auth/me to auth service
     */
    public function me(Request $request): JsonResponse
    {
        try {
            // Get the token from the request
            $token = $this->extractToken($request);

            $response = $this->httpClient->get("{$this->authServiceUrl}/api/v1/auth/me", [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer {$token}",
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            return response()->json($data, $response->getStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'غير مصرح',
                'error' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Logout endpoint
     * Proxies POST /auth/logout to auth service
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // Get the token from the request
            $token = $this->extractToken($request);

            $response = $this->httpClient->post("{$this->authServiceUrl}/api/v1/auth/logout", [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer {$token}",
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            return response()->json($data, $response->getStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'تم تسجيل الخروج بنجاح',
                'success' => true,
            ], 200);
        }
    }

    /**
     * Extract Bearer token from Authorization header
     */
    protected function extractToken(Request $request): ?string
    {
        $header = $request->header('Authorization');

        if (preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return $matches[1];
        }

        // Also check for token in cookie
        return $request->cookie('token') ?? null;
    }
}
