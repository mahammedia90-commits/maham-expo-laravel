<?php

/**
 * Admin Routes — Aliases for /admin/* that map to controllers (same as /manage/*)
 *
 * These routes provide the /admin/* paths expected by the maham-admin frontend
 * while maintaining the existing /manage/* routes for backward compatibility.
 */

use App\Http\Controllers\Api\AuthGatewayController;
use App\Http\Controllers\Api\Admin\{
    SponsorController as AdminSponsorController,
    SponsorPackageController as AdminSponsorPackageController,
    SponsorContractController as AdminSponsorContractController,
    SponsorPaymentController as AdminSponsorPaymentController,
    SponsorBenefitController as AdminSponsorBenefitController,
    SponsorAssetController as AdminSponsorAssetController,
    SponsorLeadController as AdminSponsorLeadController,
    SponsorDeliverableController as AdminSponsorDeliverableController,
    EventController as AdminEventController,
    SectionController as AdminSectionController,
    ServiceController as AdminServiceController,
    SpaceController as AdminSpaceController,
    VisitRequestController as AdminVisitRequestController,
    RentalRequestController as AdminRentalRequestController,
    BusinessProfileController as AdminBusinessProfileController,
    DashboardController as AdminDashboardController,
    StatisticsController as AdminStatisticsController,
    RatingController as AdminRatingController,
    SupportTicketController as AdminSupportTicketController,
    RentalContractController as AdminRentalContractController,
    InvoiceController as AdminInvoiceController,
    PageController as AdminPageController,
    FaqController as AdminFaqController,
    BannerController as AdminBannerController,
    AnalyticsController as AdminAnalyticsController,
    MemberTypeController as AdminMemberTypeController,
    BusinessActivityTypeController as AdminBusinessActivityTypeController,
    InvestorController as AdminInvestorController,
    MerchantController as AdminMerchantController,
    CrmController,
    OperationsController,
    PermissionsController,
    ContractWorkflowController,
    MessageController,
    ReportController,
    WorkforceController,
    RequestController,
    BadgeController,
    WaitlistController,
    DocumentController,
    ExhibitionMapController,
    OpportunityController,
    // Phase 3: Revenue Engine
    LeadsController,
    PipelineController,
    PerformanceController,
    EnforcementController,
    RevenueController,
    WorkflowController,
    AIController,
    ReportsController,
    NotificationsController,
};

use App\Http\Controllers\Api\SuperAdmin\{
    CategoryController as ManageCategoryController,
    CityController as ManageCityController,
    UserController as ManageUserController,
    SettingController as ManageSettingController,
};

// Portal Controllers (not in Api namespace)
use App\Http\Controllers\Admin\InvestorPortalController as PortalInvestorController;
use App\Http\Controllers\Admin\MerchantPortalController as PortalMerchantController;
use App\Http\Controllers\Admin\SponsorPortalController as PortalSponsorController;

use App\Http\Middleware\CheckPermission;

