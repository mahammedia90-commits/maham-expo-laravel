<?php

namespace App\Enums;

enum OneSignalSubscriptionType: string
{
    case ANDROID_PUSH = 'AndroidPush';
    case IOS_PUSH = 'iOSPush';
    case CHROME_PUSH = 'ChromePush';
    case FIREFOX_PUSH = 'FirefoxPush';
    case SAFARI_PUSH = 'SafariPush';
    case WEB_PUSH = 'WebPush';
    case EMAIL = 'Email';
    case SMS = 'SMS';

    /**
     * Get all push notification types
     */
    public static function pushTypes(): array
    {
        return [
            self::ANDROID_PUSH,
            self::IOS_PUSH,
            self::CHROME_PUSH,
            self::FIREFOX_PUSH,
            self::SAFARI_PUSH,
            self::WEB_PUSH,
        ];
    }

    /**
     * Get mobile push types only
     */
    public static function mobileTypes(): array
    {
        return [
            self::ANDROID_PUSH,
            self::IOS_PUSH,
        ];
    }

    /**
     * Get web push types only
     */
    public static function webTypes(): array
    {
        return [
            self::CHROME_PUSH,
            self::FIREFOX_PUSH,
            self::SAFARI_PUSH,
            self::WEB_PUSH,
        ];
    }

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match($this) {
            self::ANDROID_PUSH => 'Android',
            self::IOS_PUSH => 'iOS',
            self::CHROME_PUSH => 'Chrome Browser',
            self::FIREFOX_PUSH => 'Firefox Browser',
            self::SAFARI_PUSH => 'Safari Browser',
            self::WEB_PUSH => 'Web Push',
            self::EMAIL => 'Email',
            self::SMS => 'SMS',
        };
    }

    /**
     * Check if this is a mobile type
     */
    public function isMobile(): bool
    {
        return in_array($this, self::mobileTypes());
    }

    /**
     * Check if this is a web type
     */
    public function isWeb(): bool
    {
        return in_array($this, self::webTypes());
    }
}
