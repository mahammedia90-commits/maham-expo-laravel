<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait TracksPlatform
{
    /**
     * Get the platform from request header or default
     */
    protected function getPlatform(Request $request): string
    {
        $platform = $request->header('X-Platform', 'web');

        if (!in_array($platform, ['web', 'mobile', 'api'])) {
            return 'web';
        }

        return $platform;
    }

    /**
     * Get current authenticated user ID from request
     */
    protected function getCurrentUserId(Request $request): ?string
    {
        return $request->input('auth_user_id');
    }

    /**
     * Get tracking data for model creation
     */
    protected function getTrackingData(Request $request): array
    {
        return [
            'created_from' => $this->getPlatform($request),
            'created_by' => $this->getCurrentUserId($request),
        ];
    }
}
