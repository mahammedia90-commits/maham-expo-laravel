<?php

return [

    // ================== AUTH ==================
    'auth' => [
        'unauthenticated' => 'Unauthenticated',
        'token_expired' => 'Token has expired',
        'token_invalid' => 'Token is invalid',
        'token_blacklisted' => 'Token has been blacklisted',
        'token_error' => 'Token error',
        'account_suspended' => 'Account is suspended',
        'account_blocked' => 'Account is blocked',
        'account_inactive' => 'Account is inactive',

        // Password Reset
        'user_not_found' => 'User not found',
        'reset_link_sent' => 'Password reset link has been sent',
        'invalid_reset_token' => 'Invalid reset token',
        'reset_token_expired' => 'Reset token has expired',
        'password_reset_success' => 'Password has been reset successfully',

        // Change Password
        'current_password_incorrect' => 'Current password is incorrect',
        'password_same_as_old' => 'New password must be different from current password',
        'password_changed' => 'Password has been changed successfully',

        // Profile
        'profile_updated' => 'Profile has been updated successfully',

        // Email Verification
        'email_already_verified' => 'Email is already verified',
        'verification_code_sent' => 'Verification code has been sent',
        'invalid_verification_code' => 'Invalid verification code',
        'email_verified' => 'Email has been verified successfully',
    ],

    // ================== PERMISSIONS ==================
    'permissions' => [
        'denied' => 'You do not have permission to perform this action',
    ],

    // ================== SERVICES ==================
    'services' => [
        'token_required' => 'Service token is required',
        'token_invalid' => 'Service token is invalid',
        'ip_not_allowed' => 'IP address is not allowed',
        'suspended' => 'Service is suspended',
        'disabled' => 'Service is disabled',
    ],

    // ================== RESOURCES ==================
    'user' => [
        'not_found' => 'User not found',
    ],
    'role' => [
        'not_found' => 'Role not found',
    ],
    'permission' => [
        'not_found' => 'Permission not found',
    ],
    'service' => [
        'not_found' => 'Service not found',
    ],

    // ================== VALIDATION ==================
    'validation' => [
        'email_required' => 'Email is required',
        'email_invalid' => 'Email is invalid',
        'email_not_found' => 'Email is not registered',
        'email_unique' => 'Email is already in use',
        'token_required' => 'Token is required',
        'password_required' => 'Password is required',
        'password_confirmation' => 'Password confirmation does not match',
        'password_min' => 'Password must be at least 8 characters',
        'password_different' => 'New password must be different from current password',
        'current_password_required' => 'Current password is required',
        'name_max' => 'Name must not exceed 255 characters',
        'phone_max' => 'Phone must not exceed 20 characters',
        'phone_required' => 'Phone is required',
        'roles_array' => 'Roles must be an array',
        'roles_exists' => 'One or more specified roles do not exist',

    ],

    // ================== GENERAL ==================
    'resource_not_found' => 'Resource not found',
    'route_not_found' => 'Route not found',
    'method_not_allowed' => 'Method not allowed',
    'rate_limit_exceeded' => 'Too many requests',
    'validation_failed' => 'Validation failed',
    'server_error' => 'Internal server error',
    'unauthorized' => 'Unauthorized',
    'forbidden' => 'Forbidden',
    'bad_request' => 'Bad request',
    'not_found' => 'Not found',
    'conflict' => 'Data conflict',
    'unprocessable_entity' => 'Unprocessable entity',
    'bad_gateway' => 'Bad gateway',
    'service_unavailable' => 'Service unavailable',
    'gateway_timeout' => 'Gateway timeout',
    'unknown_error' => 'Unknown error',
];
