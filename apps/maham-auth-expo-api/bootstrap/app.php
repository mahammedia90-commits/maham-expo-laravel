<?php

use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
         api: __DIR__.'/../routes/api.php',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
   
     ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
           
        $middleware->api(prepend: [
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, Request $request) {
            if (!$request->expectsJson() && !$request->is('api/*')) {
                return null;
            }

            // Set locale from Accept-Language header (needed here because
            // exceptions like AuthenticationException may be thrown before
            // the SetLocale middleware runs)
            $locale = $request->header('Accept-Language');
            if ($locale) {
                $locale = strtolower(substr($locale, 0, 2));
                if (in_array($locale, ['ar', 'en'])) {
                    app()->setLocale($locale);
                }
            }

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
                    message: $message ?: __('messages.server_error'),
                    errorCode: $errorCode,
                    httpCode: $statusCode
                );
            }

            // Custom API Exceptions
            if ($e instanceof \App\Exceptions\ApiException) {
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
        });
    })->create();

