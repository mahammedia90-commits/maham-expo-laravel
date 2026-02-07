<?php

namespace App\Support;

class ApiErrorCode
{
    // ================== AUTHENTICATION ERRORS ==================
    public const AUTHENTICATION_REQUIRED = 'authentication_required';
    public const TOKEN_INVALID = 'token_invalid';
    public const TOKEN_EXPIRED = 'token_expired';
    public const PERMISSION_DENIED = 'permission_denied';
    public const ROLE_REQUIRED = 'role_required';
    public const PROFILE_NOT_VERIFIED = 'profile_not_verified';

    // ================== VALIDATION ERRORS ==================
    public const VALIDATION_FAILED = 'validation_failed';
    public const INVALID_INPUT = 'invalid_input';
    public const MISSING_REQUIRED_FIELD = 'missing_required_field';
    public const INVALID_DATE_FORMAT = 'invalid_date_format';
    public const INVALID_FILE_TYPE = 'invalid_file_type';
    public const FILE_TOO_LARGE = 'file_too_large';

    // ================== RESOURCE ERRORS ==================
    public const RESOURCE_NOT_FOUND = 'resource_not_found';
    public const RESOURCE_ALREADY_EXISTS = 'resource_already_exists';
    public const RESOURCE_CREATION_FAILED = 'resource_creation_failed';
    public const RESOURCE_UPDATE_FAILED = 'resource_update_failed';
    public const RESOURCE_DELETION_FAILED = 'resource_deletion_failed';

    // ================== EVENT ERRORS ==================
    public const EVENT_NOT_FOUND = 'event_not_found';
    public const EVENT_NOT_AVAILABLE = 'event_not_available';
    public const EVENT_ENDED = 'event_ended';
    public const EVENT_NOT_PUBLISHED = 'event_not_published';

    // ================== SPACE ERRORS ==================
    public const SPACE_NOT_FOUND = 'space_not_found';
    public const SPACE_NOT_AVAILABLE = 'space_not_available';
    public const SPACE_ALREADY_RENTED = 'space_already_rented';

    // ================== VISIT REQUEST ERRORS ==================
    public const VISIT_REQUEST_NOT_FOUND = 'visit_request_not_found';
    public const VISIT_REQUEST_ALREADY_EXISTS = 'visit_request_already_exists';
    public const VISIT_REQUEST_CANNOT_BE_MODIFIED = 'visit_request_cannot_be_modified';
    public const VISIT_REQUEST_CANNOT_BE_CANCELLED = 'visit_request_cannot_be_cancelled';
    public const VISIT_DATE_IN_PAST = 'visit_date_in_past';
    public const VISIT_DATE_OUTSIDE_EVENT = 'visit_date_outside_event';

    // ================== RENTAL REQUEST ERRORS ==================
    public const RENTAL_REQUEST_NOT_FOUND = 'rental_request_not_found';
    public const RENTAL_REQUEST_ALREADY_EXISTS = 'rental_request_already_exists';
    public const RENTAL_REQUEST_CANNOT_BE_MODIFIED = 'rental_request_cannot_be_modified';
    public const RENTAL_REQUEST_CANNOT_BE_CANCELLED = 'rental_request_cannot_be_cancelled';
    public const RENTAL_DATE_CONFLICT = 'rental_date_conflict';
    public const RENTAL_DATES_OUTSIDE_EVENT = 'rental_dates_outside_event';

    // ================== PROFILE ERRORS ==================
    public const PROFILE_NOT_FOUND = 'profile_not_found';
    public const PROFILE_ALREADY_EXISTS = 'profile_already_exists';
    public const PROFILE_PENDING = 'profile_pending';
    public const PROFILE_REJECTED = 'profile_rejected';
    public const PROFILE_REQUIRED = 'profile_required';
    public const PROFILE_CANNOT_BE_MODIFIED = 'profile_cannot_be_modified';

    // ================== CATEGORY ERRORS ==================
    public const CATEGORY_NOT_FOUND = 'category_not_found';

    // ================== CITY ERRORS ==================
    public const CITY_NOT_FOUND = 'city_not_found';

    // ================== FAVORITE ERRORS ==================
    public const FAVORITE_ALREADY_EXISTS = 'favorite_already_exists';
    public const FAVORITE_NOT_FOUND = 'favorite_not_found';

