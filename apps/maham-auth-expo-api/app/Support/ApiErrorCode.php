<?php

namespace App\Support;

class ApiErrorCode
{ 
    // ================== AUTHENTICATION ERRORS ==================
    public const INVALID_LOGIN_CREDENTIALS = 'invalid_login_credentials';
    public const USER_BLOCKED = 'user_blocked';
    public const USER_DELETED = 'user_deleted';
    public const USER_DISABLED = 'user_disabled';
    public const USER_SUSPENDED = 'user_suspended';
    public const USER_PENDING = 'user_pending';
    public const USER_NOT_VERIFIED = 'user_not_verified';
    public const ACCESS_DENIED = 'access_denied';
    public const AUTHENTICATION_REQUIRED = 'authentication_required';
    public const INVALID_PASSWORD = 'invalid_password';
    public const PASSWORD_MISMATCH = 'password_mismatch';
    public const ACCOUNT_LOCKED = 'account_locked';
    public const TOO_MANY_LOGIN_ATTEMPTS = 'too_many_login_attempts';
    public const LOGOUT_FAILED = 'logout_failed';

    // ================== TOKEN ERRORS ==================
    public const TOKEN_EXPIRED = 'token_expired';
    public const TOKEN_INVALID = 'token_invalid';
    public const TOKEN_BLACKLISTED = 'token_blacklisted';
    public const TOKEN_NOT_PROVIDED = 'token_not_provided';
    public const TOKEN_MALFORMED = 'token_malformed';
    public const TOKEN_SIGNATURE_INVALID = 'token_signature_invalid';
    public const TOKEN_REFRESH_FAILED = 'token_refresh_failed';
    public const TOKEN_GENERATION_FAILED = 'token_generation_failed';

    // ================== VALIDATION ERRORS ==================
    public const VALIDATION_FAILED = 'validation_failed';
    public const INVALID_INPUT = 'invalid_input';
    public const MISSING_REQUIRED_FIELD = 'missing_required_field';
    public const INVALID_FORMAT = 'invalid_format';
    public const INVALID_EMAIL_FORMAT = 'invalid_email_format';
    public const INVALID_PHONE_FORMAT = 'invalid_phone_format';
    public const INVALID_DATE_FORMAT = 'invalid_date_format';
    public const INVALID_UUID_FORMAT = 'invalid_uuid_format';
    public const VALUE_TOO_SHORT = 'value_too_short';
    public const VALUE_TOO_LONG = 'value_too_long';
    public const VALUE_OUT_OF_RANGE = 'value_out_of_range';
    public const INVALID_FILE_TYPE = 'invalid_file_type';
    public const FILE_TOO_LARGE = 'file_too_large';

    // ================== RESOURCE ERRORS ==================
    public const RESOURCE_NOT_FOUND = 'resource_not_found';
    public const RESOURCE_ALREADY_EXISTS = 'resource_already_exists';
    public const RESOURCE_CREATION_FAILED = 'resource_creation_failed';
    public const RESOURCE_UPDATE_FAILED = 'resource_update_failed';
    public const RESOURCE_DELETION_FAILED = 'resource_deletion_failed';
    public const RESOURCE_LOCKED = 'resource_locked';
    public const RESOURCE_ARCHIVED = 'resource_archived';

    // ================== USER ERRORS ==================
    public const USER_NOT_FOUND = 'user_not_found';
    public const USER_ALREADY_EXISTS = 'user_already_exists';
    public const USER_CREATION_FAILED = 'user_creation_failed';
    public const USER_UPDATE_FAILED = 'user_update_failed';
    public const USER_DELETION_FAILED = 'user_deletion_failed';
    public const USER_EMAIL_ALREADY_EXISTS = 'user_email_already_exists';
    public const USER_PHONE_ALREADY_EXISTS = 'user_phone_already_exists';
    public const USER_CANNOT_DELETE_SELF = 'user_cannot_delete_self';
    public const USER_CANNOT_MODIFY_SUPER_ADMIN = 'user_cannot_modify_super_admin';
    public const USER_STATUS_CHANGE_FAILED = 'user_status_change_failed';

