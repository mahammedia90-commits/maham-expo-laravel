<?php

use App\Http\Controllers\Api\BusinessProfileController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\RentalRequestController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SpaceController;
use App\Http\Controllers\Api\SponsorController;
use App\Http\Controllers\Api\StatisticsController;
use App\Http\Controllers\Api\VisitRequestController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\SupportTicketController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\NotificationPreferenceController;
use App\Http\Controllers\Api\DeviceController;

// Management Controllers (Admin/Supervisor/SuperAdmin — permission-based, no role checks)
use App\Http\Controllers\Api\Admin\SponsorController as AdminSponsorController;
use App\Http\Controllers\Api\Admin\SponsorPackageController as AdminSponsorPackageController;
use App\Http\Controllers\Api\Admin\SponsorContractController as AdminSponsorContractController;
use App\Http\Controllers\Api\Admin\SponsorPaymentController as AdminSponsorPaymentController;
use App\Http\Controllers\Api\Admin\SponsorBenefitController as AdminSponsorBenefitController;
use App\Http\Controllers\Api\Admin\SponsorAssetController as AdminSponsorAssetController;
use App\Http\Controllers\Api\Admin\EventController as AdminEventController;
use App\Http\Controllers\Api\Admin\SectionController as AdminSectionController;
use App\Http\Controllers\Api\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Api\Admin\SpaceController as AdminSpaceController;
use App\Http\Controllers\Api\Admin\VisitRequestController as AdminVisitRequestController;
use App\Http\Controllers\Api\Admin\RentalRequestController as AdminRentalRequestController;
use App\Http\Controllers\Api\Admin\BusinessProfileController as AdminBusinessProfileController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\StatisticsController as AdminStatisticsController;
use App\Http\Controllers\Api\Admin\RatingController as AdminRatingController;
use App\Http\Controllers\Api\Admin\SupportTicketController as AdminSupportTicketController;
use App\Http\Controllers\Api\Admin\RentalContractController as AdminRentalContractController;
use App\Http\Controllers\Api\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Api\Admin\PageController as AdminPageController;
use App\Http\Controllers\Api\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Api\Admin\BannerController as AdminBannerController;

// Owner-scoped Controllers (Investor/Merchant/Sponsor)
use App\Http\Controllers\Api\Investor\SpaceController as InvestorSpaceController;
use App\Http\Controllers\Api\Investor\RentalRequestController as InvestorRentalRequestController;
use App\Http\Controllers\Api\Investor\VisitRequestController as InvestorVisitRequestController;
use App\Http\Controllers\Api\Investor\PaymentController as InvestorPaymentController;
use App\Http\Controllers\Api\Merchant\VisitRequestController as MerchantVisitRequestController;
use App\Http\Controllers\Api\Merchant\RentalRequestController as MerchantRentalRequestController;
use App\Http\Controllers\Api\Sponsor\ContractController as SponsorContractController;
use App\Http\Controllers\Api\Sponsor\PaymentController as SponsorPaymentController;
use App\Http\Controllers\Api\Sponsor\AssetController as SponsorAssetController;
use App\Http\Controllers\Api\Sponsor\ExposureController as SponsorExposureController;

// Management-only Controllers (Categories, Cities, Settings, Users)
use App\Http\Controllers\Api\SuperAdmin\CategoryController as ManageCategoryController;
use App\Http\Controllers\Api\SuperAdmin\CityController as ManageCityController;
use App\Http\Controllers\Api\SuperAdmin\UserController as ManageUserController;
use App\Http\Controllers\Api\SuperAdmin\SettingController as ManageSettingController;

// Unified User Dashboard
use App\Http\Controllers\Api\My\DashboardController as MyDashboardController;

use App\Http\Controllers\Api\PaymentGatewayController;
use App\Http\Controllers\Api\Webhook\TapWebhookController;

use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\Api\My\ActivityController as MyActivityController;
use App\Http\Controllers\Api\Admin\AnalyticsController as AdminAnalyticsController;

use App\Http\Middleware\AuthServiceMiddleware;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\CheckVerifiedProfile;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes v1 — Permission-Based (No Hardcoded Roles)
|--------------------------------------------------------------------------
|
| All routes use check.permission middleware instead of check.role.
| Access is controlled by the permissions assigned to roles in the
| auth service. Create new roles and assign permissions dynamically
| without changing any code here.
|
| Structure:
|   /v1/              — Public routes (no auth)
|   /v1/profile, etc  — Authenticated self-service (any user)
|   /v1/my/           — Owner-scoped data (investor spaces, merchant requests, etc.)
|   /v1/manage/       — Management operations (admin, supervisor, etc.)
|
*/

