<?php

namespace App\Exceptions;

use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return $this->handleApiException($e);
            }
        });
    }

    /**
     * Handle API exceptions
     */
    protected function handleApiException(Throwable $e): JsonResponse
    {
        // Validation Exception
        if ($e instanceof ValidationException) {
            return ApiResponse::validationError(
                errors: $e->errors(),
                message: $e->getMessage()
            );
        }

        // Authentication Exception
        if ($e instanceof AuthenticationException) {
            return ApiResponse::unauthorized(
                message: __('messages.auth.unauthenticated'),
                errorCode: ApiErrorCode::AUTHENTICATION_REQUIRED
            );
        }

        // JWT Exceptions
        if ($e instanceof TokenExpiredException) {
            return ApiResponse::error(
                message: __('messages.auth.token_expired'),
                errorCode: ApiErrorCode::TOKEN_EXPIRED
            );
        }

        if ($e instanceof TokenInvalidException) {
            return ApiResponse::error(
                message: __('messages.auth.token_invalid'),
                errorCode: ApiErrorCode::TOKEN_INVALID
            );
        }

        if ($e instanceof TokenBlacklistedException) {
            return ApiResponse::error(
                message: __('messages.auth.token_blacklisted'),
                errorCode: ApiErrorCode::TOKEN_BLACKLISTED
            );
        }

        if ($e instanceof JWTException) {
            return ApiResponse::error(
                message: __('messages.auth.token_error'),
                errorCode: ApiErrorCode::TOKEN_INVALID
            );
        }

        // Model Not Found
        if ($e instanceof ModelNotFoundException) {
            $model = class_basename($e->getModel());
            $resource = strtolower($model);
            
            return ApiResponse::notFound(
                message: __("messages.{$resource}.not_found", [], __('messages.resource_not_found')),
                resource: $resource
            );
        }

        // Route Not Found
        if ($e instanceof NotFoundHttpException) {
            return ApiResponse::error(
                message: __('messages.route_not_found'),
                errorCode: ApiErrorCode::RESOURCE_NOT_FOUND
            );
        }

        // Method Not Allowed
        if ($e instanceof MethodNotAllowedHttpException) {
            return ApiResponse::error(
                message: __('messages.method_not_allowed'),
                errorCode: ApiErrorCode::OPERATION_NOT_ALLOWED,
                httpCode: 405
            );
        }

        // Too Many Requests
        if ($e instanceof TooManyRequestsHttpException) {
            $retryAfter = $e->getHeaders()['Retry-After'] ?? null;
            return ApiResponse::rateLimitExceeded(
                message: __('messages.rate_limit_exceeded'),
                retryAfter: $retryAfter ? (int) $retryAfter : null
            );
        }

        // HTTP Exception
        if ($e instanceof HttpException) {
            return $this->handleHttpException($e);
        }

        // Custom API Exceptions
        if ($e instanceof ApiException) {
            return ApiResponse::error(
                message: $e->getMessage(),
                errorCode: $e->getErrorCode(),
                httpCode: $e->getCode(),
                errors: $e->getErrors()
            );
        }

        // Default: Server Error
        return ApiResponse::serverError(
            message: config('app.debug') ? $e->getMessage() : __('messages.server_error'),
            exception: $e
        );
    }

    /**
     * Handle HTTP exceptions
     */
    protected function handleHttpException(HttpException $e): JsonResponse
    {
        $statusCode = $e->getStatusCode();
        $message = $e->getMessage();

        $errorCode = match($statusCode) {
            400 => ApiErrorCode::INVALID_INPUT,
            401 => ApiErrorCode::AUTHENTICATION_REQUIRED,
            403 => ApiErrorCode::PERMISSION_DENIED,
            404 => ApiErrorCode::RESOURCE_NOT_FOUND,
            405 => ApiErrorCode::OPERATION_NOT_ALLOWED,
            409 => ApiErrorCode::RESOURCE_ALREADY_EXISTS,
            422 => ApiErrorCode::VALIDATION_FAILED,
            429 => ApiErrorCode::RATE_LIMIT_EXCEEDED,
            500 => ApiErrorCode::INTERNAL_SERVER_ERROR,
            502 => ApiErrorCode::EXTERNAL_SERVICE_ERROR,
            503 => ApiErrorCode::SERVICE_UNAVAILABLE,
            504 => ApiErrorCode::SERVICE_TIMEOUT,
            default => ApiErrorCode::INTERNAL_SERVER_ERROR,
        };

        return ApiResponse::error(
            message: $message ?: $this->getDefaultMessage($statusCode),
            errorCode: $errorCode,
            httpCode: $statusCode
        );
    }

    /**
     * Get default message for HTTP status code
     */
    protected function getDefaultMessage(int $statusCode): string
    {
        return match($statusCode) {
            400 => __('messages.bad_request'),
            401 => __('messages.unauthorized'),
            403 => __('messages.forbidden'),
            404 => __('messages.not_found'),
            405 => __('messages.method_not_allowed'),
            409 => __('messages.conflict'),
            422 => __('messages.unprocessable_entity'),
            429 => __('messages.rate_limit_exceeded'),
            500 => __('messages.server_error'),
            502 => __('messages.bad_gateway'),
            503 => __('messages.service_unavailable'),
            504 => __('messages.gateway_timeout'),
            default => __('messages.unknown_error'),
        };
    }
}