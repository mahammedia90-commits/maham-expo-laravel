<?php

namespace App\Http\Controllers\Api\Merchant;

/**
 * Merchant Statistics Controller
 *
 * Provides the same statistics as the Merchant Dashboard
 * under the /statistics route for consistency with the website.
 *
 * GET /api/v1/merchant/statistics
 *
 * Shows merchant's own data:
 * profile status, rental_requests, visit_requests, recent_activity
 */
class StatisticsController extends DashboardController
{
    // Inherits all functionality from Merchant DashboardController
    // Returns: profile, rental_requests, visit_requests, recent_activity
}