    // ================== NOTIFICATION ERRORS ==================
    public const NOTIFICATION_NOT_FOUND = 'notification_not_found';

    // ================== FILE ERRORS ==================
    public const FILE_UPLOAD_FAILED = 'file_upload_failed';
    public const FILE_NOT_FOUND = 'file_not_found';

    // ================== SERVICE ERRORS ==================
    public const AUTH_SERVICE_UNAVAILABLE = 'auth_service_unavailable';
    public const INTERNAL_SERVER_ERROR = 'internal_server_error';
    public const SERVICE_UNAVAILABLE = 'service_unavailable';

    // ================== RATE LIMIT ERRORS ==================
    public const RATE_LIMIT_EXCEEDED = 'rate_limit_exceeded';

    // ================== HTTP STATUS MAPPING ==================

    /**
     * Get HTTP status code for error code
     */
    public static function getHttpStatus(string $errorCode): int
    {
        return match($errorCode) {
            // 400 Bad Request
            self::VALIDATION_FAILED,
            self::INVALID_INPUT,
            self::MISSING_REQUIRED_FIELD,
            self::INVALID_DATE_FORMAT,
            self::INVALID_FILE_TYPE,
            self::FILE_TOO_LARGE,
            self::VISIT_DATE_IN_PAST,
            self::VISIT_DATE_OUTSIDE_EVENT,
            self::RENTAL_DATE_CONFLICT,
            self::RENTAL_DATES_OUTSIDE_EVENT => 400,

            // 401 Unauthorized
            self::AUTHENTICATION_REQUIRED,
            self::TOKEN_INVALID,
            self::TOKEN_EXPIRED => 401,

            // 403 Forbidden
            self::PERMISSION_DENIED,
            self::ROLE_REQUIRED,
            self::PROFILE_NOT_VERIFIED,
            self::PROFILE_REQUIRED,
            self::VISIT_REQUEST_CANNOT_BE_MODIFIED,
            self::VISIT_REQUEST_CANNOT_BE_CANCELLED,
            self::RENTAL_REQUEST_CANNOT_BE_MODIFIED,
            self::RENTAL_REQUEST_CANNOT_BE_CANCELLED,
            self::PROFILE_CANNOT_BE_MODIFIED => 403,

            // 404 Not Found
            self::RESOURCE_NOT_FOUND,
            self::EVENT_NOT_FOUND,
            self::SPACE_NOT_FOUND,
            self::VISIT_REQUEST_NOT_FOUND,
            self::RENTAL_REQUEST_NOT_FOUND,
            self::PROFILE_NOT_FOUND,
            self::CATEGORY_NOT_FOUND,
            self::CITY_NOT_FOUND,
            self::FAVORITE_NOT_FOUND,
            self::NOTIFICATION_NOT_FOUND,
            self::FILE_NOT_FOUND => 404,

            // 409 Conflict
            self::RESOURCE_ALREADY_EXISTS,
            self::VISIT_REQUEST_ALREADY_EXISTS,
            self::RENTAL_REQUEST_ALREADY_EXISTS,
            self::PROFILE_ALREADY_EXISTS,
            self::FAVORITE_ALREADY_EXISTS,
            self::SPACE_ALREADY_RENTED => 409,

            // 422 Unprocessable Entity
            self::RESOURCE_CREATION_FAILED,
            self::RESOURCE_UPDATE_FAILED,
            self::RESOURCE_DELETION_FAILED,
            self::EVENT_NOT_AVAILABLE,
            self::EVENT_ENDED,
            self::EVENT_NOT_PUBLISHED,
            self::SPACE_NOT_AVAILABLE,
            self::PROFILE_PENDING,
            self::PROFILE_REJECTED,
            self::FILE_UPLOAD_FAILED => 422,

            // 429 Too Many Requests
            self::RATE_LIMIT_EXCEEDED => 429,

            // 500 Internal Server Error
            self::INTERNAL_SERVER_ERROR => 500,

            // 502 Bad Gateway
            self::AUTH_SERVICE_UNAVAILABLE => 502,

            // 503 Service Unavailable
            self::SERVICE_UNAVAILABLE => 503,

            // Default
            default => 500,
        };
    }
}
