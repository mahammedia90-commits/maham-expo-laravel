<?php

namespace App\Http\Controllers\Api\Supervisor;

use App\Http\Controllers\Api\Admin\StatisticsController as AdminStatisticsController;

/**
 * Supervisor Statistics Controller
 *
 * Supervisors have the same statistics access as admins.
 *
 * GET /api/v1/supervisor/statistics
 */
class StatisticsController extends AdminStatisticsController
{
    // Inherits all functionality from Admin StatisticsController
    // Returns: overview, spaces, revenue, visit_requests, rental_requests, recent_activity
}
