<?php

namespace App\Http\Controllers\Api\Supervisor;

use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;

/**
 * Supervisor Dashboard Controller
 *
 * Supervisors have the same dashboard access as admins
 * They can view all statistics and reports
 */
class DashboardController extends AdminDashboardController
{
    // Inherits all functionality from Admin DashboardController
}