    // ================== ROLE ERRORS ==================
    public const ROLE_NOT_FOUND = 'role_not_found';
    public const ROLE_ALREADY_EXISTS = 'role_already_exists';
    public const ROLE_CREATION_FAILED = 'role_creation_failed';
    public const ROLE_UPDATE_FAILED = 'role_update_failed';
    public const ROLE_DELETION_FAILED = 'role_deletion_failed';
    public const ROLE_SYSTEM_MODIFICATION_FORBIDDEN = 'role_system_modification_forbidden';
    public const ROLE_SYSTEM_DELETION_FORBIDDEN = 'role_system_deletion_forbidden';
    public const ROLE_HAS_USERS = 'role_has_users';
    public const ROLE_SUPER_ADMIN_PROTECTED = 'role_super_admin_protected';
    public const ROLE_PERMISSION_INVALID = 'role_permission_invalid';
    public const ROLE_ASSIGNMENT_FAILED = 'role_assignment_failed';
    public const ROLE_REMOVAL_FAILED = 'role_removal_failed';
    public const ROLE_LEVEL_INSUFFICIENT = 'role_level_insufficient';

    // ================== PERMISSION ERRORS ==================
    public const PERMISSION_DENIED = 'permission_denied';
    public const PERMISSION_NOT_FOUND = 'permission_not_found';
    public const PERMISSION_ALREADY_EXISTS = 'permission_already_exists';
    public const PERMISSION_CREATION_FAILED = 'permission_creation_failed';
    public const PERMISSION_UPDATE_FAILED = 'permission_update_failed';
    public const PERMISSION_DELETION_FAILED = 'permission_deletion_failed';
    public const PERMISSION_SYSTEM_MODIFICATION_FORBIDDEN = 'permission_system_modification_forbidden';
    public const PERMISSION_ASSIGNMENT_FAILED = 'permission_assignment_failed';
    public const PERMISSION_REVOCATION_FAILED = 'permission_revocation_failed';
    public const INSUFFICIENT_PERMISSIONS = 'insufficient_permissions';

    // ================== PROFILE ERRORS ==================
    public const PROFILE_NOT_FOUND = 'profile_not_found';
    public const PROFILE_ALREADY_EXISTS = 'profile_already_exists';
    public const PROFILE_CREATION_FAILED = 'profile_creation_failed';
    public const PROFILE_UPDATE_FAILED = 'profile_update_failed';
    public const PROFILE_DELETION_FAILED = 'profile_deletion_failed';
    public const PROFILE_CANNOT_EDIT = 'profile_cannot_edit';
    public const PROFILE_CANNOT_SUBMIT = 'profile_cannot_submit';
    public const PROFILE_CANNOT_RESUBMIT = 'profile_cannot_resubmit';
    public const PROFILE_NOT_PENDING = 'profile_not_pending';
    public const PROFILE_ALREADY_APPROVED = 'profile_already_approved';
    public const PROFILE_ALREADY_REJECTED = 'profile_already_rejected';
    public const PROFILE_VERIFICATION_REQUIRED = 'profile_verification_required';
    public const PROFILE_INCOMPLETE = 'profile_incomplete';
    public const PROFILE_TYPE_INVALID = 'profile_type_invalid';

    // ================== SERVICE ERRORS ==================
    public const SERVICE_NOT_FOUND = 'service_not_found';
    public const SERVICE_ALREADY_EXISTS = 'service_already_exists';
    public const SERVICE_CREATION_FAILED = 'service_creation_failed';
    public const SERVICE_UPDATE_FAILED = 'service_update_failed';
    public const SERVICE_DELETION_FAILED = 'service_deletion_failed';
    public const SERVICE_DISABLED = 'service_disabled';
    public const SERVICE_SUSPENDED = 'service_suspended';
    public const SERVICE_IP_NOT_ALLOWED = 'service_ip_not_allowed';
    public const SERVICE_PERMISSION_DENIED = 'service_permission_denied';
    public const SERVICE_COMMUNICATION_FAILED = 'service_communication_failed';
    public const SERVICE_TIMEOUT = 'service_timeout';

    // ================== RATE LIMIT ERRORS ==================
    public const RATE_LIMIT_EXCEEDED = 'rate_limit_exceeded';
    public const RATE_LIMIT_LOGIN_EXCEEDED = 'rate_limit_login_exceeded';
    public const RATE_LIMIT_API_EXCEEDED = 'rate_limit_api_exceeded';
    public const RATE_LIMIT_SERVICE_EXCEEDED = 'rate_limit_service_exceeded';

