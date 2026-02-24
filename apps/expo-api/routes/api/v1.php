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
use App\Http\Controllers\Api\NotificationPreferenceController;

// Admin Controllers
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

// Investor Controllers
use App\Http\Controllers\Api\Investor\DashboardController as InvestorDashboardController;
use App\Http\Controllers\Api\Investor\StatisticsController as InvestorStatisticsController;
use App\Http\Controllers\Api\Investor\SpaceController as InvestorSpaceController;
use App\Http\Controllers\Api\Investor\RentalRequestController as InvestorRentalRequestController;
use App\Http\Controllers\Api\Investor\VisitRequestController as InvestorVisitRequestController;
use App\Http\Controllers\Api\Investor\PaymentController as InvestorPaymentController;

// Merchant Controllers
use App\Http\Controllers\Api\Merchant\DashboardController as MerchantDashboardController;
use App\Http\Controllers\Api\Merchant\StatisticsController as MerchantStatisticsController;
use App\Http\Controllers\Api\Merchant\EventController as MerchantEventController;
use App\Http\Controllers\Api\Merchant\SpaceController as MerchantSpaceController;
use App\Http\Controllers\Api\Merchant\ServiceController as MerchantServiceController;
use App\Http\Controllers\Api\Merchant\RentalRequestController as MerchantRentalRequestController;
use App\Http\Controllers\Api\Merchant\VisitRequestController as MerchantVisitRequestController;

// Supervisor Controllers
use App\Http\Controllers\Api\Supervisor\DashboardController as SupervisorDashboardController;
use App\Http\Controllers\Api\Supervisor\StatisticsController as SupervisorStatisticsController;
use App\Http\Controllers\Api\Supervisor\RentalRequestController as SupervisorRentalRequestController;
use App\Http\Controllers\Api\Supervisor\VisitRequestController as SupervisorVisitRequestController;

// Sponsor Controllers
use App\Http\Controllers\Api\Sponsor\DashboardController as SponsorDashboardController;
use App\Http\Controllers\Api\Sponsor\StatisticsController as SponsorStatisticsController;
use App\Http\Controllers\Api\Sponsor\ContractController as SponsorContractController;
use App\Http\Controllers\Api\Sponsor\PaymentController as SponsorPaymentController;
use App\Http\Controllers\Api\Sponsor\AssetController as SponsorAssetController;
use App\Http\Controllers\Api\Sponsor\ExposureController as SponsorExposureController;

// SuperAdmin Controllers
use App\Http\Controllers\Api\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\Api\SuperAdmin\StatisticsController as SuperAdminStatisticsController;
use App\Http\Controllers\Api\SuperAdmin\CategoryController as SuperAdminCategoryController;
use App\Http\Controllers\Api\SuperAdmin\CityController as SuperAdminCityController;
use App\Http\Controllers\Api\SuperAdmin\UserController as SuperAdminUserController;
use App\Http\Controllers\Api\SuperAdmin\SettingController as SuperAdminSettingController;

use App\Http\Middleware\AuthServiceMiddleware;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckVerifiedProfile;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes v1
|--------------------------------------------------------------------------
*/

