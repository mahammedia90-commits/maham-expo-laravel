<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Success Response
     */
    public static function success(
        mixed $data = null,
        ?string $message = null,
        int $code = 200
    ): JsonResponse {
        $response = [
            'success' => true,
        ];

        if ($message) {
            $response['message'] = $message;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Error Response
     */
    public static function error(
        string $message,
        string $errorCode,
        ?int $httpCode = null,
        mixed $errors = null,
        mixed $debug = null
    ): JsonResponse {
        $httpCode = $httpCode ?? ApiErrorCode::getHttpStatus($errorCode);

        $response = [
            'success' => false,
            'message' => $message,
            'error_code' => $errorCode,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        // Debug info only in development
        if ($debug !== null && config('app.debug')) {
            $response['debug'] = $debug;
        }

        return response()->json($response, $httpCode);
    }

    /**
     * Validation Error Response
     */
    public static function validationError(array $errors, ?string $message = null): JsonResponse
    {
        return self::error(
            message: $message ?? __('messages.validation_failed'),
            errorCode: ApiErrorCode::VALIDATION_FAILED,
            errors: $errors
        );
    }

    /**
     * Not Found Response
     */
    public static function notFound(?string $message = null, ?string $resource = null): JsonResponse
    {
        $errorCode = match($resource) {
            'event' => ApiErrorCode::EVENT_NOT_FOUND,
            'space' => ApiErrorCode::SPACE_NOT_FOUND,
            'visit_request' => ApiErrorCode::VISIT_REQUEST_NOT_FOUND,
            'rental_request' => ApiErrorCode::RENTAL_REQUEST_NOT_FOUND,
            'profile' => ApiErrorCode::PROFILE_NOT_FOUND,
            'category' => ApiErrorCode::CATEGORY_NOT_FOUND,
            'city' => ApiErrorCode::CITY_NOT_FOUND,
            default => ApiErrorCode::RESOURCE_NOT_FOUND,
        };

        return self::error(
            message: $message ?? __('messages.resource_not_found'),
            errorCode: $errorCode
        );
    }

    /**
     * Unauthorized Response
     */
    public static function unauthorized(?string $message = null, ?string $errorCode = null): JsonResponse
    {
        return self::error(
            message: $message ?? __('messages.unauthorized'),
            errorCode: $errorCode ?? ApiErrorCode::AUTHENTICATION_REQUIRED
        );
    }

    /**
     * Forbidden Response
     */
    public static function forbidden(?string $message = null, ?string $errorCode = null): JsonResponse
    {
        return self::error(
            message: $message ?? __('messages.forbidden'),
            errorCode: $errorCode ?? ApiErrorCode::PERMISSION_DENIED
        );
    }

    /**
     * Server Error Response
     */
    public static function serverError(?string $message = null, ?\Throwable $exception = null): JsonResponse
    {
        $debug = null;
        if ($exception && config('app.debug')) {
            $debug = [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ];
        }

        return self::error(
            message: $message ?? __('messages.server_error'),
            errorCode: ApiErrorCode::INTERNAL_SERVER_ERROR,
            debug: $debug
        );
    }

    /**
     * Paginated Response
     */
    public static function paginated($paginator, ?string $message = null): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $paginator->items(),
            'pagination' => [  
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ];

        if ($message) {
            $response['message'] = $message;
        }

        return response()->json($response);
    }

    /**
     * Created Response
     */
    public static function created(mixed $data = null, ?string $message = null): JsonResponse
    {
        return self::success($data, $message, 201);
    }

    /**
     * No Content Response
     */
    public static function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }
}
