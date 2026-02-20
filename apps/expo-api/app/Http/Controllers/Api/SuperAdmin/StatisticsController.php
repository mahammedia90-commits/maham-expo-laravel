<?php

namespace App\Http\Controllers\Api\SuperAdmin;

/**
 * SuperAdmin Statistics Controller
 *
 * Provides the same statistics as the SuperAdmin Dashboard
 * under the /statistics route for consistency with the website.
 *
 * GET /api/v1/super-admin/statistics
 *
 * Includes all Admin stats plus: system stats, analytics (page views, top events/spaces)
 */
class StatisticsController extends DashboardController
{
    // Inherits all functionality from SuperAdmin DashboardController
    // Returns: overview, spaces, revenue, visit_requests, rental_requests, recent_activity, system, analytics
}