    // ================== SERVER ERRORS ==================
    public const INTERNAL_SERVER_ERROR = 'internal_server_error';
    public const SERVICE_UNAVAILABLE = 'service_unavailable';
    public const DATABASE_ERROR = 'database_error';
    public const CACHE_ERROR = 'cache_error';
    public const QUEUE_ERROR = 'queue_error';
    public const EXTERNAL_SERVICE_ERROR = 'external_service_error';
    public const MAINTENANCE_MODE = 'maintenance_mode';

    // ================== CIRCUIT BREAKER ERRORS ==================
    public const CIRCUIT_BREAKER_OPEN = 'circuit_breaker_open';
    public const CIRCUIT_BREAKER_HALF_OPEN = 'circuit_breaker_half_open';
    public const FALLBACK_EXECUTED = 'fallback_executed';

    // ================== FILE ERRORS ==================
    public const FILE_NOT_FOUND = 'file_not_found';
    public const FILE_UPLOAD_FAILED = 'file_upload_failed';
    public const FILE_DELETE_FAILED = 'file_delete_failed';
    public const FILE_TYPE_NOT_ALLOWED = 'file_type_not_allowed';
    public const FILE_SIZE_EXCEEDED = 'file_size_exceeded';
    public const FILE_CORRUPTED = 'file_corrupted';
    public const FILE_PROCESSING_FAILED = 'file_processing_failed';

    // ================== OTP/VERIFICATION ERRORS ==================
    public const OTP_INVALID = 'otp_invalid';
    public const OTP_EXPIRED = 'otp_expired';
    public const OTP_MAX_ATTEMPTS_EXCEEDED = 'otp_max_attempts_exceeded';
    public const OTP_SEND_FAILED = 'otp_send_failed';
    public const OTP_ALREADY_SENT = 'otp_already_sent';
    public const OTP_COOLDOWN_ACTIVE = 'otp_cooldown_active';
    public const OTP_NOT_REQUESTED = 'otp_not_requested';
    public const USER_TYPE_MISMATCH = 'user_type_mismatch';
    public const ACCOUNT_INACTIVE = 'account_inactive';
    public const INVALID_REGISTRATION_TOKEN = 'invalid_registration_token';
    public const PHONE_ALREADY_REGISTERED = 'phone_already_registered';
    public const EMAIL_VERIFICATION_REQUIRED = 'email_verification_required';
    public const PHONE_VERIFICATION_REQUIRED = 'phone_verification_required';
    public const EMAIL_ALREADY_VERIFIED = 'email_already_verified';
    public const PHONE_ALREADY_VERIFIED = 'phone_already_verified';

    // ================== PASSWORD RESET ERRORS ==================
    public const PASSWORD_RESET_TOKEN_INVALID = 'password_reset_token_invalid';
    public const PASSWORD_RESET_TOKEN_EXPIRED = 'password_reset_token_expired';
    public const PASSWORD_RESET_FAILED = 'password_reset_failed';
    public const PASSWORD_SAME_AS_OLD = 'password_same_as_old';
    public const PASSWORD_TOO_WEAK = 'password_too_weak';

    // ================== BUSINESS LOGIC ERRORS ==================
    public const OPERATION_NOT_ALLOWED = 'operation_not_allowed';
    public const INVALID_STATE_TRANSITION = 'invalid_state_transition';
    public const DUPLICATE_ACTION = 'duplicate_action';
    public const ACTION_ALREADY_PERFORMED = 'action_already_performed';
    public const DEPENDENCY_EXISTS = 'dependency_exists';
    public const PREREQUISITE_NOT_MET = 'prerequisite_not_met';

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
            self::INVALID_FORMAT,
            self::INVALID_EMAIL_FORMAT,
            self::INVALID_PHONE_FORMAT,
            self::INVALID_DATE_FORMAT,
            self::INVALID_UUID_FORMAT,
            self::VALUE_TOO_SHORT,
            self::VALUE_TOO_LONG,
            self::VALUE_OUT_OF_RANGE,
            self::INVALID_FILE_TYPE,
            self::FILE_TOO_LARGE,
            self::PASSWORD_MISMATCH,
            self::PASSWORD_SAME_AS_OLD,
            self::PASSWORD_TOO_WEAK,
            self::PROFILE_TYPE_INVALID,
            self::OPERATION_NOT_ALLOWED,
            self::INVALID_STATE_TRANSITION,
            self::DUPLICATE_ACTION,
            self::INVALID_REGISTRATION_TOKEN => 400,