Route::middleware([SetLocale::class])->group(function () {

    // Health Check (controller-based so route:cache works in production)
    Route::get('/health', HealthController::class);

    Route::prefix('v1')->group(function () {

        // ==================== PUBLIC ROUTES ====================

        // Categories
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::get('/categories/{category}', [CategoryController::class, 'show']);

        // Cities
        Route::get('/cities', [CityController::class, 'index']);
        Route::get('/cities/{city}', [CityController::class, 'show']);

        // Events (Public)
        Route::get('/events', [EventController::class, 'index']);
        Route::get('/events/featured', [EventController::class, 'featured']);
        Route::get('/events/{event}', [EventController::class, 'show']);
        Route::get('/events/{event}/spaces', [EventController::class, 'spaces']);
        Route::get('/events/{event}/sections', [EventController::class, 'sections']);

        // Sponsors (Public)
        Route::get('/events/{event}/sponsors', [SponsorController::class, 'index']);
        Route::get('/events/{event}/sponsor-packages', [SponsorController::class, 'packages']);

        // Spaces (Public)
        Route::get('/spaces/{space}', [SpaceController::class, 'show']);

        // Services (Public)
        Route::get('/services', [ServiceController::class, 'index']);

        // Auth Mode (Public - tells frontend which login flow to use)
        Route::get('/auth-mode', function () {
            $settings = \Illuminate\Support\Facades\Cache::get('system_settings', []);
            return response()->json([
                'success' => true,
                'data' => [
                    'auth_mode' => $settings['auth_mode'] ?? 'phone_and_otp',
                ],
            ]);
        });

        // Statistics (Public - Website)
        Route::prefix('statistics')->group(function () {
            Route::get('/', [StatisticsController::class, 'index']);
            Route::get('/events', [StatisticsController::class, 'events']);
            Route::get('/spaces', [StatisticsController::class, 'spaces']);
        });

        // Ratings (Public - Read)
        Route::prefix('ratings')->group(function () {
            Route::get('/', [RatingController::class, 'index']);
            Route::get('/summary', [RatingController::class, 'summary']);
        });

        // Pages (Public - CMS)
        Route::get('/pages', [PageController::class, 'index']);
        Route::get('/pages/{slug}', [PageController::class, 'show']);

        // FAQs (Public)
        Route::prefix('faqs')->group(function () {
            Route::get('/', [FaqController::class, 'index']);
            Route::get('/categories', [FaqController::class, 'categories']);
            Route::get('/{faq}', [FaqController::class, 'show']);
            Route::post('/{faq}/helpful', [FaqController::class, 'helpful']);
        });

        // Banners (Public)
        Route::get('/banners', [BannerController::class, 'index']);
        Route::post('/banners/{banner}/click', [BannerController::class, 'click']);

        // ==================== TRACKING ROUTES ====================
        // These work for both authenticated & anonymous users.
        // If user is authenticated, user_id is recorded; otherwise null.
        Route::prefix('track')->group(function () {
            Route::post('/view', [TrackingController::class, 'view']);
            Route::post('/action', [TrackingController::class, 'action']);
        });

        // ==================== WEBHOOK ROUTES (No Auth) ====================
        Route::prefix('webhooks')->group(function () {
            Route::post('/tap', [TapWebhookController::class, 'handle']);
        });

        // ==================== AUTHENTICATED ROUTES ====================

        Route::middleware([AuthServiceMiddleware::class])->group(function () {

            // ── Self-Service (any authenticated user) ──────────────────

            // Business Profile
            Route::prefix('profile')->group(function () {
                Route::get('/', [BusinessProfileController::class, 'show']);
                Route::post('/', [BusinessProfileController::class, 'store']);
                Route::put('/', [BusinessProfileController::class, 'update']);
            });

            // Favorites
            Route::prefix('favorites')->group(function () {
                Route::get('/', [FavoriteController::class, 'index']);
                Route::post('/', [FavoriteController::class, 'store']);
                Route::delete('/{favorite}', [FavoriteController::class, 'destroy']);
            });

            // Notifications
            Route::prefix('notifications')->group(function () {
                Route::get('/', [NotificationController::class, 'index'])
                    ->middleware(CheckPermission::class . ':notifications.view');
                Route::get('/unread-count', [NotificationController::class, 'unreadCount'])
                    ->middleware(CheckPermission::class . ':notifications.view');
                Route::put('/{notification}/read', [NotificationController::class, 'markAsRead'])
                    ->middleware(CheckPermission::class . ':notifications.update');
                Route::put('/read-all', [NotificationController::class, 'markAllAsRead'])
                    ->middleware(CheckPermission::class . ':notifications.update');

                // Notification Preferences
                Route::get('/preferences', [NotificationPreferenceController::class, 'show'])
                    ->middleware(CheckPermission::class . ':notification-preferences.view');
                Route::put('/preferences', [NotificationPreferenceController::class, 'update'])
                    ->middleware(CheckPermission::class . ':notification-preferences.update');
            });

            // Devices (Push Notification Registration)
            Route::prefix('devices')->group(function () {
                Route::get('/', [DeviceController::class, 'index']);
                Route::post('/', [DeviceController::class, 'register']);
                Route::delete('/', [DeviceController::class, 'unregister']);
            });

            // Ratings (Authenticated - Create/Update/Delete own)
            Route::prefix('ratings')->group(function () {
                Route::post('/', [RatingController::class, 'store'])
                    ->middleware(CheckPermission::class . ':ratings.create');
                Route::put('/{rating}', [RatingController::class, 'update'])
                    ->middleware(CheckPermission::class . ':ratings.update');
                Route::delete('/{rating}', [RatingController::class, 'destroy'])
                    ->middleware(CheckPermission::class . ':ratings.delete');
            });

            // Support Tickets (own tickets)
            Route::prefix('support-tickets')->group(function () {
                Route::get('/', [SupportTicketController::class, 'index'])
                    ->middleware(CheckPermission::class . ':support-tickets.view');
                Route::post('/', [SupportTicketController::class, 'store'])
                    ->middleware(CheckPermission::class . ':support-tickets.create');
                Route::get('/{supportTicket}', [SupportTicketController::class, 'show'])
                    ->middleware(CheckPermission::class . ':support-tickets.view');
                Route::post('/{supportTicket}/reply', [SupportTicketController::class, 'reply'])
                    ->middleware(CheckPermission::class . ':support-tickets.reply');
                Route::put('/{supportTicket}/close', [SupportTicketController::class, 'close'])
                    ->middleware(CheckPermission::class . ':support-tickets.close');
                Route::put('/{supportTicket}/reopen', [SupportTicketController::class, 'reopen'])
                    ->middleware(CheckPermission::class . ':support-tickets.create');
            });

            // Invoices (own invoices)
            Route::prefix('invoices')->group(function () {
                Route::get('/', [InvoiceController::class, 'index'])
                    ->middleware(CheckPermission::class . ':invoices.view');
                Route::get('/{invoice}', [InvoiceController::class, 'show'])
                    ->middleware(CheckPermission::class . ':invoices.view');
            });

            // Payment Gateway (Tap)
            Route::prefix('payments')->group(function () {
                Route::get('/', [PaymentGatewayController::class, 'index'])
                    ->middleware(CheckPermission::class . ':invoices.view');
                Route::get('/{payment}', [PaymentGatewayController::class, 'show'])
                    ->middleware(CheckPermission::class . ':invoices.view');
                Route::post('/pay-invoice', [PaymentGatewayController::class, 'payInvoice'])
                    ->middleware(CheckPermission::class . ':invoices.view');
                Route::get('/{payment}/status', [PaymentGatewayController::class, 'checkStatus'])
                    ->middleware(CheckPermission::class . ':invoices.view');
            });

            // Visit Requests (own requests)
            Route::prefix('visit-requests')->group(function () {
                Route::get('/', [VisitRequestController::class, 'index'])
                    ->middleware(CheckPermission::class . ':visit-requests.view');
                Route::post('/', [VisitRequestController::class, 'store'])
                    ->middleware(CheckPermission::class . ':visit-requests.create');
                Route::get('/{visitRequest}', [VisitRequestController::class, 'show'])
                    ->middleware(CheckPermission::class . ':visit-requests.view');
                Route::put('/{visitRequest}', [VisitRequestController::class, 'update'])
                    ->middleware(CheckPermission::class . ':visit-requests.update');
                Route::delete('/{visitRequest}', [VisitRequestController::class, 'destroy'])
                    ->middleware(CheckPermission::class . ':visit-requests.delete');
            });

            // Rental Requests (own requests, requires verified profile)
            Route::prefix('rental-requests')->middleware([CheckVerifiedProfile::class])->group(function () {
                Route::get('/', [RentalRequestController::class, 'index'])
                    ->middleware(CheckPermission::class . ':rental-requests.view');
                Route::post('/', [RentalRequestController::class, 'store'])
                    ->middleware(CheckPermission::class . ':rental-requests.create');
                Route::get('/{rentalRequest}', [RentalRequestController::class, 'show'])
                    ->middleware(CheckPermission::class . ':rental-requests.view');
                Route::put('/{rentalRequest}', [RentalRequestController::class, 'update'])
                    ->middleware(CheckPermission::class . ':rental-requests.update');
                Route::delete('/{rentalRequest}', [RentalRequestController::class, 'destroy'])
                    ->middleware(CheckPermission::class . ':rental-requests.delete');
            });


            // ══════════════════════════════════════════════════════════
            // ══ MY — Owner-scoped resources ═══════════════════════════
            // ══════════════════════════════════════════════════════════
            //
            // Endpoints scoped to the current user's own data.
            // No hardcoded roles — permissions determine access.
            //
            // Examples:
            //   spaces.create → user can manage own spaces (investor)
            //   visit-requests.approve → user can handle received requests
            //   sponsor-assets.create → user can manage sponsor assets
            //
            Route::prefix('my')->group(function () {

                // ── Unified Dashboard ─────────────────────────────────
                Route::get('/dashboard', [MyDashboardController::class, 'index']);

                // ── Space Management (own spaces) ─────────────────────
                Route::prefix('spaces')->group(function () {
                    Route::get('/', [InvestorSpaceController::class, 'index'])
                        ->middleware(CheckPermission::class . ':spaces.view');
                    Route::post('/', [InvestorSpaceController::class, 'store'])
                        ->middleware(CheckPermission::class . ':spaces.create');
                    Route::get('/{space}', [InvestorSpaceController::class, 'show'])
                        ->middleware(CheckPermission::class . ':spaces.view');
                    Route::put('/{space}', [InvestorSpaceController::class, 'update'])
                        ->middleware(CheckPermission::class . ':spaces.update');
                    Route::delete('/{space}', [InvestorSpaceController::class, 'destroy'])
                        ->middleware(CheckPermission::class . ':spaces.delete');
                    Route::post('/{space}/services', [InvestorSpaceController::class, 'addServices'])
                        ->middleware(CheckPermission::class . ':spaces.update');
                    Route::delete('/{space}/services', [InvestorSpaceController::class, 'removeServices'])
                        ->middleware(CheckPermission::class . ':spaces.update');
                });

                // ── Received Visit Requests (for user's spaces) ──────
                Route::prefix('received-visit-requests')->group(function () {
                    Route::get('/', [InvestorVisitRequestController::class, 'index'])
                        ->middleware(CheckPermission::class . ':visit-requests.approve');
                    Route::get('/pending-count', [InvestorVisitRequestController::class, 'pendingCount'])
                        ->middleware(CheckPermission::class . ':visit-requests.approve');
                    Route::get('/{visitRequest}', [InvestorVisitRequestController::class, 'show'])
                        ->middleware(CheckPermission::class . ':visit-requests.approve');
                    Route::put('/{visitRequest}/approve', [InvestorVisitRequestController::class, 'approve'])
                        ->middleware(CheckPermission::class . ':visit-requests.approve');
                    Route::put('/{visitRequest}/reject', [InvestorVisitRequestController::class, 'reject'])
                        ->middleware(CheckPermission::class . ':visit-requests.reject');
                });

                // ── Received Rental Requests (for user's spaces) ─────
                Route::prefix('received-rental-requests')->group(function () {
                    Route::get('/', [InvestorRentalRequestController::class, 'index'])
                        ->middleware(CheckPermission::class . ':rental-requests.approve');
                    Route::get('/pending-count', [InvestorRentalRequestController::class, 'pendingCount'])
                        ->middleware(CheckPermission::class . ':rental-requests.approve');
                    Route::get('/{rentalRequest}', [InvestorRentalRequestController::class, 'show'])
                        ->middleware(CheckPermission::class . ':rental-requests.approve');
                    Route::put('/{rentalRequest}/approve', [InvestorRentalRequestController::class, 'approve'])
                        ->middleware(CheckPermission::class . ':rental-requests.approve');
                    Route::put('/{rentalRequest}/reject', [InvestorRentalRequestController::class, 'reject'])
                        ->middleware(CheckPermission::class . ':rental-requests.reject');
                });

                // ── Payments (own revenue/payments) ──────────────────
                Route::prefix('payments')->group(function () {
                    Route::get('/', [InvestorPaymentController::class, 'index'])
                        ->middleware(CheckPermission::class . ':payments.view');
                    Route::get('/summary', [InvestorPaymentController::class, 'summary'])
                        ->middleware(CheckPermission::class . ':payments.view');
                    Route::get('/{rentalRequest}', [InvestorPaymentController::class, 'show'])
                        ->middleware(CheckPermission::class . ':payments.view');
                });

                // ── Rental Contracts (own — view & sign) ─────────────
                Route::prefix('rental-contracts')->group(function () {
                    Route::get('/', [AdminRentalContractController::class, 'index'])
                        ->middleware(CheckPermission::class . ':rental-contracts.view');
                    Route::get('/{rentalContract}', [AdminRentalContractController::class, 'show'])
                        ->middleware(CheckPermission::class . ':rental-contracts.view');
                    Route::put('/{rentalContract}/sign', [AdminRentalContractController::class, 'sign'])
                        ->middleware(CheckPermission::class . ':rental-contracts.sign');
                });

                // ── Sponsor Contracts (own — read-only) ──────────────
                Route::prefix('sponsor-contracts')->group(function () {
                    Route::get('/', [SponsorContractController::class, 'index'])
                        ->middleware(CheckPermission::class . ':sponsor-contracts.view');
                    Route::get('/{sponsorContract}', [SponsorContractController::class, 'show'])
                        ->middleware(CheckPermission::class . ':sponsor-contracts.view');
                });

                // ── Sponsor Payments (own — read-only) ───────────────
                Route::prefix('sponsor-payments')->group(function () {
                    Route::get('/', [SponsorPaymentController::class, 'index'])
                        ->middleware(CheckPermission::class . ':sponsor-payments.view');
                    Route::get('/{sponsorPayment}', [SponsorPaymentController::class, 'show'])
                        ->middleware(CheckPermission::class . ':sponsor-payments.view');
                });

                // ── Sponsor Assets (own — full CRUD) ─────────────────
                Route::prefix('sponsor-assets')->group(function () {
                    Route::get('/', [SponsorAssetController::class, 'index'])
                        ->middleware(CheckPermission::class . ':sponsor-assets.view');
                    Route::post('/', [SponsorAssetController::class, 'store'])
                        ->middleware(CheckPermission::class . ':sponsor-assets.create');
                    Route::get('/{sponsorAsset}', [SponsorAssetController::class, 'show'])
                        ->middleware(CheckPermission::class . ':sponsor-assets.view');
                    Route::put('/{sponsorAsset}', [SponsorAssetController::class, 'update'])
                        ->middleware(CheckPermission::class . ':sponsor-assets.update');
                    Route::delete('/{sponsorAsset}', [SponsorAssetController::class, 'destroy'])
                        ->middleware(CheckPermission::class . ':sponsor-assets.delete');
                });

                // ── Sponsor Exposure/ROI (own — read-only) ───────────
                Route::prefix('sponsor-exposure')->group(function () {
                    Route::get('/', [SponsorExposureController::class, 'index'])
                        ->middleware(CheckPermission::class . ':sponsor-exposure.view');
                    Route::get('/summary', [SponsorExposureController::class, 'summary'])
                        ->middleware(CheckPermission::class . ':sponsor-exposure.view');
                });

                // ── My Activity (own activity history) ───────────────
                Route::prefix('activity')->group(function () {
                    Route::get('/', [MyActivityController::class, 'index']);
                    Route::get('/summary', [MyActivityController::class, 'summary']);
                });

            }); // End /my/


            // ══════════════════════════════════════════════════════════
            // ══ MANAGE — Management Operations ═══════════════════════
            // ══════════════════════════════════════════════════════════
            //
            // All management routes use check.permission per route.
            // Access is controlled by permissions assigned to roles.
            //
            // admin role         → full CRUD permissions → full access
            // supervisor role    → view + approve permissions → review access
            // super-admin role   → bypasses all checks (built into middleware)
            // custom roles       → just assign the right permissions!
            //
            Route::prefix('manage')->group(function () {

                // ── Dashboard & Statistics ────────────────────────────
                Route::get('/dashboard', [AdminDashboardController::class, 'index'])
                    ->middleware(CheckPermission::class . ':reports.view');
                Route::get('/statistics', [AdminStatisticsController::class, 'index'])
                    ->middleware(CheckPermission::class . ':reports.view');

                // ── Events ───────────────────────────────────────────
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

                    // Nested: Sections for event
                    Route::get('/{event}/sections', [AdminSectionController::class, 'index'])
                        ->middleware(CheckPermission::class . ':sections.view');
                    Route::post('/{event}/sections', [AdminSectionController::class, 'store'])
                        ->middleware(CheckPermission::class . ':sections.create');

                    // Nested: Spaces for event
                    Route::get('/{event}/spaces', [AdminSpaceController::class, 'index'])
                        ->middleware(CheckPermission::class . ':spaces.view');
                    Route::post('/{event}/spaces', [AdminSpaceController::class, 'store'])
                        ->middleware(CheckPermission::class . ':spaces.create');

                    // Nested: Sponsor packages for event
                    Route::get('/{event}/sponsor-packages', [AdminSponsorPackageController::class, 'index'])
                        ->middleware(CheckPermission::class . ':sponsor-packages.view');
                    Route::post('/{event}/sponsor-packages', [AdminSponsorPackageController::class, 'store'])
                        ->middleware(CheckPermission::class . ':sponsor-packages.create');
                });

                // ── Sections ─────────────────────────────────────────
                Route::prefix('sections')->group(function () {
                    Route::get('/{section}', [AdminSectionController::class, 'show'])
                        ->middleware(CheckPermission::class . ':sections.view');
                    Route::put('/{section}', [AdminSectionController::class, 'update'])
                        ->middleware(CheckPermission::class . ':sections.update');
                    Route::delete('/{section}', [AdminSectionController::class, 'destroy'])
                        ->middleware(CheckPermission::class . ':sections.delete');
                });

                // ── Spaces ───────────────────────────────────────────
                Route::prefix('spaces')->group(function () {
                    Route::get('/{space}', [AdminSpaceController::class, 'show'])
                        ->middleware(CheckPermission::class . ':spaces.view');
                    Route::put('/{space}', [AdminSpaceController::class, 'update'])
                        ->middleware(CheckPermission::class . ':spaces.update');
                    Route::delete('/{space}', [AdminSpaceController::class, 'destroy'])
                        ->middleware(CheckPermission::class . ':spaces.delete');
                });

                // ── Services ─────────────────────────────────────────
                Route::prefix('services')->group(function () {
                    Route::get('/', [AdminServiceController::class, 'index'])
                        ->middleware(CheckPermission::class . ':expo-services.view');
                    Route::post('/', [AdminServiceController::class, 'store'])
                        ->middleware(CheckPermission::class . ':expo-services.create');
                    Route::get('/{service}', [AdminServiceController::class, 'show'])
                        ->middleware(CheckPermission::class . ':expo-services.view');
                    Route::put('/{service}', [AdminServiceController::class, 'update'])
                        ->middleware(CheckPermission::class . ':expo-services.update');
                    Route::delete('/{service}', [AdminServiceController::class, 'destroy'])
                        ->middleware(CheckPermission::class . ':expo-services.delete');
                });

                // ── Categories ───────────────────────────────────────
                Route::prefix('categories')->group(function () {
                    Route::get('/', [ManageCategoryController::class, 'index'])
                        ->middleware(CheckPermission::class . ':categories.view');
                    Route::post('/', [ManageCategoryController::class, 'store'])
                        ->middleware(CheckPermission::class . ':categories.create');
                    Route::get('/{category}', [ManageCategoryController::class, 'show'])
                        ->middleware(CheckPermission::class . ':categories.view');
                    Route::put('/{category}', [ManageCategoryController::class, 'update'])
                        ->middleware(CheckPermission::class . ':categories.update');
                    Route::delete('/{category}', [ManageCategoryController::class, 'destroy'])
                        ->middleware(CheckPermission::class . ':categories.delete');
                });

                // ── Cities ───────────────────────────────────────────
                Route::prefix('cities')->group(function () {
                    Route::get('/', [ManageCityController::class, 'index'])
                        ->middleware(CheckPermission::class . ':cities.view');
                    Route::post('/', [ManageCityController::class, 'store'])
                        ->middleware(CheckPermission::class . ':cities.create');
                    Route::get('/{city}', [ManageCityController::class, 'show'])
                        ->middleware(CheckPermission::class . ':cities.view');
                    Route::put('/{city}', [ManageCityController::class, 'update'])
                        ->middleware(CheckPermission::class . ':cities.update');
                    Route::delete('/{city}', [ManageCityController::class, 'destroy'])
                        ->middleware(CheckPermission::class . ':cities.delete');
                });

                // ── System Settings ──────────────────────────────────
                Route::prefix('settings')->group(function () {
                    Route::get('/', [ManageSettingController::class, 'index'])
                        ->middleware(CheckPermission::class . ':settings.view');
                    Route::get('/{key}', [ManageSettingController::class, 'show'])
                        ->middleware(CheckPermission::class . ':settings.view');
                    Route::put('/', [ManageSettingController::class, 'update'])
                        ->middleware(CheckPermission::class . ':settings.update');
                });

                // ── Users / Profiles Management ──────────────────────
                Route::prefix('users')->group(function () {
                    Route::get('/', [ManageUserController::class, 'index'])
                        ->middleware(CheckPermission::class . ':profiles.view-all');
                    Route::get('/{profile}', [ManageUserController::class, 'show'])
                        ->middleware(CheckPermission::class . ':profiles.view-all');
                    Route::put('/{profile}/approve', [ManageUserController::class, 'approve'])
                        ->middleware(CheckPermission::class . ':profiles.approve');
                    Route::put('/{profile}/reject', [ManageUserController::class, 'reject'])
                        ->middleware(CheckPermission::class . ':profiles.reject');
                    Route::put('/{profile}/suspend', [ManageUserController::class, 'suspend'])
                        ->middleware(CheckPermission::class . ':profiles.reject');
                });

                // ── Business Profiles ────────────────────────────────
                Route::prefix('profiles')->group(function () {
                    Route::get('/', [AdminBusinessProfileController::class, 'index'])
                        ->middleware(CheckPermission::class . ':profiles.view-all');
                    Route::get('/{profile}', [AdminBusinessProfileController::class, 'show'])
                        ->middleware(CheckPermission::class . ':profiles.view-all');
                    Route::put('/{profile}/approve', [AdminBusinessProfileController::class, 'approve'])
                        ->middleware(CheckPermission::class . ':profiles.approve');
                    Route::put('/{profile}/reject', [AdminBusinessProfileController::class, 'reject'])
                        ->middleware(CheckPermission::class . ':profiles.reject');
                });

                // ── Visit Requests ───────────────────────────────────
                Route::prefix('visit-requests')->group(function () {
                    Route::get('/', [AdminVisitRequestController::class, 'index'])
                        ->middleware(CheckPermission::class . ':visit-requests.view-all');
                    Route::get('/{visitRequest}', [AdminVisitRequestController::class, 'show'])
                        ->middleware(CheckPermission::class . ':visit-requests.view-all');
                    Route::put('/{visitRequest}/approve', [AdminVisitRequestController::class, 'approve'])
                        ->middleware(CheckPermission::class . ':visit-requests.approve');
                    Route::put('/{visitRequest}/reject', [AdminVisitRequestController::class, 'reject'])
                        ->middleware(CheckPermission::class . ':visit-requests.reject');
                });

                // ── Rental Requests ──────────────────────────────────
                Route::prefix('rental-requests')->group(function () {
                    Route::get('/', [AdminRentalRequestController::class, 'index'])
                        ->middleware(CheckPermission::class . ':rental-requests.view-all');
                    Route::get('/{rentalRequest}', [AdminRentalRequestController::class, 'show'])
                        ->middleware(CheckPermission::class . ':rental-requests.view-all');
                    Route::put('/{rentalRequest}/approve', [AdminRentalRequestController::class, 'approve'])
                        ->middleware(CheckPermission::class . ':rental-requests.approve');
                    Route::put('/{rentalRequest}/reject', [AdminRentalRequestController::class, 'reject'])
                        ->middleware(CheckPermission::class . ':rental-requests.reject');
                    Route::post('/{rentalRequest}/payment', [AdminRentalRequestController::class, 'recordPayment'])
                        ->middleware(CheckPermission::class . ':rental-requests.record-payment');
                });

                // ── Rental Contracts ─────────────────────────────────
                Route::prefix('rental-contracts')->group(function () {
                    Route::get('/', [AdminRentalContractController::class, 'index'])
                        ->middleware(CheckPermission::class . ':rental-contracts.view-all');
                    Route::post('/', [AdminRentalContractController::class, 'store'])
                        ->middleware(CheckPermission::class . ':rental-contracts.create');
                    Route::get('/{rentalContract}', [AdminRentalContractController::class, 'show'])
                        ->middleware(CheckPermission::class . ':rental-contracts.view-all');
                    Route::put('/{rentalContract}', [AdminRentalContractController::class, 'update'])
                        ->middleware(CheckPermission::class . ':rental-contracts.update');
                    Route::put('/{rentalContract}/approve', [AdminRentalContractController::class, 'approve'])
                        ->middleware(CheckPermission::class . ':rental-contracts.approve');
                    Route::put('/{rentalContract}/reject', [AdminRentalContractController::class, 'reject'])
                        ->middleware(CheckPermission::class . ':rental-contracts.reject');
                    Route::put('/{rentalContract}/terminate', [AdminRentalContractController::class, 'terminate'])
                        ->middleware(CheckPermission::class . ':rental-contracts.terminate');
                });

                // ── Sponsors ─────────────────────────────────────────
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
                    Route::put('/{sponsor}/activate', [AdminSponsorController::class, 'activate'])
                        ->middleware(CheckPermission::class . ':sponsors.approve');
                    Route::put('/{sponsor}/suspend', [AdminSponsorController::class, 'suspend'])
                        ->middleware(CheckPermission::class . ':sponsors.reject');
                });

                // ── Sponsor Packages ─────────────────────────────────
                Route::prefix('sponsor-packages')->group(function () {
                    Route::get('/{sponsorPackage}', [AdminSponsorPackageController::class, 'show'])
                        ->middleware(CheckPermission::class . ':sponsor-packages.view');
                    Route::put('/{sponsorPackage}', [AdminSponsorPackageController::class, 'update'])
                        ->middleware(CheckPermission::class . ':sponsor-packages.update');
                    Route::delete('/{sponsorPackage}', [AdminSponsorPackageController::class, 'destroy'])
                        ->middleware(CheckPermission::class . ':sponsor-packages.delete');
                });

                // ── Sponsor Contracts ────────────────────────────────
                Route::prefix('sponsor-contracts')->group(function () {
                    Route::get('/', [AdminSponsorContractController::class, 'index'])
                        ->middleware(CheckPermission::class . ':sponsor-contracts.view-all');
                    Route::post('/', [AdminSponsorContractController::class, 'store'])
                        ->middleware(CheckPermission::class . ':sponsor-contracts.create');
                    Route::get('/{sponsorContract}', [AdminSponsorContractController::class, 'show'])
                        ->middleware(CheckPermission::class . ':sponsor-contracts.view-all');
                    Route::put('/{sponsorContract}', [AdminSponsorContractController::class, 'update'])
                        ->middleware(CheckPermission::class . ':sponsor-contracts.update');
                    Route::put('/{sponsorContract}/approve', [AdminSponsorContractController::class, 'approve'])
                        ->middleware(CheckPermission::class . ':sponsor-contracts.approve');
                    Route::put('/{sponsorContract}/reject', [AdminSponsorContractController::class, 'reject'])
                        ->middleware(CheckPermission::class . ':sponsor-contracts.reject');
                    Route::put('/{sponsorContract}/complete', [AdminSponsorContractController::class, 'complete'])
                        ->middleware(CheckPermission::class . ':sponsor-contracts.approve');
                });

                // ── Sponsor Payments ─────────────────────────────────
                Route::prefix('sponsor-payments')->group(function () {
                    Route::get('/', [AdminSponsorPaymentController::class, 'index'])
                        ->middleware(CheckPermission::class . ':sponsor-payments.view-all');
                    Route::post('/', [AdminSponsorPaymentController::class, 'store'])
                        ->middleware(CheckPermission::class . ':sponsor-payments.create');
                    Route::get('/{sponsorPayment}', [AdminSponsorPaymentController::class, 'show'])
                        ->middleware(CheckPermission::class . ':sponsor-payments.view-all');
                    Route::put('/{sponsorPayment}', [AdminSponsorPaymentController::class, 'update'])
                        ->middleware(CheckPermission::class . ':sponsor-payments.create');
                    Route::put('/{sponsorPayment}/mark-paid', [AdminSponsorPaymentController::class, 'markPaid'])
                        ->middleware(CheckPermission::class . ':sponsor-payments.create');
                });

                // ── Sponsor Benefits ─────────────────────────────────
                Route::prefix('sponsor-benefits')->group(function () {
                    Route::get('/', [AdminSponsorBenefitController::class, 'index'])
                        ->middleware(CheckPermission::class . ':sponsor-benefits.view');
                    Route::post('/', [AdminSponsorBenefitController::class, 'store'])
                        ->middleware(CheckPermission::class . ':sponsor-benefits.create');
                    Route::get('/{sponsorBenefit}', [AdminSponsorBenefitController::class, 'show'])
                        ->middleware(CheckPermission::class . ':sponsor-benefits.view');
                    Route::put('/{sponsorBenefit}', [AdminSponsorBenefitController::class, 'update'])
                        ->middleware(CheckPermission::class . ':sponsor-benefits.update');
                    Route::put('/{sponsorBenefit}/deliver', [AdminSponsorBenefitController::class, 'markDelivered'])
                        ->middleware(CheckPermission::class . ':sponsor-benefits.deliver');
                });

                // ── Sponsor Assets ───────────────────────────────────
                Route::prefix('sponsor-assets')->group(function () {
                    Route::get('/', [AdminSponsorAssetController::class, 'index'])
                        ->middleware(CheckPermission::class . ':sponsor-assets.view');
                    Route::get('/{sponsorAsset}', [AdminSponsorAssetController::class, 'show'])
                        ->middleware(CheckPermission::class . ':sponsor-assets.view');
                    Route::put('/{sponsorAsset}/approve', [AdminSponsorAssetController::class, 'approve'])
                        ->middleware(CheckPermission::class . ':sponsor-assets.approve');
                    Route::put('/{sponsorAsset}/reject', [AdminSponsorAssetController::class, 'reject'])
                        ->middleware(CheckPermission::class . ':sponsor-assets.approve');
                });

                // ── Ratings (management) ─────────────────────────────
                Route::prefix('ratings')->group(function () {
                    Route::get('/', [AdminRatingController::class, 'index'])
                        ->middleware(CheckPermission::class . ':ratings.view-all');
                    Route::get('/{rating}', [AdminRatingController::class, 'show'])
                        ->middleware(CheckPermission::class . ':ratings.view-all');
                    Route::put('/{rating}/approve', [AdminRatingController::class, 'approve'])
                        ->middleware(CheckPermission::class . ':ratings.approve');
                    Route::put('/{rating}/reject', [AdminRatingController::class, 'reject'])
                        ->middleware(CheckPermission::class . ':ratings.reject');
                    Route::delete('/{rating}', [AdminRatingController::class, 'destroy'])
                        ->middleware(CheckPermission::class . ':ratings.delete');
                });

                // ── Support Tickets (management) ─────────────────────
                Route::prefix('support-tickets')->group(function () {
                    Route::get('/', [AdminSupportTicketController::class, 'index'])
                        ->middleware(CheckPermission::class . ':support-tickets.view-all');
                    Route::get('/{supportTicket}', [AdminSupportTicketController::class, 'show'])
                        ->middleware(CheckPermission::class . ':support-tickets.view-all');
                    Route::put('/{supportTicket}/assign', [AdminSupportTicketController::class, 'assign'])
                        ->middleware(CheckPermission::class . ':support-tickets.assign');
                    Route::post('/{supportTicket}/reply', [AdminSupportTicketController::class, 'reply'])
                        ->middleware(CheckPermission::class . ':support-tickets.reply');
                    Route::put('/{supportTicket}/resolve', [AdminSupportTicketController::class, 'resolve'])
                        ->middleware(CheckPermission::class . ':support-tickets.close');
                    Route::put('/{supportTicket}/close', [AdminSupportTicketController::class, 'close'])
                        ->middleware(CheckPermission::class . ':support-tickets.close');
                    Route::delete('/{supportTicket}', [AdminSupportTicketController::class, 'destroy'])
                        ->middleware(CheckPermission::class . ':support-tickets.delete');
                });

                // ── Invoices (management) ────────────────────────────
                Route::prefix('invoices')->group(function () {
                    Route::get('/', [AdminInvoiceController::class, 'index'])
                        ->middleware(CheckPermission::class . ':invoices.view-all');
                    Route::post('/', [AdminInvoiceController::class, 'store'])
                        ->middleware(CheckPermission::class . ':invoices.create');
                    Route::get('/{invoice}', [AdminInvoiceController::class, 'show'])
                        ->middleware(CheckPermission::class . ':invoices.view-all');
                    Route::put('/{invoice}', [AdminInvoiceController::class, 'update'])
                        ->middleware(CheckPermission::class . ':invoices.update');
                    Route::put('/{invoice}/issue', [AdminInvoiceController::class, 'issue'])
                        ->middleware(CheckPermission::class . ':invoices.issue');
                    Route::put('/{invoice}/mark-paid', [AdminInvoiceController::class, 'markPaid'])
                        ->middleware(CheckPermission::class . ':invoices.mark-paid');
                    Route::put('/{invoice}/cancel', [AdminInvoiceController::class, 'cancel'])
                        ->middleware(CheckPermission::class . ':invoices.cancel');
                });

                // ── Pages (CMS) ──────────────────────────────────────
                Route::prefix('pages')->group(function () {
                    Route::get('/', [AdminPageController::class, 'index'])
                        ->middleware(CheckPermission::class . ':pages.view');
                    Route::post('/', [AdminPageController::class, 'store'])
                        ->middleware(CheckPermission::class . ':pages.create');
                    Route::get('/{page}', [AdminPageController::class, 'show'])
                        ->middleware(CheckPermission::class . ':pages.view');
                    Route::put('/{page}', [AdminPageController::class, 'update'])
                        ->middleware(CheckPermission::class . ':pages.update');
                    Route::delete('/{page}', [AdminPageController::class, 'destroy'])
                        ->middleware(CheckPermission::class . ':pages.delete');
                });

                // ── FAQs ─────────────────────────────────────────────
                Route::prefix('faqs')->group(function () {
                    Route::get('/', [AdminFaqController::class, 'index'])
                        ->middleware(CheckPermission::class . ':faqs.view');
                    Route::post('/', [AdminFaqController::class, 'store'])
                        ->middleware(CheckPermission::class . ':faqs.create');
                    Route::get('/{faq}', [AdminFaqController::class, 'show'])
                        ->middleware(CheckPermission::class . ':faqs.view');
                    Route::put('/{faq}', [AdminFaqController::class, 'update'])
                        ->middleware(CheckPermission::class . ':faqs.update');
                    Route::delete('/{faq}', [AdminFaqController::class, 'destroy'])
                        ->middleware(CheckPermission::class . ':faqs.delete');
                });

                // ── Banners ──────────────────────────────────────────
                Route::prefix('banners')->group(function () {
                    Route::get('/', [AdminBannerController::class, 'index'])
                        ->middleware(CheckPermission::class . ':banners.view');
                    Route::post('/', [AdminBannerController::class, 'store'])
                        ->middleware(CheckPermission::class . ':banners.create');
                    Route::get('/{banner}', [AdminBannerController::class, 'show'])
                        ->middleware(CheckPermission::class . ':banners.view');
                    Route::put('/{banner}', [AdminBannerController::class, 'update'])
                        ->middleware(CheckPermission::class . ':banners.update');
                    Route::delete('/{banner}', [AdminBannerController::class, 'destroy'])
                        ->middleware(CheckPermission::class . ':banners.delete');
                });

                // ── Analytics ─────────────────────────────────────────
                Route::prefix('analytics')->group(function () {
                    Route::get('/', [AdminAnalyticsController::class, 'index'])
                        ->middleware(CheckPermission::class . ':reports.view');
                    Route::get('/views', [AdminAnalyticsController::class, 'views'])
                        ->middleware(CheckPermission::class . ':reports.view');
                    Route::get('/actions', [AdminAnalyticsController::class, 'actions'])
                        ->middleware(CheckPermission::class . ':reports.view');
                    Route::get('/users', [AdminAnalyticsController::class, 'users'])
                        ->middleware(CheckPermission::class . ':reports.view');
                });

            }); // End /manage/

        }); // End Authenticated Routes

    }); // End v1 prefix

}); // End middleware group