Route::middleware([SetLocale::class, 'throttle:60,1'])->group(function () {

    // ==================== HEALTH CHECK ====================
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'service' => config('app.name'),
            'version' => config('app.version'),
            'timestamp' => now()->toISOString(),
        ]);
    });

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

        // ==================== AUTHENTICATED ROUTES ====================

        Route::middleware([AuthServiceMiddleware::class])->group(function () {

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

            // Ratings (Authenticated - Create/Update/Delete)
            Route::prefix('ratings')->group(function () {
                Route::post('/', [RatingController::class, 'store'])
                    ->middleware(CheckPermission::class . ':ratings.create');
                Route::put('/{rating}', [RatingController::class, 'update'])
                    ->middleware(CheckPermission::class . ':ratings.update');
                Route::delete('/{rating}', [RatingController::class, 'destroy'])
                    ->middleware(CheckPermission::class . ':ratings.delete');
            });

            // Support Tickets (Authenticated)
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

            // Invoices (Authenticated - own invoices)
            Route::prefix('invoices')->group(function () {
                Route::get('/', [InvoiceController::class, 'index'])
                    ->middleware(CheckPermission::class . ':invoices.view');
                Route::get('/{invoice}', [InvoiceController::class, 'show'])
                    ->middleware(CheckPermission::class . ':invoices.view');
            });

            // Visit Requests (Legacy - for backward compatibility)
            Route::prefix('visit-requests')->group(function () {
                Route::get('/', [VisitRequestController::class, 'index']);
                Route::post('/', [VisitRequestController::class, 'store']);
                Route::get('/{visitRequest}', [VisitRequestController::class, 'show']);
                Route::put('/{visitRequest}', [VisitRequestController::class, 'update']);
                Route::delete('/{visitRequest}', [VisitRequestController::class, 'destroy']);
            });

            // Rental Requests (Legacy - for backward compatibility, requires verified profile)
            Route::prefix('rental-requests')->middleware([CheckVerifiedProfile::class])->group(function () {
                Route::get('/', [RentalRequestController::class, 'index']);
                Route::post('/', [RentalRequestController::class, 'store']);
                Route::get('/{rentalRequest}', [RentalRequestController::class, 'show']);
                Route::put('/{rentalRequest}', [RentalRequestController::class, 'update']);
                Route::delete('/{rentalRequest}', [RentalRequestController::class, 'destroy']);
            });

            // ==================== SUPER ADMIN ROUTES ====================
            // Full system access including categories, cities, settings, users

            Route::prefix('super-admin')->middleware([CheckRole::class . ':super-admin'])->group(function () {

                // Dashboard (extended with system stats + analytics)
                Route::get('/dashboard', [SuperAdminDashboardController::class, 'index']);

                // Statistics
                Route::get('/statistics', [SuperAdminStatisticsController::class, 'index']);

                // Categories Management
                Route::prefix('categories')->group(function () {
                    Route::get('/', [SuperAdminCategoryController::class, 'index']);
                    Route::post('/', [SuperAdminCategoryController::class, 'store']);
                    Route::get('/{category}', [SuperAdminCategoryController::class, 'show']);
                    Route::put('/{category}', [SuperAdminCategoryController::class, 'update']);
                    Route::delete('/{category}', [SuperAdminCategoryController::class, 'destroy']);
                });

                // Cities Management
                Route::prefix('cities')->group(function () {
                    Route::get('/', [SuperAdminCityController::class, 'index']);
                    Route::post('/', [SuperAdminCityController::class, 'store']);
                    Route::get('/{city}', [SuperAdminCityController::class, 'show']);
                    Route::put('/{city}', [SuperAdminCityController::class, 'update']);
                    Route::delete('/{city}', [SuperAdminCityController::class, 'destroy']);
                });

                // Users/Profiles Management
                Route::prefix('users')->group(function () {
                    Route::get('/', [SuperAdminUserController::class, 'index']);
                    Route::get('/{profile}', [SuperAdminUserController::class, 'show']);
                    Route::put('/{profile}/approve', [SuperAdminUserController::class, 'approve']);
                    Route::put('/{profile}/reject', [SuperAdminUserController::class, 'reject']);
                    Route::put('/{profile}/suspend', [SuperAdminUserController::class, 'suspend']);
                });

                // System Settings
                Route::prefix('settings')->group(function () {
                    Route::get('/', [SuperAdminSettingController::class, 'index']);
                    Route::get('/{key}', [SuperAdminSettingController::class, 'show']);
                    Route::put('/', [SuperAdminSettingController::class, 'update']);
                });
            });

            // ==================== ADMIN ROUTES ====================
            // Full access to all CRUD operations

            Route::prefix('admin')->middleware([CheckRole::class . ':admin,super-admin'])->group(function () {

                // Dashboard Statistics
                Route::get('/dashboard', [AdminDashboardController::class, 'index']);

                // Statistics
                Route::get('/statistics', [AdminStatisticsController::class, 'index']);

                // Events Management
                Route::prefix('events')->group(function () {
                    Route::get('/', [AdminEventController::class, 'index']);
                    Route::post('/', [AdminEventController::class, 'store']);
                    Route::get('/{event}', [AdminEventController::class, 'show']);
                    Route::put('/{event}', [AdminEventController::class, 'update']);
                    Route::delete('/{event}', [AdminEventController::class, 'destroy']);

                    // Sections for event
                    Route::get('/{event}/sections', [AdminSectionController::class, 'index']);
                    Route::post('/{event}/sections', [AdminSectionController::class, 'store']);

                    // Spaces for event
                    Route::get('/{event}/spaces', [AdminSpaceController::class, 'index']);
                    Route::post('/{event}/spaces', [AdminSpaceController::class, 'store']);
                });

                // Sections Management
                Route::prefix('sections')->group(function () {
                    Route::get('/{section}', [AdminSectionController::class, 'show']);
                    Route::put('/{section}', [AdminSectionController::class, 'update']);
                    Route::delete('/{section}', [AdminSectionController::class, 'destroy']);
                });

                // Spaces Management
                Route::prefix('spaces')->group(function () {
                    Route::get('/{space}', [AdminSpaceController::class, 'show']);
                    Route::put('/{space}', [AdminSpaceController::class, 'update']);
                    Route::delete('/{space}', [AdminSpaceController::class, 'destroy']);
                });

                // Services Management
                Route::prefix('services')->group(function () {
                    Route::get('/', [AdminServiceController::class, 'index']);
                    Route::post('/', [AdminServiceController::class, 'store']);
                    Route::get('/{service}', [AdminServiceController::class, 'show']);
                    Route::put('/{service}', [AdminServiceController::class, 'update']);
                    Route::delete('/{service}', [AdminServiceController::class, 'destroy']);
                });

                // Visit Requests Management
                Route::prefix('visit-requests')->group(function () {
                    Route::get('/', [AdminVisitRequestController::class, 'index']);
                    Route::get('/{visitRequest}', [AdminVisitRequestController::class, 'show']);
                    Route::put('/{visitRequest}/approve', [AdminVisitRequestController::class, 'approve']);
                    Route::put('/{visitRequest}/reject', [AdminVisitRequestController::class, 'reject']);
                });

                // Rental Requests Management
                Route::prefix('rental-requests')->group(function () {
                    Route::get('/', [AdminRentalRequestController::class, 'index']);
                    Route::get('/{rentalRequest}', [AdminRentalRequestController::class, 'show']);
                    Route::put('/{rentalRequest}/approve', [AdminRentalRequestController::class, 'approve']);
                    Route::put('/{rentalRequest}/reject', [AdminRentalRequestController::class, 'reject']);
                    Route::post('/{rentalRequest}/payment', [AdminRentalRequestController::class, 'recordPayment']);
                });

                // Business Profiles Management
                Route::prefix('profiles')->group(function () {
                    Route::get('/', [AdminBusinessProfileController::class, 'index']);
                    Route::get('/{profile}', [AdminBusinessProfileController::class, 'show']);
                    Route::put('/{profile}/approve', [AdminBusinessProfileController::class, 'approve']);
                    Route::put('/{profile}/reject', [AdminBusinessProfileController::class, 'reject']);
                });

                // Sponsors Management
                Route::prefix('sponsors')->group(function () {
                    Route::get('/', [AdminSponsorController::class, 'index']);
                    Route::post('/', [AdminSponsorController::class, 'store']);
                    Route::get('/{sponsor}', [AdminSponsorController::class, 'show']);
                    Route::put('/{sponsor}', [AdminSponsorController::class, 'update']);
                    Route::delete('/{sponsor}', [AdminSponsorController::class, 'destroy']);
                    Route::put('/{sponsor}/approve', [AdminSponsorController::class, 'approve']);
                    Route::put('/{sponsor}/activate', [AdminSponsorController::class, 'activate']);
                    Route::put('/{sponsor}/suspend', [AdminSponsorController::class, 'suspend']);
                });

                // Sponsor Packages Management
                Route::get('/events/{event}/sponsor-packages', [AdminSponsorPackageController::class, 'index']);
                Route::post('/events/{event}/sponsor-packages', [AdminSponsorPackageController::class, 'store']);
                Route::prefix('sponsor-packages')->group(function () {
                    Route::get('/{sponsorPackage}', [AdminSponsorPackageController::class, 'show']);
                    Route::put('/{sponsorPackage}', [AdminSponsorPackageController::class, 'update']);
                    Route::delete('/{sponsorPackage}', [AdminSponsorPackageController::class, 'destroy']);
                });

                // Sponsor Contracts Management
                Route::prefix('sponsor-contracts')->group(function () {
                    Route::get('/', [AdminSponsorContractController::class, 'index']);
                    Route::post('/', [AdminSponsorContractController::class, 'store']);
                    Route::get('/{sponsorContract}', [AdminSponsorContractController::class, 'show']);
                    Route::put('/{sponsorContract}', [AdminSponsorContractController::class, 'update']);
                    Route::put('/{sponsorContract}/approve', [AdminSponsorContractController::class, 'approve']);
                    Route::put('/{sponsorContract}/reject', [AdminSponsorContractController::class, 'reject']);
                    Route::put('/{sponsorContract}/complete', [AdminSponsorContractController::class, 'complete']);
                });

                // Sponsor Payments Management
                Route::prefix('sponsor-payments')->group(function () {
                    Route::get('/', [AdminSponsorPaymentController::class, 'index']);
                    Route::post('/', [AdminSponsorPaymentController::class, 'store']);
                    Route::get('/{sponsorPayment}', [AdminSponsorPaymentController::class, 'show']);
                    Route::put('/{sponsorPayment}', [AdminSponsorPaymentController::class, 'update']);
                    Route::put('/{sponsorPayment}/mark-paid', [AdminSponsorPaymentController::class, 'markPaid']);
                });

                // Sponsor Benefits Management
                Route::prefix('sponsor-benefits')->group(function () {
                    Route::get('/', [AdminSponsorBenefitController::class, 'index']);
                    Route::post('/', [AdminSponsorBenefitController::class, 'store']);
                    Route::get('/{sponsorBenefit}', [AdminSponsorBenefitController::class, 'show']);
                    Route::put('/{sponsorBenefit}', [AdminSponsorBenefitController::class, 'update']);
                    Route::put('/{sponsorBenefit}/deliver', [AdminSponsorBenefitController::class, 'markDelivered']);
                });

                // Sponsor Assets Management
                Route::prefix('sponsor-assets')->group(function () {
                    Route::get('/', [AdminSponsorAssetController::class, 'index']);
                    Route::get('/{sponsorAsset}', [AdminSponsorAssetController::class, 'show']);
                    Route::put('/{sponsorAsset}/approve', [AdminSponsorAssetController::class, 'approve']);
                    Route::put('/{sponsorAsset}/reject', [AdminSponsorAssetController::class, 'reject']);
                });

                // Ratings Management
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

                // Support Tickets Management
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

                // Rental Contracts Management
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

                // Invoices Management
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

                // Pages Management (CMS)
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

                // FAQs Management
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

                // Banners Management
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
            });

            // ==================== SUPERVISOR ROUTES ====================
            // Same as Admin but NO delete operations
            // Can view events/spaces/sections/services (read-only)
            // Can approve/reject visit & rental requests (after investor approval)

            Route::prefix('supervisor')->middleware([CheckRole::class . ':supervisor,admin,super-admin'])->group(function () {

                // Dashboard Statistics
                Route::get('/dashboard', [SupervisorDashboardController::class, 'index']);

                // Statistics
                Route::get('/statistics', [SupervisorStatisticsController::class, 'index']);

                // Events (read-only - no create/update/delete)
                Route::prefix('events')->group(function () {
                    Route::get('/', [AdminEventController::class, 'index']);
                    Route::get('/{event}', [AdminEventController::class, 'show']);

                    // Sections for event (read-only)
                    Route::get('/{event}/sections', [AdminSectionController::class, 'index']);

                    // Spaces for event (read-only)
                    Route::get('/{event}/spaces', [AdminSpaceController::class, 'index']);
                });

                // Sections (read-only)
                Route::get('/sections/{section}', [AdminSectionController::class, 'show']);

                // Spaces (read-only)
                Route::get('/spaces/{space}', [AdminSpaceController::class, 'show']);

                // Services (read-only)
                Route::prefix('services')->group(function () {
                    Route::get('/', [AdminServiceController::class, 'index']);
                    Route::get('/{service}', [AdminServiceController::class, 'show']);
                });

                // Visit Requests (approve/reject only after investor)
                Route::prefix('visit-requests')->group(function () {
                    Route::get('/', [SupervisorVisitRequestController::class, 'index']);
                    Route::get('/{visitRequest}', [SupervisorVisitRequestController::class, 'show']);
                    Route::put('/{visitRequest}/approve', [SupervisorVisitRequestController::class, 'approve']);
                    Route::put('/{visitRequest}/reject', [SupervisorVisitRequestController::class, 'reject']);
                });

                // Rental Requests (approve/reject only after investor)
                Route::prefix('rental-requests')->group(function () {
                    Route::get('/', [SupervisorRentalRequestController::class, 'index']);
                    Route::get('/{rentalRequest}', [SupervisorRentalRequestController::class, 'show']);
                    Route::put('/{rentalRequest}/approve', [SupervisorRentalRequestController::class, 'approve']);
                    Route::put('/{rentalRequest}/reject', [SupervisorRentalRequestController::class, 'reject']);
                    Route::post('/{rentalRequest}/payment', [SupervisorRentalRequestController::class, 'recordPayment']);
                });

                // Business Profiles Management (view and approve/reject)
                Route::prefix('profiles')->group(function () {
                    Route::get('/', [AdminBusinessProfileController::class, 'index']);
                    Route::get('/{profile}', [AdminBusinessProfileController::class, 'show']);
                    Route::put('/{profile}/approve', [AdminBusinessProfileController::class, 'approve']);
                    Route::put('/{profile}/reject', [AdminBusinessProfileController::class, 'reject']);
                });

                // Sponsors (read-only + approve/reject contracts)
                Route::prefix('sponsors')->group(function () {
                    Route::get('/', [AdminSponsorController::class, 'index']);
                    Route::get('/{sponsor}', [AdminSponsorController::class, 'show']);
                });

                Route::prefix('sponsor-contracts')->group(function () {
                    Route::get('/', [AdminSponsorContractController::class, 'index']);
                    Route::get('/{sponsorContract}', [AdminSponsorContractController::class, 'show']);
                    Route::put('/{sponsorContract}/approve', [AdminSponsorContractController::class, 'approve']);
                    Route::put('/{sponsorContract}/reject', [AdminSponsorContractController::class, 'reject']);
                });

                // Support Tickets (view + resolve)
                Route::prefix('support-tickets')->group(function () {
                    Route::get('/', [AdminSupportTicketController::class, 'index'])
                        ->middleware(CheckPermission::class . ':support-tickets.view-all');
                    Route::get('/{supportTicket}', [AdminSupportTicketController::class, 'show'])
                        ->middleware(CheckPermission::class . ':support-tickets.view-all');
                    Route::post('/{supportTicket}/reply', [AdminSupportTicketController::class, 'reply'])
                        ->middleware(CheckPermission::class . ':support-tickets.reply');
                    Route::put('/{supportTicket}/resolve', [AdminSupportTicketController::class, 'resolve'])
                        ->middleware(CheckPermission::class . ':support-tickets.close');
                });

                // Rental Contracts (read-only + approve/reject)
                Route::prefix('rental-contracts')->group(function () {
                    Route::get('/', [AdminRentalContractController::class, 'index'])
                        ->middleware(CheckPermission::class . ':rental-contracts.view-all');
                    Route::get('/{rentalContract}', [AdminRentalContractController::class, 'show'])
                        ->middleware(CheckPermission::class . ':rental-contracts.view-all');
                    Route::put('/{rentalContract}/approve', [AdminRentalContractController::class, 'approve'])
                        ->middleware(CheckPermission::class . ':rental-contracts.approve');
                    Route::put('/{rentalContract}/reject', [AdminRentalContractController::class, 'reject'])
                        ->middleware(CheckPermission::class . ':rental-contracts.reject');
                });
            });

            // ==================== SPONSOR ROUTES ====================
            // Sponsor self-service: view contracts, payments, assets, exposure

            Route::prefix('sponsor')->middleware([CheckRole::class . ':sponsor'])->group(function () {

                // Dashboard
                Route::get('/dashboard', [SponsorDashboardController::class, 'index']);

                // Statistics
                Route::get('/statistics', [SponsorStatisticsController::class, 'index']);

                // Contracts (read-only)
                Route::prefix('contracts')->group(function () {
                    Route::get('/', [SponsorContractController::class, 'index']);
                    Route::get('/{sponsorContract}', [SponsorContractController::class, 'show']);
                });

                // Payments (read-only)
                Route::prefix('payments')->group(function () {
                    Route::get('/', [SponsorPaymentController::class, 'index']);
                    Route::get('/{sponsorPayment}', [SponsorPaymentController::class, 'show']);
                });

                // Assets (CRUD)
                Route::prefix('assets')->group(function () {
                    Route::get('/', [SponsorAssetController::class, 'index']);
                    Route::post('/', [SponsorAssetController::class, 'store']);
                    Route::get('/{sponsorAsset}', [SponsorAssetController::class, 'show']);
                    Route::put('/{sponsorAsset}', [SponsorAssetController::class, 'update']);
                    Route::delete('/{sponsorAsset}', [SponsorAssetController::class, 'destroy']);
                });

                // Exposure/ROI
                Route::prefix('exposure')->group(function () {
                    Route::get('/', [SponsorExposureController::class, 'index']);
                    Route::get('/summary', [SponsorExposureController::class, 'summary']);
                });
            });

            // ==================== INVESTOR ROUTES ====================
            // Manage own spaces, approve/reject requests for their spaces

            Route::prefix('investor')->middleware([CheckRole::class . ':investor'])->group(function () {

                // Dashboard
                Route::get('/dashboard', [InvestorDashboardController::class, 'index']);

                // Statistics
                Route::get('/statistics', [InvestorStatisticsController::class, 'index']);

                // Spaces Management (own spaces only)
                Route::prefix('spaces')->group(function () {
                    Route::get('/', [InvestorSpaceController::class, 'index']);
                    Route::post('/', [InvestorSpaceController::class, 'store']);
                    Route::get('/{space}', [InvestorSpaceController::class, 'show']);
                    Route::put('/{space}', [InvestorSpaceController::class, 'update']);
                    Route::delete('/{space}', [InvestorSpaceController::class, 'destroy']);

                    // Services management for space
                    Route::post('/{space}/services', [InvestorSpaceController::class, 'addServices']);
                    Route::delete('/{space}/services', [InvestorSpaceController::class, 'removeServices']);
                });

                // Rental Requests (for investor's spaces)
                Route::prefix('rental-requests')->group(function () {
                    Route::get('/', [InvestorRentalRequestController::class, 'index']);
                    Route::get('/pending-count', [InvestorRentalRequestController::class, 'pendingCount']);
                    Route::get('/{rentalRequest}', [InvestorRentalRequestController::class, 'show']);
                    Route::put('/{rentalRequest}/approve', [InvestorRentalRequestController::class, 'approve']);
                    Route::put('/{rentalRequest}/reject', [InvestorRentalRequestController::class, 'reject']);
                });

                // Visit Requests (for events with investor's spaces)
                Route::prefix('visit-requests')->group(function () {
                    Route::get('/', [InvestorVisitRequestController::class, 'index']);
                    Route::get('/pending-count', [InvestorVisitRequestController::class, 'pendingCount']);
                    Route::get('/{visitRequest}', [InvestorVisitRequestController::class, 'show']);
                    Route::put('/{visitRequest}/approve', [InvestorVisitRequestController::class, 'approve']);
                    Route::put('/{visitRequest}/reject', [InvestorVisitRequestController::class, 'reject']);
                });

                // Payments/Revenue
                Route::prefix('payments')->group(function () {
                    Route::get('/', [InvestorPaymentController::class, 'index']);
                    Route::get('/summary', [InvestorPaymentController::class, 'summary']);
                    Route::get('/{rentalRequest}', [InvestorPaymentController::class, 'show']);
                });

                // Rental Contracts (investor signs their own contracts)
                Route::prefix('rental-contracts')->group(function () {
                    Route::get('/', [AdminRentalContractController::class, 'index'])
                        ->middleware(CheckPermission::class . ':rental-contracts.view');
                    Route::get('/{rentalContract}', [AdminRentalContractController::class, 'show'])
                        ->middleware(CheckPermission::class . ':rental-contracts.view');
                    Route::put('/{rentalContract}/sign', [AdminRentalContractController::class, 'signByInvestor'])
                        ->middleware(CheckPermission::class . ':rental-contracts.sign');
                });
            });

            // ==================== MERCHANT ROUTES ====================
            // View events/spaces/services, create visit/rental requests

            Route::prefix('merchant')->middleware([CheckRole::class . ':merchant'])->group(function () {

                // Dashboard
                Route::get('/dashboard', [MerchantDashboardController::class, 'index']);

                // Statistics
                Route::get('/statistics', [MerchantStatisticsController::class, 'index']);

                // Browse Events (view only)
                Route::prefix('events')->group(function () {
                    Route::get('/', [MerchantEventController::class, 'index']);
                    Route::get('/{event}', [MerchantEventController::class, 'show']);
                    Route::get('/{event}/sections', [MerchantEventController::class, 'sections']);
                    Route::get('/{event}/spaces', [MerchantEventController::class, 'spaces']);
                });

                // Browse Spaces (view only)
                Route::prefix('spaces')->group(function () {
                    Route::get('/', [MerchantSpaceController::class, 'index']);
                    Route::get('/{space}', [MerchantSpaceController::class, 'show']);
                });

                // Browse Services (view only)
                Route::prefix('services')->group(function () {
                    Route::get('/', [MerchantServiceController::class, 'index']);
                    Route::get('/{service}', [MerchantServiceController::class, 'show']);
                });

                // Visit Requests (own requests only)
                Route::prefix('visit-requests')->group(function () {
                    Route::get('/', [MerchantVisitRequestController::class, 'index']);
                    Route::post('/', [MerchantVisitRequestController::class, 'store']);
                    Route::get('/{visitRequest}', [MerchantVisitRequestController::class, 'show']);
                    Route::put('/{visitRequest}', [MerchantVisitRequestController::class, 'update']);
                    Route::delete('/{visitRequest}', [MerchantVisitRequestController::class, 'destroy']);
                });

                // Rental Requests (own requests only, requires verified profile)
                Route::prefix('rental-requests')->middleware([CheckVerifiedProfile::class])->group(function () {
                    Route::get('/', [MerchantRentalRequestController::class, 'index']);
                    Route::post('/', [MerchantRentalRequestController::class, 'store']);
                    Route::get('/{rentalRequest}', [MerchantRentalRequestController::class, 'show']);
                    Route::put('/{rentalRequest}', [MerchantRentalRequestController::class, 'update']);
                    Route::delete('/{rentalRequest}', [MerchantRentalRequestController::class, 'destroy']);
                });

                // Rental Contracts (merchant signs their own contracts)
                Route::prefix('rental-contracts')->group(function () {
                    Route::get('/', [AdminRentalContractController::class, 'index'])
                        ->middleware(CheckPermission::class . ':rental-contracts.view');
                    Route::get('/{rentalContract}', [AdminRentalContractController::class, 'show'])
                        ->middleware(CheckPermission::class . ':rental-contracts.view');
                    Route::put('/{rentalContract}/sign', [AdminRentalContractController::class, 'signByMerchant'])
                        ->middleware(CheckPermission::class . ':rental-contracts.sign');
                });
            });

        }); // End Authenticated Routes

    }); // End v1 prefix

}); // End middleware group