            // 401 Unauthorized
            self::INVALID_LOGIN_CREDENTIALS,
            self::AUTHENTICATION_REQUIRED,
            self::TOKEN_EXPIRED,
            self::TOKEN_INVALID,
            self::TOKEN_BLACKLISTED,
            self::TOKEN_NOT_PROVIDED,
            self::TOKEN_MALFORMED,
            self::TOKEN_SIGNATURE_INVALID,
            self::INVALID_PASSWORD,
            self::OTP_INVALID,
            self::OTP_EXPIRED,
            self::PASSWORD_RESET_TOKEN_INVALID,
            self::PASSWORD_RESET_TOKEN_EXPIRED,
            self::OTP_NOT_REQUESTED => 401,

            // 403 Forbidden
            self::USER_BLOCKED,
            self::USER_DISABLED,
            self::USER_SUSPENDED,
            self::USER_NOT_VERIFIED,
            self::ACCESS_DENIED,
            self::PERMISSION_DENIED,
            self::INSUFFICIENT_PERMISSIONS,
            self::ACCOUNT_LOCKED,
            self::USER_TYPE_MISMATCH,
            self::ACCOUNT_INACTIVE,
            self::ROLE_SYSTEM_MODIFICATION_FORBIDDEN,
            self::ROLE_SYSTEM_DELETION_FORBIDDEN,
            self::ROLE_SUPER_ADMIN_PROTECTED,
            self::ROLE_LEVEL_INSUFFICIENT,
            self::PERMISSION_SYSTEM_MODIFICATION_FORBIDDEN,
            self::PROFILE_CANNOT_EDIT,
            self::PROFILE_CANNOT_SUBMIT,
            self::PROFILE_CANNOT_RESUBMIT,
            self::PROFILE_VERIFICATION_REQUIRED,
            self::SERVICE_IP_NOT_ALLOWED,
            self::SERVICE_PERMISSION_DENIED,
            self::SERVICE_DISABLED,
            self::SERVICE_SUSPENDED,
            self::USER_CANNOT_DELETE_SELF,
            self::USER_CANNOT_MODIFY_SUPER_ADMIN,
            self::EMAIL_VERIFICATION_REQUIRED,
            self::PHONE_VERIFICATION_REQUIRED => 403,

            // 404 Not Found
            self::RESOURCE_NOT_FOUND,
            self::USER_NOT_FOUND,
            self::USER_DELETED,
            self::ROLE_NOT_FOUND,
            self::PERMISSION_NOT_FOUND,
            self::PROFILE_NOT_FOUND,
            self::SERVICE_NOT_FOUND,
            self::FILE_NOT_FOUND => 404,

            // 409 Conflict
            self::RESOURCE_ALREADY_EXISTS,
            self::USER_ALREADY_EXISTS,
            self::USER_EMAIL_ALREADY_EXISTS,
            self::USER_PHONE_ALREADY_EXISTS,
            self::ROLE_ALREADY_EXISTS,
            self::PERMISSION_ALREADY_EXISTS,
            self::PROFILE_ALREADY_EXISTS,
            self::SERVICE_ALREADY_EXISTS,
            self::ROLE_HAS_USERS,
            self::DEPENDENCY_EXISTS,
            self::PROFILE_ALREADY_APPROVED,
            self::PROFILE_ALREADY_REJECTED,
            self::EMAIL_ALREADY_VERIFIED,
            self::PHONE_ALREADY_VERIFIED,
            self::ACTION_ALREADY_PERFORMED,
            self::PHONE_ALREADY_REGISTERED => 409,

