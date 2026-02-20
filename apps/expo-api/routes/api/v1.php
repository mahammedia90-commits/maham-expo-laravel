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
use App\Http\Controllers\Api\StatisticsController;
use App\Http\Controllers\Api\VisitRequestController;

// Admin Controllers
use App\Http\Controllers\Api\Admin\EventController as AdminEventController;
use App\Http\Controllers\Api\Admin\SectionController as AdminSectionController;
use App\Http\Controllers\Api\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Api\Admin\SpaceController as AdminSpaceController;
use App\Http\Controllers\Api\Admin\VisitRequestController as AdminVisitRequestController;
use App\Http\Controllers\Api\Admin\RentalRequestController as AdminRentalRequestController;
use App\Http\Controllers\Api\Admin\BusinessProfileController as AdminBusinessProfileController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\StatisticsController as AdminStatisticsController;

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

// SuperAdmin Controllers
use App\Http\Controllers\Api\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\Api\SuperAdmin\StatisticsController as SuperAdminStatisticsController;
use App\Http\Controllers\Api\SuperAdmin\CategoryController as SuperAdminCategoryController;
use App\Http\Controllers\Api\SuperAdmin\CityController as SuperAdminCityController;
use App\Http\Controllers\Api\SuperAdmin\UserController as SuperAdminUserController;
use App\Http\Controllers\Api\SuperAdmin\SettingController as SuperAdminSettingController;

use App\Http\Middleware\AuthServiceMiddleware;
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
                Route::get('/', [NotificationController::class, 'index']);
                Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
                Route::put('/{notification}/read', [NotificationController::class, 'markAsRead']);
                Route::put('/read-all', [NotificationController::class, 'markAllAsRead']);
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
            });

        }); // End Authenticated Routes

    }); // End v1 prefix

}); // End middleware group
