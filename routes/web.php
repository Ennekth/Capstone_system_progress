<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ForecastSettingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SalesRecordController;
use App\Http\Controllers\EoqSettingController;
use App\Http\Controllers\EoqCalibrationController;
use App\Http\Controllers\RestockBatchController;
use App\Http\Controllers\ThemeController;

// ==================== GUEST ROUTES ====================
Route::middleware('guest')->group(function () {
    Route::get('/login', [UserController::class, 'showLogin'])->name('login');
    Route::post('/login', [UserController::class, 'login']);
});

// ==================== AUTHENTICATED ROUTES ====================
Route::middleware('auth')->group(function () {
    // Admin only: User management
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });

    // Admin + Manager: Forecast Settings
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/forecast/settings', [ForecastSettingController::class, 'index'])->name('forecast.settings');
        Route::post('/forecast/settings/global', [ForecastSettingController::class, 'updateGlobal'])->name('forecast.settings.global');
        Route::post('/forecast/settings', [ForecastSettingController::class, 'storeOverride'])->name('forecast.settings.store');
        Route::delete('/forecast/settings/{forecastSetting}', [ForecastSettingController::class, 'destroyOverride'])->name('forecast.settings.destroy');
    });

    // Admin + Manager: full inventory, approvals, logs
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::post('/inventory/{product}/transfer', [InventoryController::class, 'transfer'])->name('inventory.transfer');
        Route::get('/inventory/logs', [InventoryController::class, 'logs'])->name('inventory.logs');

        Route::get('/inventory/approvals', [InventoryController::class, 'pendingApprovals'])->name('inventory.approvals');
        Route::post('/inventory/approvals/{movement}/approve', [InventoryController::class, 'approve'])->name('inventory.approve');
        Route::post('/inventory/approvals/{movement}/reject', [InventoryController::class, 'reject'])->name('inventory.reject');

        Route::get('/sales', [SalesRecordController::class, 'index'])->name('sales.records');
        Route::get('/sales/import', [SalesRecordController::class, 'showImport'])->name('sales.import');
        Route::post('/sales/import', [SalesRecordController::class, 'processImport'])->name('sales.import.process');
        Route::delete('/sales/{transaction}', [SalesRecordController::class, 'destroy'])->name('sales.destroy');

        Route::get('/forecast/eoq-settings', [EoqSettingController::class, 'index'])->name('eoq.settings');
        Route::post('/forecast/eoq-settings', [EoqSettingController::class, 'update'])->name('eoq.settings.update');

        Route::get('/forecast/eoq-calibration', [EoqCalibrationController::class, 'index'])->name('eoq.calibration');
        Route::post('/forecast/eoq-calibration/apply', [EoqCalibrationController::class, 'applyBatch'])->name('eoq.calibration.apply');
    });
    // Admin + Manager: Products & Categories
    Route::middleware('role:admin,manager')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('products', ProductController::class)->except(['show']);
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });
    // Admin + Manager + Staff: POS
    Route::middleware('role:admin,manager,staff')->group(function () {

        // POS
        Route::get('/pos', [PosController::class, 'index'])->name('pos');
        Route::post('/pos/add/{id}', [PosController::class, 'addToCart'])->name('pos.add');
        Route::post('/pos/remove/{id}', [PosController::class, 'removeFromCart'])->name('pos.remove');

        Route::get('/checkout', [PosController::class, 'checkout'])->name('checkout');
        Route::post('/checkout', [PosController::class, 'processCheckout'])->name('checkout.process');
        Route::get('/receipt/{id}', [PosController::class, 'receipt'])->name('receipt');

        Route::get('/transactions', [TransactionController::class, 'index'])->name('transaction.history');
    });
  
    // Warehouse staff + Staff: view inventory (read-only-ish, restock button only)
    Route::middleware('role:admin,manager,warehouse_staff,staff')->group(function () {
        Route::get('/inventory/view', [InventoryController::class, 'index'])->name('inventory.view');
        Route::post('/inventory/{product}/restock', [InventoryController::class, 'restock'])->name('inventory.restock');

        Route::get('/inventory/{product}/details', [InventoryController::class, 'show'])->name('inventory.show');

        Route::get('/inventory/my-requests', [InventoryController::class, 'myRequests'])->name('inventory.myRequests');
        Route::delete('/inventory/my-requests/{movement}', [InventoryController::class, 'cancelRequest'])->name('inventory.cancelRequest');
        Route::get('/inventory/my-requests/{movement}/edit', [InventoryController::class, 'editRequest'])->name('inventory.editRequest');
        Route::put('/inventory/my-requests/{movement}', [InventoryController::class, 'updateRequest'])->name('inventory.updateRequest');

        Route::get('/inventory/restock-mode', [RestockBatchController::class, 'index'])->name('inventory.restockBatch');
        Route::post('/inventory/restock-mode', [RestockBatchController::class, 'store'])->name('inventory.restockBatch.store');
    });
        // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    Route::post('/theme', [ThemeController::class, 'update'])->name('theme.update');
});