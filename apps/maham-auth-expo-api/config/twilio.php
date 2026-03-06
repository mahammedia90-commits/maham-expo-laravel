<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Twilio Verify Configuration
    |--------------------------------------------------------------------------
    |
    | Configure your Twilio credentials for OTP verification via SMS/WhatsApp.
    | Get your credentials from: https://console.twilio.com
    |
    */

    'account_sid' => env('TWILIO_ACCOUNT_SID', ''),
    'auth_token' => env('TWILIO_AUTH_TOKEN', ''),
    'verify_service_sid' => env('TWILIO_VERIFY_SERVICE_SID', ''),

    'base_url' => 'https://verify.twilio.com/v2',

    // Default channel: sms or whatsapp
    'default_channel' => env('TWILIO_DEFAULT_CHANNEL', 'sms'),

    // OTP code length (configured in Twilio Verify Service)
    'code_length' => env('TWILIO_CODE_LENGTH', 6),

    // Rate limiting
    'max_attempts_per_hour' => env('TWILIO_MAX_ATTEMPTS', 5),
];
