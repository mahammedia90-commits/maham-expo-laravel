<?php

namespace App\Exceptions;

use App\Support\ApiErrorCode;
use Exception;

class ApiException extends Exception
{
    protected string $errorCode;
    protected mixed $errors;

    public function __construct(
        string $message,
        string $errorCode = ApiErrorCode::INTERNAL_SERVER_ERROR,
        ?int $httpCode = null,
        mixed $errors = null
    ) {
        $this->errorCode = $errorCode;
        $this->errors = $errors;

        $httpCode = $httpCode ?? ApiErrorCode::getHttpStatus($errorCode);

        parent::__construct($message, $httpCode);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getErrors(): mixed
    {
        return $this->errors;
    }

    /**
     * Static factory methods
     */
    public static function notFound(string $message, string $resource = 'resource'): self
    {
        $errorCode = match($resource) {
            'user' => ApiErrorCode::USER_NOT_FOUND,
            'role' => ApiErrorCode::ROLE_NOT_FOUND,
            'permission' => ApiErrorCode::PERMISSION_NOT_FOUND,
            'profile' => ApiErrorCode::PROFILE_NOT_FOUND,
            'service' => ApiErrorCode::SERVICE_NOT_FOUND,
            default => ApiErrorCode::RESOURCE_NOT_FOUND,
        };

        return new self($message, $errorCode, 404);
    }

    public static function unauthorized(string $message, ?string $errorCode = null): self
    {
        return new self(
            $message,
            $errorCode ?? ApiErrorCode::AUTHENTICATION_REQUIRED,
            401
        );
    }

    public static function forbidden(string $message, ?string $errorCode = null): self
    {
        return new self(
            $message,
            $errorCode ?? ApiErrorCode::PERMISSION_DENIED,
            403
        );
    }

    public static function validation(string $message, mixed $errors = null): self
    {
        return new self(
            $message,
            ApiErrorCode::VALIDATION_FAILED,
            422,
            $errors
        );
    }

    public static function conflict(string $message, ?string $errorCode = null): self
    {
        return new self(
            $message,
            $errorCode ?? ApiErrorCode::RESOURCE_ALREADY_EXISTS,
            409
        );
    }

    public static function unprocessable(string $message, ?string $errorCode = null): self
    {
        return new self(
            $message,
            $errorCode ?? ApiErrorCode::OPERATION_NOT_ALLOWED,
            422
        );
    }

    public static function serverError(string $message): self
    {
        return new self(
            $message,
            ApiErrorCode::INTERNAL_SERVER_ERROR,
            500
        );
    }
}
