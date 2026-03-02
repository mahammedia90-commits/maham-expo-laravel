<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ServiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Service API Routes
|--------------------------------------------------------------------------
*/

// Health Check with diagnostics
Route::get('/health', function () {
    $status = 'ok';
    $checks = [];

    // Database check
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        $checks['database'] = 'connected';
    } catch (\Throwable $e) {
        $checks['database'] = 'error: ' . $e->getMessage();
        $status = 'degraded';
    }

    // Redis check
    $cacheDriver = config('cache.default');
    if ($cacheDriver === 'redis' || config('queue.default') === 'redis') {
        try {
            \Illuminate\Support\Facades\Redis::ping();
            $checks['redis'] = 'connected';
        } catch (\Throwable $e) {
            $checks['redis'] = 'unavailable: ' . $e->getMessage();
            $status = 'degraded';
        }
    } else {
        $checks['redis'] = 'not configured as primary driver';
    }

    // Cache driver check
    try {
        \Illuminate\Support\Facades\Cache::put('health_test', true, 5);
        $checks['cache'] = 'working (' . $cacheDriver . ')';
    } catch (\Throwable $e) {
        $checks['cache'] = 'error: ' . $e->getMessage();
        $status = 'degraded';
    }

    // JWT check
    $checks['jwt'] = !empty(config('jwt.secret')) ? 'configured' : 'MISSING JWT_SECRET';
    if ($checks['jwt'] !== 'configured') {
        $status = 'degraded';
    }

    return response()->json([
        'status' => $status,
        'service' => config('auth-service.service_name'),
        'version' => config('auth-service.service_version'),
        'timestamp' => now()->toISOString(),
        'checks' => $checks,
        'drivers' => [
            'cache' => $cacheDriver,
            'queue' => config('queue.default'),
            'session' => config('session.driver'),
        ],
        'php_version' => PHP_VERSION,
    ], $status === 'ok' ? 200 : 503);
});

Route::prefix('v1')->group(function () {

/* 
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    // Password Reset (Public)
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

/*
|--------------------------------------------------------------------------
| Service-to-Service Routes
| يتم التحقق عبر X-Service-Token header
|--------------------------------------------------------------------------
*/
Route::prefix('service')->group(function () {
    Route::post('/verify-token', [ServiceController::class, 'verifyUserToken']);
    Route::post('/check-permission', [ServiceController::class, 'checkUserPermission']);
    Route::post('/user-info', [ServiceController::class, 'getUserInfo']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->group(function () {

    // Admin Dashboard Statistics
    Route::get('/admin/stats/users', [DashboardController::class, 'users'])
        ->middleware('permission:users.view');

    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/me', [AuthController::class, 'me']);

        // Password & Profile Management
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);

        // Email Verification
        Route::post('/email/send-verification', [AuthController::class, 'sendEmailVerification']);
        Route::post('/email/verify', [AuthController::class, 'verifyEmail']);
    });

    // Internal token verification
    Route::post('/verify-token', [AuthController::class, 'verifyToken']);
    Route::post('/check-permission', [AuthController::class, 'checkPermission']);
    Route::post('/check-permissions', [AuthController::class, 'checkPermissions']);

    /*
    |--------------------------------------------------------------------------
    | Users Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])
            ->middleware('permission:users.view');
        
        Route::post('/', [UserController::class, 'store'])
            ->middleware('permission:users.create');
        
        Route::get('/{user}', [UserController::class, 'show'])
            ->middleware('permission:users.view');
        
        Route::put('/{user}', [UserController::class, 'update'])
            ->middleware('permission:users.update');
        
        Route::delete('/{user}', [UserController::class, 'destroy'])
            ->middleware('permission:users.delete');

        // Role & Permission assignment
        Route::post('/{user}/roles', [UserController::class, 'assignRoles'])
            ->middleware('permission:roles.update');
        
        Route::post('/{user}/permissions', [UserController::class, 'assignPermissions'])
            ->middleware('permission:permissions.update');
        
        Route::get('/{user}/permissions', [UserController::class, 'permissions'])
            ->middleware('permission:permissions.view');
    });

    /*
    |--------------------------------------------------------------------------
    | Roles Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])
            ->middleware('permission:roles.view');
        
        Route::post('/', [RoleController::class, 'store'])
            ->middleware('permission:roles.create');
        
        Route::get('/{role}', [RoleController::class, 'show'])
            ->middleware('permission:roles.view');
        
        Route::put('/{role}', [RoleController::class, 'update'])
            ->middleware('permission:roles.update');
        
        Route::delete('/{role}', [RoleController::class, 'destroy'])
            ->middleware('permission:roles.delete');

        // Permission management
        Route::post('/{role}/permissions', [RoleController::class, 'syncPermissions'])
            ->middleware('permission:roles.update');
        
        Route::post('/{role}/permissions/add', [RoleController::class, 'addPermissions'])
            ->middleware('permission:roles.update');
        
        Route::post('/{role}/permissions/remove', [RoleController::class, 'removePermissions'])
            ->middleware('permission:roles.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Permissions Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('permissions')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])
            ->middleware('permission:permissions.view');
        
        Route::post('/', [PermissionController::class, 'store'])
            ->middleware('permission:permissions.create');
        
        Route::post('/resource', [PermissionController::class, 'createForResource'])
            ->middleware('permission:permissions.create');
        
        Route::get('/{permission}', [PermissionController::class, 'show'])
            ->middleware('permission:permissions.view');
        
        Route::put('/{permission}', [PermissionController::class, 'update'])
            ->middleware('permission:permissions.update');
        
        Route::delete('/{permission}', [PermissionController::class, 'destroy'])
            ->middleware('permission:permissions.delete');
    });

    /*
    |--------------------------------------------------------------------------
    | Services Management (for registering other microservices)
    |--------------------------------------------------------------------------
    */
    Route::prefix('services')->middleware('permission:services.view')->group(function () {
        Route::get('/', [ServiceController::class, 'index']);

        Route::post('/', [ServiceController::class, 'store'])
            ->middleware('permission:services.create');

        Route::get('/{service}', [ServiceController::class, 'show']);

        Route::put('/{service}', [ServiceController::class, 'update'])
            ->middleware('permission:services.update');

        Route::delete('/{service}', [ServiceController::class, 'destroy'])
            ->middleware('permission:services.delete');

        Route::post('/{service}/regenerate-token', [ServiceController::class, 'regenerateToken'])
            ->middleware('permission:services.update');

        // Service Roles Management
        Route::get('/{service}/roles', [ServiceController::class, 'roles']);

        Route::post('/{service}/roles', [ServiceController::class, 'assignRoles'])
            ->middleware('permission:services.update');

        Route::delete('/{service}/roles', [ServiceController::class, 'removeRoles'])
            ->middleware('permission:services.update');

        Route::put('/{service}/roles', [ServiceController::class, 'syncRoles'])
            ->middleware('permission:services.update');
    });
});
});