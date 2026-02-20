<?php

namespace App\Http\Controllers\Api\Admin;

/**
 * Admin Statistics Controller
 *
 * Provides the same statistics as the Admin Dashboard
 * under the /statistics route for consistency with the website.
 *
 * GET /api/v1/admin/statistics
 */
class StatisticsController extends DashboardController
{
    // Inherits all functionality from Admin DashboardController
    // Returns: overview, spaces, revenue, visit_requests, rental_requests, recent_activity
}
