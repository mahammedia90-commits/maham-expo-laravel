<?php

namespace App\Http\Controllers\Api\Investor;

/**
 * Investor Statistics Controller
 *
 * Provides the same statistics as the Investor Dashboard
 * under the /statistics route for consistency with the website.
 *
 * GET /api/v1/investor/statistics
 *
 * Shows stats only for investor's own spaces:
 * overview, spaces, revenue, requests, analytics, recent_activity
 */
class StatisticsController extends DashboardController
{
    // Inherits all functionality from Investor DashboardController
    // Returns: overview, spaces, revenue, requests, analytics, recent_activity
}
