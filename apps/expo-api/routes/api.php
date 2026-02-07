<?php

use App\Http\Controllers\Api\BusinessProfileController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\RentalRequestController;
use App\Http\Controllers\Api\SpaceController;
use App\Http\Controllers\Api\VisitRequestController;
use App\Http\Controllers\Api\Admin\EventController as AdminEventController;
use App\Http\Controllers\Api\Admin\SpaceController as AdminSpaceController;
use App\Http\Controllers\Api\Admin\VisitRequestController as AdminVisitRequestController;
use App\Http\Controllers\Api\Admin\RentalRequestController as AdminRentalRequestController;
use App\Http\Controllers\Api\Admin\BusinessProfileController as AdminBusinessProfileController;
use App\Http\Middleware\AuthServiceMiddleware;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckVerifiedProfile;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
 
// Apply locale middleware to all routes
Route::middleware([SetLocale::class])->group(function () {

    // ==================== PUBLIC ROUTES ====================

    // Health check
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok', 
            'service' => config('app.name'),
            'version' => config('app.version'),
            'timestamp' => now()->toISOString(),
        ]);
    });


    
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

    // Spaces (Public)
    Route::get('/spaces/{space}', [SpaceController::class, 'show']);


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

        // Visit Requests
        Route::prefix('visit-requests')->group(function () {
            Route::get('/', [VisitRequestController::class, 'index']);
            Route::post('/', [VisitRequestController::class, 'store']);
            Route::get('/{visitRequest}', [VisitRequestController::class, 'show']);
            Route::put('/{visitRequest}', [VisitRequestController::class, 'update']);
            Route::delete('/{visitRequest}', [VisitRequestController::class, 'destroy']);
        });

        // Rental Requests (Requires verified profile)
        Route::prefix('rental-requests')->middleware([CheckVerifiedProfile::class])->group(function () {
            Route::get('/', [RentalRequestController::class, 'index']);
            Route::post('/', [RentalRequestController::class, 'store']);
            Route::get('/{rentalRequest}', [RentalRequestController::class, 'show']);
            Route::put('/{rentalRequest}', [RentalRequestController::class, 'update']);
            Route::delete('/{rentalRequest}', [RentalRequestController::class, 'destroy']);
        });


        // ==================== ADMIN ROUTES ====================

        Route::prefix('admin')->middleware([CheckRole::class . ':admin,super-admin'])->group(function () {

            // Events Management
            Route::prefix('events')->group(function () {
                Route::get('/', [AdminEventController::class, 'index']);
                Route::post('/', [AdminEventController::class, 'store']);
                Route::get('/{event}', [AdminEventController::class, 'show']);
                Route::put('/{event}', [AdminEventController::class, 'update']);
                Route::delete('/{event}', [AdminEventController::class, 'destroy']);

                // Spaces for event
                Route::get('/{event}/spaces', [AdminSpaceController::class, 'index']);
                Route::post('/{event}/spaces', [AdminSpaceController::class, 'store']);
            });

            // Spaces Management
            Route::prefix('spaces')->group(function () {
                Route::get('/{space}', [AdminSpaceController::class, 'show']);
                Route::put('/{space}', [AdminSpaceController::class, 'update']);
                Route::delete('/{space}', [AdminSpaceController::class, 'destroy']);
            });

            // Visit Requests Management
            Route::prefix('visit-requests')->group(function () {
                Route::get('/', [AdminVisitRequestController::class, 'index']);
                Route::get('/{visitRequest}', [AdminVisitRequestController::class, 'show']);
                Route::put('/{visitRequest}/approve', [AdminVisitRequestController::class, 'approve'])
                    ->name('admin.visit-requests.approve');
                Route::put('/{visitRequest}/reject', [AdminVisitRequestController::class, 'reject'])
                    ->name('admin.visit-requests.reject');
            });

            // Rental Requests Management
            Route::prefix('rental-requests')->group(function () {
                Route::get('/', [AdminRentalRequestController::class, 'index']);
                Route::get('/{rentalRequest}', [AdminRentalRequestController::class, 'show']);
                Route::put('/{rentalRequest}/approve', [AdminRentalRequestController::class, 'approve'])
                    ->name('admin.rental-requests.approve');
                Route::put('/{rentalRequest}/reject', [AdminRentalRequestController::class, 'reject'])
                    ->name('admin.rental-requests.reject');
                Route::post('/{rentalRequest}/payment', [AdminRentalRequestController::class, 'recordPayment']);
            });

            // Business Profiles Management
            Route::prefix('profiles')->group(function () {
                Route::get('/', [AdminBusinessProfileController::class, 'index']);
                Route::get('/{profile}', [AdminBusinessProfileController::class, 'show']);
                Route::put('/{profile}/approve', [AdminBusinessProfileController::class, 'approve'])
                    ->name('admin.profiles.approve');
                Route::put('/{profile}/reject', [AdminBusinessProfileController::class, 'reject'])
                    ->name('admin.profiles.reject');
            });
        });
    });
});