            // 422 Unprocessable Entity
            self::RESOURCE_CREATION_FAILED,
            self::RESOURCE_UPDATE_FAILED,
            self::RESOURCE_DELETION_FAILED,
            self::USER_CREATION_FAILED,
            self::USER_UPDATE_FAILED,
            self::USER_DELETION_FAILED,
            self::USER_STATUS_CHANGE_FAILED,
            self::ROLE_CREATION_FAILED,
            self::ROLE_UPDATE_FAILED,
            self::ROLE_DELETION_FAILED,
            self::ROLE_PERMISSION_INVALID,
            self::ROLE_ASSIGNMENT_FAILED,
            self::ROLE_REMOVAL_FAILED,
            self::PERMISSION_CREATION_FAILED,
            self::PERMISSION_UPDATE_FAILED,
            self::PERMISSION_DELETION_FAILED,
            self::PERMISSION_ASSIGNMENT_FAILED,
            self::PERMISSION_REVOCATION_FAILED,
            self::PROFILE_CREATION_FAILED,
            self::PROFILE_UPDATE_FAILED,
            self::PROFILE_DELETION_FAILED,
            self::PROFILE_NOT_PENDING,
            self::PROFILE_INCOMPLETE,
            self::SERVICE_CREATION_FAILED,
            self::SERVICE_UPDATE_FAILED,
            self::SERVICE_DELETION_FAILED,
            self::FILE_UPLOAD_FAILED,
            self::FILE_DELETE_FAILED,
            self::FILE_PROCESSING_FAILED,
            self::TOKEN_REFRESH_FAILED,
            self::TOKEN_GENERATION_FAILED,
            self::LOGOUT_FAILED,
            self::OTP_SEND_FAILED,
            self::PASSWORD_RESET_FAILED,
            self::PREREQUISITE_NOT_MET => 422,

            // 423 Locked
            self::RESOURCE_LOCKED,
            self::RESOURCE_ARCHIVED => 423,

            // 429 Too Many Requests
            self::RATE_LIMIT_EXCEEDED,
            self::RATE_LIMIT_LOGIN_EXCEEDED,
            self::RATE_LIMIT_API_EXCEEDED,
            self::RATE_LIMIT_SERVICE_EXCEEDED,
            self::TOO_MANY_LOGIN_ATTEMPTS,
            self::OTP_MAX_ATTEMPTS_EXCEEDED,
            self::OTP_ALREADY_SENT,
            self::OTP_COOLDOWN_ACTIVE => 429,

            // 500 Internal Server Error
            self::INTERNAL_SERVER_ERROR,
            self::DATABASE_ERROR,
            self::CACHE_ERROR,
            self::QUEUE_ERROR,
            self::FILE_CORRUPTED => 500,

            // 502 Bad Gateway
            self::EXTERNAL_SERVICE_ERROR,
            self::SERVICE_COMMUNICATION_FAILED => 502,

            // 503 Service Unavailable
            self::SERVICE_UNAVAILABLE,
            self::MAINTENANCE_MODE,
            self::CIRCUIT_BREAKER_OPEN,
            self::CIRCUIT_BREAKER_HALF_OPEN => 503,

            // 504 Gateway Timeout
            self::SERVICE_TIMEOUT => 504,