Route::prefix('admin')->group(function () {

    // ══════════════════════════════════════════════════════════════════
    // DASHBOARD & STATISTICS
    // ══════════════════════════════════════════════════════════════════
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->middleware(CheckPermission::class . ':reports.view');
    Route::get('/dashboard/stats', [AdminDashboardController::class, 'index'])
        ->middleware(CheckPermission::class . ':reports.view');
    Route::get('/dashboard/recent-activity', [AdminDashboardController::class, 'index'])
        ->middleware(CheckPermission::class . ':reports.view');
    Route::get('/dashboard/charts', [AdminDashboardController::class, 'index'])
        ->middleware(CheckPermission::class . ':reports.view');

    Route::get('/statistics', [AdminStatisticsController::class, 'index'])
        ->middleware(CheckPermission::class . ':reports.view');

    // ══════════════════════════════════════════════════════════════════
    // EVENTS
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('events')->group(function () {
        Route::get('/', [AdminEventController::class, 'index'])
            ->middleware(CheckPermission::class . ':events.view');
        Route::post('/', [AdminEventController::class, 'store'])
            ->middleware(CheckPermission::class . ':events.create');
        Route::get('/{event}', [AdminEventController::class, 'show'])
            ->middleware(CheckPermission::class . ':events.view');
        Route::put('/{event}', [AdminEventController::class, 'update'])
            ->middleware(CheckPermission::class . ':events.update');
        Route::delete('/{event}', [AdminEventController::class, 'destroy'])
            ->middleware(CheckPermission::class . ':events.delete');
        Route::post('/{event}/publish', [AdminEventController::class, 'publish'])
            ->middleware(CheckPermission::class . ':events.update');
        Route::get('/{event}/crowd', [AdminEventController::class, 'crowd'])
            ->middleware(CheckPermission::class . ':events.view');
    });

    // ══════════════════════════════════════════════════════════════════
    // REQUESTS (Unified: Visit + Rental)
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('requests')->group(function () {
        Route::get('/', [RequestController::class, 'index']);
        Route::get('/{id}', [RequestController::class, 'show']);
        Route::post('/{id}/approve', [RequestController::class, 'approve']);
        Route::post('/{id}/reject', [RequestController::class, 'reject']);
    });

    // ══════════════════════════════════════════════════════════════════
    // USERS (Proxy to Auth Service)
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('users')->group(function () {
        Route::get('/', [ManageUserController::class, 'index'])
            ->middleware(CheckPermission::class . ':profiles.view-all');
        Route::get('/{profile}', [ManageUserController::class, 'show'])
            ->middleware(CheckPermission::class . ':profiles.view-all');
        Route::post('/', [ManageUserController::class, 'store'])
            ->middleware(CheckPermission::class . ':profiles.view-all');
        Route::put('/{profile}', [ManageUserController::class, 'update'])
            ->middleware(CheckPermission::class . ':profiles.view-all');
        Route::delete('/{profile}', [ManageUserController::class, 'destroy'])
            ->middleware(CheckPermission::class . ':profiles.view-all');
    });

    Route::prefix('roles')->group(function () {
        Route::get('/', [PermissionsController::class, 'roles']);
    });

    Route::prefix('permissions')->group(function () {
        Route::get('/', [PermissionsController::class, 'permissions']);
    });

    // ══════════════════════════════════════════════════════════════════
    // FINANCE
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('finance')->group(function () {
        Route::prefix('invoices')->group(function () {
            Route::get('/', [AdminInvoiceController::class, 'index'])
                ->middleware(CheckPermission::class . ':invoices.view');
            Route::get('/{invoice}', [AdminInvoiceController::class, 'show'])
                ->middleware(CheckPermission::class . ':invoices.view');
            Route::post('/', [AdminInvoiceController::class, 'store'])
                ->middleware(CheckPermission::class . ':invoices.create');
            Route::post('/{invoice}/zatca', [AdminInvoiceController::class, 'submitZatca'])
                ->middleware(CheckPermission::class . ':invoices.update');
        });

        Route::prefix('payments')->group(function () {
            Route::get('/', [AdminSponsorPaymentController::class, 'index'])
                ->middleware(CheckPermission::class . ':payments.view');
            Route::post('/{payment}/refund', [AdminSponsorPaymentController::class, 'refund'])
                ->middleware(CheckPermission::class . ':payments.update');
        });
    });

    // ══════════════════════════════════════════════════════════════════
    // CRM & SALES
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('crm')->group(function () {
        // Leads
        Route::prefix('leads')->group(function () {
            Route::get('/', [CrmController::class, 'leads']);
            Route::post('/', [CrmController::class, 'createLead']);
            Route::get('/{id}', [CrmController::class, 'showLead']);
            Route::put('/{id}', [CrmController::class, 'updateLead']);
            Route::delete('/{id}', [CrmController::class, 'deleteLead']);
            Route::post('/{id}/score', [CrmController::class, 'scoreLead']);
            Route::post('/import', [CrmController::class, 'importLeads']);
            Route::post('/bulk-assign', [CrmController::class, 'bulkAssignLeads']);
            Route::post('/bulk-status', [CrmController::class, 'bulkStatusLeads']);
            Route::get('/export', [CrmController::class, 'exportLeads']);
        });

        // Pipeline
        Route::get('/pipeline', [CrmController::class, 'dashboard']);

        // Activities
        Route::prefix('activities')->group(function () {
            Route::get('/', [CrmController::class, 'activities']);
            Route::post('/', [CrmController::class, 'logActivity']);
        });

        // Follow-ups
        Route::prefix('followups')->group(function () {
            Route::get('/', [CrmController::class, 'tasks']); // Alias for tasks
            Route::post('/', [CrmController::class, 'tasks']);
            Route::put('/{id}/complete', [CrmController::class, 'tasks']);
        });
    });

    // ══════════════════════════════════════════════════════════════════
    // SPONSOR MANAGEMENT
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('sponsors')->group(function () {
        Route::get('/', [AdminSponsorController::class, 'index'])
            ->middleware(CheckPermission::class . ':sponsors.view-all');
        Route::post('/', [AdminSponsorController::class, 'store'])
            ->middleware(CheckPermission::class . ':sponsors.create');
        Route::get('/{sponsor}', [AdminSponsorController::class, 'show'])
            ->middleware(CheckPermission::class . ':sponsors.view-all');
        Route::put('/{sponsor}', [AdminSponsorController::class, 'update'])
            ->middleware(CheckPermission::class . ':sponsors.update');
        Route::delete('/{sponsor}', [AdminSponsorController::class, 'destroy'])
            ->middleware(CheckPermission::class . ':sponsors.delete');
        Route::put('/{sponsor}/approve', [AdminSponsorController::class, 'approve'])
            ->middleware(CheckPermission::class . ':sponsors.approve');
        Route::put('/{sponsor}/reject', [AdminSponsorController::class, 'reject'])
            ->middleware(CheckPermission::class . ':sponsors.reject');
    });

    // ══════════════════════════════════════════════════════════════════
    // SPONSOR ASSETS
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('sponsor-assets')->group(function () {
        Route::get('/', [AdminSponsorAssetController::class, 'index'])
            ->middleware(CheckPermission::class . ':sponsor-assets.view');
        Route::get('/{asset}', [AdminSponsorAssetController::class, 'show'])
            ->middleware(CheckPermission::class . ':sponsor-assets.view');
        Route::post('/{asset}/approve', [AdminSponsorAssetController::class, 'approve'])
            ->middleware(CheckPermission::class . ':sponsor-assets.approve');
        Route::post('/{asset}/reject', [AdminSponsorAssetController::class, 'reject'])
            ->middleware(CheckPermission::class . ':sponsor-assets.reject');
        Route::get('/stats', [AdminSponsorAssetController::class, 'stats']);
    });

    // ══════════════════════════════════════════════════════════════════
    // PACKAGES
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('packages')->group(function () {
        Route::get('/', [AdminSponsorPackageController::class, 'index'])
            ->middleware(CheckPermission::class . ':sponsor-packages.view');
        Route::post('/', [AdminSponsorPackageController::class, 'store'])
            ->middleware(CheckPermission::class . ':sponsor-packages.create');
        Route::get('/{package}', [AdminSponsorPackageController::class, 'show'])
            ->middleware(CheckPermission::class . ':sponsor-packages.view');
        Route::put('/{package}', [AdminSponsorPackageController::class, 'update'])
            ->middleware(CheckPermission::class . ':sponsor-packages.update');
        Route::delete('/{package}', [AdminSponsorPackageController::class, 'destroy'])
            ->middleware(CheckPermission::class . ':sponsor-packages.delete');
        Route::get('/sectors', [AdminSponsorPackageController::class, 'sectors']);
        Route::get('/stats', [AdminSponsorPackageController::class, 'stats']);
    });

    // ══════════════════════════════════════════════════════════════════
    // TEAMS
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('teams')->group(function () {
        Route::get('/', [AdminMemberTypeController::class, 'index'])
            ->middleware(CheckPermission::class . ':member-types.view');
        Route::post('/', [AdminMemberTypeController::class, 'store'])
            ->middleware(CheckPermission::class . ':member-types.create');
        Route::get('/{team}', [AdminMemberTypeController::class, 'show'])
            ->middleware(CheckPermission::class . ':member-types.view');
        Route::put('/{team}', [AdminMemberTypeController::class, 'update'])
            ->middleware(CheckPermission::class . ':member-types.update');
        Route::delete('/{team}', [AdminMemberTypeController::class, 'destroy'])
            ->middleware(CheckPermission::class . ':member-types.delete');
        Route::get('/{team}/members', [AdminMemberTypeController::class, 'show']);
        Route::post('/{team}/members', [AdminMemberTypeController::class, 'store']);
        Route::delete('/{team}/members/{userId}', [AdminMemberTypeController::class, 'destroy']);
        Route::get('/stats', [AdminMemberTypeController::class, 'stats']);
    });

    // ══════════════════════════════════════════════════════════════════
    // SPACES
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('spaces')->group(function () {
        Route::get('/', [AdminSpaceController::class, 'index'])
            ->middleware(CheckPermission::class . ':spaces.view');
        Route::post('/', [AdminSpaceController::class, 'store'])
            ->middleware(CheckPermission::class . ':spaces.create');
        Route::get('/{space}', [AdminSpaceController::class, 'show'])
            ->middleware(CheckPermission::class . ':spaces.view');
        Route::put('/{space}', [AdminSpaceController::class, 'update'])
            ->middleware(CheckPermission::class . ':spaces.update');
        Route::delete('/{space}', [AdminSpaceController::class, 'destroy'])
            ->middleware(CheckPermission::class . ':spaces.delete');
        Route::put('/{space}/approve', [AdminSpaceController::class, 'approve'])
            ->middleware(CheckPermission::class . ':spaces.update');
        Route::put('/{space}/reject', [AdminSpaceController::class, 'reject'])
            ->middleware(CheckPermission::class . ':spaces.update');
    });

    // ══════════════════════════════════════════════════════════════════
    // SETTINGS
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('settings')->group(function () {
        Route::get('/general', [ManageSettingController::class, 'index'])
            ->middleware(CheckPermission::class . ':settings.view');
        Route::put('/general', [ManageSettingController::class, 'update'])
            ->middleware(CheckPermission::class . ':settings.update');
        Route::get('/notifications', [ManageSettingController::class, 'show'])
            ->middleware(CheckPermission::class . ':settings.view');
        Route::put('/notifications', [ManageSettingController::class, 'update'])
            ->middleware(CheckPermission::class . ':settings.update');
    });

    // ══════════════════════════════════════════════════════════════════
    // NOTIFICATIONS
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationsController::class, 'index']);
        Route::post('/{id}/read', [NotificationsController::class, 'read']);
        Route::post('/read-all', [NotificationsController::class, 'readAll']);
        Route::delete('/{id}', [NotificationsController::class, 'destroy']);
        Route::get('/unread-count', [NotificationsController::class, 'unreadCount']);
        Route::get('/search', [NotificationsController::class, 'search']);
        Route::get('/preferences', [NotificationsController::class, 'preferences']);
        Route::put('/preferences', [NotificationsController::class, 'updatePreferences']);
    });

    // ══════════════════════════════════════════════════════════════════
    // MESSAGES
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('messages')->group(function () {
        Route::get('/conversations', [MessageController::class, 'inbox']);
        Route::get('/conversations/{id}', [MessageController::class, 'inbox']);
        Route::post('/send', [MessageController::class, 'send']);
        Route::post('/conversations/{id}/read', [MessageController::class, 'send']);
        Route::get('/unread-count', [MessageController::class, 'inbox']);
        Route::get('/search', [MessageController::class, 'inbox']);
    });

    // ══════════════════════════════════════════════════════════════════
    // BADGES
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('badges')->group(function () {
        Route::get('/', [BadgeController::class, 'index']);
        Route::post('/', [BadgeController::class, 'store']);
        Route::get('/{badge}', [BadgeController::class, 'show']);
        Route::put('/{badge}', [BadgeController::class, 'update']);
        Route::delete('/{badge}', [BadgeController::class, 'destroy']);
        Route::post('/grant', [BadgeController::class, 'grant']);
        Route::get('/{badge}/holders', [BadgeController::class, 'holders']);
        Route::get('/stats', [BadgeController::class, 'stats']);
    });

    // ══════════════════════════════════════════════════════════════════
    // WAITLIST
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('waitlist')->group(function () {
        Route::get('/', [WaitlistController::class, 'index']);
        Route::post('/{waitlist}/approve', [WaitlistController::class, 'approve']);
        Route::post('/{waitlist}/reject', [WaitlistController::class, 'reject']);
        Route::post('/reorder', [WaitlistController::class, 'reorder']);
        Route::get('/stats', [WaitlistController::class, 'stats']);
    });

    // ══════════════════════════════════════════════════════════════════
    // DOCUMENTS
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('documents')->group(function () {
        Route::get('/', [DocumentController::class, 'index']);
        Route::post('/', [DocumentController::class, 'store']);
        Route::get('/{document}', [DocumentController::class, 'show']);
        Route::patch('/{document}/status', [DocumentController::class, 'updateStatus']);
        Route::delete('/{document}', [DocumentController::class, 'destroy']);
        Route::get('/export', [DocumentController::class, 'export']);
        Route::get('/stats', [DocumentController::class, 'stats']);
    });

    // ══════════════════════════════════════════════════════════════════
    // EXHIBITION MAP
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('exhibition-map')->group(function () {
        Route::get('/{event}', [ExhibitionMapController::class, 'show']);
        Route::put('/spaces/{space}', [ExhibitionMapController::class, 'updateSpace']);
        Route::get('/{event}/stats', [ExhibitionMapController::class, 'stats']);
    });

    // ══════════════════════════════════════════════════════════════════
    // EXHIBITOR SERVICES
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('exhibitor-services')->group(function () {
        Route::get('/', [AdminServiceController::class, 'index'])
            ->middleware(CheckPermission::class . ':expo-services.view');
        Route::get('/{service}', [AdminServiceController::class, 'show'])
            ->middleware(CheckPermission::class . ':expo-services.view');
        Route::post('/', [AdminServiceController::class, 'store'])
            ->middleware(CheckPermission::class . ':expo-services.create');
        Route::put('/{service}', [AdminServiceController::class, 'update'])
            ->middleware(CheckPermission::class . ':expo-services.update');
        Route::get('/requests', [AdminServiceController::class, 'index']);
        Route::post('/requests/{request}/approve', [AdminServiceController::class, 'store']);
        Route::get('/stats', [AdminServiceController::class, 'index']);
    });

    // ══════════════════════════════════════════════════════════════════
    // OPPORTUNITIES
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('opportunities')->group(function () {
        Route::get('/', [OpportunityController::class, 'index']);
        Route::post('/', [OpportunityController::class, 'store']);
        Route::get('/{opportunity}', [OpportunityController::class, 'show']);
        Route::put('/{opportunity}', [OpportunityController::class, 'update']);
        Route::delete('/{opportunity}', [OpportunityController::class, 'destroy']);
        Route::get('/stats', [OpportunityController::class, 'stats']);
    });

    // ══════════════════════════════════════════════════════════════════
    // BOOKINGS
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('bookings')->group(function () {
        Route::get('/', [AdminRentalRequestController::class, 'index'])
            ->middleware(CheckPermission::class . ':rental-requests.view-all');
        Route::get('/{booking}', [AdminRentalRequestController::class, 'show'])
            ->middleware(CheckPermission::class . ':rental-requests.view-all');
    });

    // ══════════════════════════════════════════════════════════════════
    // KYC REQUESTS
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('kyc')->group(function () {
        Route::get('/', [AdminBusinessProfileController::class, 'index'])
            ->middleware(CheckPermission::class . ':profiles.view-all');
    });

    // ══════════════════════════════════════════════════════════════════
    // CONTRACTS
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('contracts')->group(function () {
        Route::get('/', [AdminRentalContractController::class, 'index'])
            ->middleware(CheckPermission::class . ':rental-contracts.view-all');
    });

    // ══════════════════════════════════════════════════════════════════
    // WORKFLOWS
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('workflows')->group(function () {
        Route::get('/', [ContractWorkflowController::class, 'listWithWorkflow']);
        Route::post('/', [ContractWorkflowController::class, 'create']);
        Route::post('/{workflow}/trigger', [ContractWorkflowController::class, 'trigger']);
        Route::get('/{workflow}/logs', [ContractWorkflowController::class, 'logs']);
    });

    // ════════════════════════════════════════════════════════════════════════════════════
    // PHASE 3: REVENUE ENGINE ROUTES
    // ════════════════════════════════════════════════════════════════════════════════════

    // ══════════════════════════════════════════════════════════════════
    // LEADS (Full CRUD + Scoring)
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('crm/leads')->group(function () {
        Route::get('/', [LeadsController::class, 'index']);
        Route::post('/', [LeadsController::class, 'store']);
        Route::get('/{id}', [LeadsController::class, 'show']);
        Route::put('/{id}', [LeadsController::class, 'update']);
        Route::delete('/{id}', [LeadsController::class, 'destroy']);
        Route::post('/{id}/score', [LeadsController::class, 'score']);
        Route::post('/import', [LeadsController::class, 'import']);
        Route::post('/bulk-assign', [LeadsController::class, 'bulkAssign']);
        Route::post('/bulk-status', [LeadsController::class, 'bulkStatus']);
        Route::get('/export', [LeadsController::class, 'export']);
    });

    // ══════════════════════════════════════════════════════════════════
    // SALES PIPELINE (Stages, Deals, At-Risk Detection)
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('sales')->group(function () {
        Route::get('/pipeline', [PipelineController::class, 'index']);
        Route::post('/deals', [PipelineController::class, 'storeDeal']);
        Route::put('/deals/{dealId}/stage', [PipelineController::class, 'moveStage']);
        Route::get('/deals/at-risk', [PipelineController::class, 'atRisk']);
        Route::post('/deals/{dealId}/activities', [PipelineController::class, 'logActivity']);
        Route::get('/followups', [PipelineController::class, 'followups']);
        Route::post('/followups', [PipelineController::class, 'storeFollowup']);
        Route::put('/followups/{id}/complete', [PipelineController::class, 'completeFollowup']);
    });

    // ══════════════════════════════════════════════════════════════════
    // PERFORMANCE (Team KPIs, Leaderboard, Daily Reports)
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('performance')->group(function () {
        Route::get('/team', [PerformanceController::class, 'team']);
        Route::get('/rep/{id}', [PerformanceController::class, 'rep']);
        Route::get('/leaderboard', [PerformanceController::class, 'leaderboard']);
        Route::post('/daily-report', [PerformanceController::class, 'dailyReport']);
    });

    // ══════════════════════════════════════════════════════════════════
    // ENFORCEMENT (Idle Detection, Warnings)
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('enforcement')->group(function () {
        Route::get('/idle', [EnforcementController::class, 'idle']);
        Route::post('/warnings/{userId}', [EnforcementController::class, 'issueWarning']);
        Route::get('/warnings', [EnforcementController::class, 'warnings']);
        Route::get('/task-queue', [EnforcementController::class, 'taskQueue']);
    });

    // ══════════════════════════════════════════════════════════════════
    // REVENUE (Dashboard, Forecasting, Breakdown)
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('revenue')->group(function () {
        Route::get('/dashboard', [RevenueController::class, 'dashboard']);
        Route::get('/forecast', [RevenueController::class, 'forecast']);
        Route::get('/by-type', [RevenueController::class, 'byType']);
    });

    // ══════════════════════════════════════════════════════════════════
    // WORKFLOWS (Revenue Engine Workflows)
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('revenue-workflows')->group(function () {
        Route::get('/', [WorkflowController::class, 'index']);
        Route::post('/', [WorkflowController::class, 'store']);
        Route::post('/{id}/trigger', [WorkflowController::class, 'trigger']);
        Route::get('/{id}/logs', [WorkflowController::class, 'logs']);
    });

    // ══════════════════════════════════════════════════════════════════
    // AI SALES INTELLIGENCE
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('ai')->group(function () {
        // Existing AI routes
        Route::get('/priority-leads', [AIController::class, 'priorityLeads']);
        Route::get('/at-risk-deals', [AIController::class, 'atRiskDeals']);
        Route::get('/recommendations', [AIController::class, 'recommendations']);
        Route::get('/recommendations/{type}', [AIController::class, 'getRecommendations']);
        Route::get('/forecast', [AIController::class, 'forecast']);

        // New AI routes
        Route::post('/executive-brain', [AIController::class, 'executiveBrain']);
        Route::get('/lead-scoring/{leadId}', [AIController::class, 'leadScoring']);
        Route::get('/revenue-forecast', [AIController::class, 'revenueForecast']);
        Route::get('/anomaly-detection', [AIController::class, 'anomalyDetection']);
        Route::post('/sentiment', [AIController::class, 'sentimentAnalysis']);
        Route::get('/risk-assessment/{eventId}', [AIController::class, 'riskAssessment']);
        Route::get('/customer-segmentation', [AIController::class, 'customerSegmentation']);
        Route::post('/chatbot', [AIController::class, 'chatbot']);
        Route::post('/auto-schedule/{eventId}', [AIController::class, 'autoSchedule']);
        Route::post('/content-generation', [AIController::class, 'contentGeneration']);
        Route::get('/performance-optimization', [AIController::class, 'performanceOptimization']);
    });

    // ══════════════════════════════════════════════════════════════════
    // REPORTS (Financial, Events, Users, Audit Logs)
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('reports')->group(function () {
        Route::get('/financial', [ReportsController::class, 'financial']);
        Route::get('/events', [ReportsController::class, 'events']);
        Route::get('/users', [ReportsController::class, 'users']);
        Route::get('/audit-logs', [ReportsController::class, 'auditLogs']);
        Route::get('/export/{type}', [ReportsController::class, 'export']);
    });

    // ══════════════════════════════════════════════════════════════════
    // CRM LEADS - Import/Export/Bulk Operations
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('crm/leads')->group(function () {
        Route::post('/import', [LeadsController::class, 'import']);
        Route::post('/bulk-assign', [LeadsController::class, 'bulkAssign']);
        Route::post('/bulk-status', [LeadsController::class, 'bulkStatus']);
        Route::get('/export', [LeadsController::class, 'export']);
    });

    // ══════════════════════════════════════════════════════════════════
    // INVESTOR PORTAL
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('investor-portal')->group(function () {
        Route::get('/dashboard', [PortalInvestorController::class, 'dashboard']);
        Route::get('/portfolio', [PortalInvestorController::class, 'portfolio']);
        Route::get('/investors', [PortalInvestorController::class, 'investors']);
        Route::get('/opportunities', [PortalInvestorController::class, 'opportunities']);
        Route::get('/contracts', [PortalInvestorController::class, 'contracts']);
        Route::get('/messages', [PortalInvestorController::class, 'messages']);
        Route::get('/due-diligence', [PortalInvestorController::class, 'dueDiligence']);
        Route::get('/risk-management', [PortalInvestorController::class, 'riskManagement']);
    });

    // ══════════════════════════════════════════════════════════════════
    // MERCHANT PORTAL
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('merchant-portal')->group(function () {
        Route::get('/dashboard', [PortalMerchantController::class, 'dashboard']);
        Route::get('/merchants', [PortalMerchantController::class, 'merchants']);
        Route::get('/booths', [PortalMerchantController::class, 'booths']);
        Route::get('/booth-map/{eventId}', [PortalMerchantController::class, 'boothMap']);
        Route::get('/booth-requests', [PortalMerchantController::class, 'boothRequests']);
        Route::get('/contracts', [PortalMerchantController::class, 'contracts']);
        Route::get('/services', [PortalMerchantController::class, 'services']);
        Route::get('/payments', [PortalMerchantController::class, 'payments']);
        Route::get('/messages', [PortalMerchantController::class, 'messages']);
    });

    // ══════════════════════════════════════════════════════════════════
    // SPONSOR PORTAL
    // ══════════════════════════════════════════════════════════════════
    Route::prefix('sponsor-portal')->group(function () {
        Route::get('/dashboard', [PortalSponsorController::class, 'dashboard']);
        Route::get('/sponsors', [PortalSponsorController::class, 'sponsors']);
        Route::get('/packages', [PortalSponsorController::class, 'packages']);
        Route::get('/contracts', [PortalSponsorController::class, 'contracts']);
        Route::get('/roi-metrics', [PortalSponsorController::class, 'roiMetrics']);
        Route::get('/deliverables', [PortalSponsorController::class, 'deliverables']);
        Route::get('/messages', [PortalSponsorController::class, 'messages']);
        Route::get('/upcoming-events', [PortalSponsorController::class, 'upcomingEvents']);
    });

}); // End /admin prefix