            // Default
            default => 500,
        };
    }

    /**
     * Get all error codes grouped by category
     */
    public static function getAllGrouped(): array
    {
        return [
            'authentication' => [
                self::INVALID_LOGIN_CREDENTIALS,
                self::USER_BLOCKED,
                self::USER_DELETED,
                self::USER_DISABLED,
                self::USER_SUSPENDED,
                self::USER_PENDING,
                self::USER_NOT_VERIFIED,
                self::ACCESS_DENIED,
                self::AUTHENTICATION_REQUIRED,
                self::INVALID_PASSWORD,
                self::PASSWORD_MISMATCH,
                self::ACCOUNT_LOCKED,
                self::TOO_MANY_LOGIN_ATTEMPTS,
                self::LOGOUT_FAILED,
            ],
            'token' => [
                self::TOKEN_EXPIRED,
                self::TOKEN_INVALID,
                self::TOKEN_BLACKLISTED,
                self::TOKEN_NOT_PROVIDED,
                self::TOKEN_MALFORMED,
                self::TOKEN_SIGNATURE_INVALID,
                self::TOKEN_REFRESH_FAILED,
                self::TOKEN_GENERATION_FAILED,
            ],
            'validation' => [
                self::VALIDATION_FAILED,
                self::INVALID_INPUT,
                self::MISSING_REQUIRED_FIELD,
                self::INVALID_FORMAT,
                self::INVALID_EMAIL_FORMAT,
                self::INVALID_PHONE_FORMAT,
                self::INVALID_DATE_FORMAT,
                self::INVALID_UUID_FORMAT,
                self::VALUE_TOO_SHORT,
                self::VALUE_TOO_LONG,
                self::VALUE_OUT_OF_RANGE,
                self::INVALID_FILE_TYPE,
                self::FILE_TOO_LARGE,
            ],
            'user' => [
                self::USER_NOT_FOUND,
                self::USER_ALREADY_EXISTS,
                self::USER_CREATION_FAILED,
                self::USER_UPDATE_FAILED,
                self::USER_DELETION_FAILED,
                self::USER_EMAIL_ALREADY_EXISTS,
                self::USER_PHONE_ALREADY_EXISTS,
                self::USER_CANNOT_DELETE_SELF,
                self::USER_CANNOT_MODIFY_SUPER_ADMIN,
                self::USER_STATUS_CHANGE_FAILED,
            ],
            'role' => [
                self::ROLE_NOT_FOUND,
                self::ROLE_ALREADY_EXISTS,
                self::ROLE_CREATION_FAILED,
                self::ROLE_UPDATE_FAILED,
                self::ROLE_DELETION_FAILED,
                self::ROLE_SYSTEM_MODIFICATION_FORBIDDEN,
                self::ROLE_SYSTEM_DELETION_FORBIDDEN,
                self::ROLE_HAS_USERS,
                self::ROLE_SUPER_ADMIN_PROTECTED,
                self::ROLE_PERMISSION_INVALID,
                self::ROLE_ASSIGNMENT_FAILED,
                self::ROLE_REMOVAL_FAILED,
                self::ROLE_LEVEL_INSUFFICIENT,
            ],
            'permission' => [
                self::PERMISSION_DENIED,
                self::PERMISSION_NOT_FOUND,
                self::PERMISSION_ALREADY_EXISTS,
                self::PERMISSION_CREATION_FAILED,
                self::PERMISSION_UPDATE_FAILED,
                self::PERMISSION_DELETION_FAILED,
                self::PERMISSION_SYSTEM_MODIFICATION_FORBIDDEN,
                self::PERMISSION_ASSIGNMENT_FAILED,
                self::PERMISSION_REVOCATION_FAILED,
                self::INSUFFICIENT_PERMISSIONS,
            ],
            'profile' => [
                self::PROFILE_NOT_FOUND,
                self::PROFILE_ALREADY_EXISTS,
                self::PROFILE_CREATION_FAILED,
                self::PROFILE_UPDATE_FAILED,
                self::PROFILE_DELETION_FAILED,
                self::PROFILE_CANNOT_EDIT,
                self::PROFILE_CANNOT_SUBMIT,
                self::PROFILE_CANNOT_RESUBMIT,
                self::PROFILE_NOT_PENDING,
                self::PROFILE_ALREADY_APPROVED,
                self::PROFILE_ALREADY_REJECTED,
                self::PROFILE_VERIFICATION_REQUIRED,
                self::PROFILE_INCOMPLETE,
                self::PROFILE_TYPE_INVALID,
            ],
            'service' => [
                self::SERVICE_NOT_FOUND,
                self::SERVICE_ALREADY_EXISTS,
                self::SERVICE_CREATION_FAILED,
                self::SERVICE_UPDATE_FAILED,
                self::SERVICE_DELETION_FAILED,
                self::SERVICE_DISABLED,
                self::SERVICE_SUSPENDED,
                self::SERVICE_IP_NOT_ALLOWED,
                self::SERVICE_PERMISSION_DENIED,
                self::SERVICE_COMMUNICATION_FAILED,
                self::SERVICE_TIMEOUT,
            ],
            'rate_limit' => [
                self::RATE_LIMIT_EXCEEDED,
                self::RATE_LIMIT_LOGIN_EXCEEDED,
                self::RATE_LIMIT_API_EXCEEDED,
                self::RATE_LIMIT_SERVICE_EXCEEDED,
            ],
            'server' => [
                self::INTERNAL_SERVER_ERROR,
                self::SERVICE_UNAVAILABLE,
                self::DATABASE_ERROR,
                self::CACHE_ERROR,
                self::QUEUE_ERROR,
                self::EXTERNAL_SERVICE_ERROR,
                self::MAINTENANCE_MODE,
            ],
        ];
    }
}