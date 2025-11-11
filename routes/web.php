<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\TruckController;
use App\Http\Controllers\TonnageController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\OptimizationController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\DeliverySchedulingController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\GateController;
use App\Http\Controllers\ShipmentCostController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\TrackingController;


Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');


// 🏠 Dashboard & halaman utama
Route::middleware('auth')->group(function () {
// 🏠 Halaman utama (dashboard)
Route::get('/', function () {
    $pageTitle = 'Dashboard';
    $breadchumb = 'Gate Dashboard';
    return view('index', compact('pageTitle','breadchumb'));
})->name('home');
Route::get('optimization', function () {
    $pageTitle = 'Optimization';
    return view('optimization.index', compact('pageTitle'));
})->name('optimization');
Route::post('/optimization/load-po-data', [App\Http\Controllers\OptimizationController::class, 'loadPOData'])
    ->name('optimization.loadPOData');
Route::get('/optimization/options', [OptimizationController::class, 'getOptions'])->name('optimization.getOptions');
Route::get('/optimization/dropdown-data', [OptimizationController::class, 'dropdownData'])->name('optimization.dropdownData');
Route::get('/getTruckRouteData', [OptimizationController::class, 'getTruckRouteData'])->name('optimization.getTruckRouteData');
Route::post('/optimization/save-schedule', [OptimizationController::class, 'saveSchedule'])->name('optimization.saveSchedule');
Route::prefix('users')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::post('/', [App\Http\Controllers\UserController::class, 'store'])->name('users.store');
    Route::put('/{user}', [App\Http\Controllers\UserController::class, 'update'])->name('users.update');
    Route::delete('/{user}', [App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
});
Route::prefix('roles')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/', [RoleController::class, 'store'])->name('roles.store');
    Route::put('/{id}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');
});
Route::prefix('permissions')->middleware('auth', 'role:admin')->group(function () {
    Route::get('/', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('/', [PermissionController::class, 'store'])->name('permissions.store');
    Route::put('/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
});
Route::prefix('customer')->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('customer.index');
    Route::get('/create', [CustomerController::class, 'create'])->name('customer.create');
    Route::post('/', [CustomerController::class, 'store'])->name('customer.store');
    Route::get('/{id}/edit', [CustomerController::class, 'edit'])->name('customer.edit');
    Route::put('/{id}', [CustomerController::class, 'update'])->name('customer.update');
    Route::delete('/{id}', [CustomerController::class, 'destroy'])->name('customer.destroy');
});
Route::prefix('produk')->group(function () {
    Route::get('/', [productController::class, 'index'])->name('product.index');
    Route::get('/create', [productController::class, 'create'])->name('product.create');
    Route::post('/', [productController::class, 'store'])->name('product.store');
    Route::get('/{id}/edit', [productController::class, 'edit'])->name('product.edit');
    Route::put('/{id}', [productController::class, 'update'])->name('product.update');
    Route::delete('/{id}', [productController::class, 'destroy'])->name('product.destroy');
});
Route::prefix('po')->group(function () {
    Route::get('/', [PurchaseOrderController::class, 'index'])->name('po.index');
    Route::get('/create', [PurchaseOrderController::class, 'create'])->name('po.create');
    Route::post('/', [PurchaseOrderController::class, 'store'])->name('po.store');
    Route::get('/{po}/edit', [PurchaseOrderController::class, 'edit'])->name('po.edit');
    Route::put('/{po}', [PurchaseOrderController::class, 'update'])->name('po.update');
    Route::delete('/{po}', [PurchaseOrderController::class, 'destroy'])->name('po.destroy');
});
Route::resource('trucks', TruckController::class);
Route::resource('drivers', DriverController::class);
Route::resource('tonnages', TonnageController::class);
Route::resource('sources', SourceController::class);
Route::resource('routes', RouteController::class);
Route::resource('vendors', VendorController::class);
Route::resource('gates', GateController::class);
Route::resource('shipment_cost', ShipmentCostController::class);
Route::get('/shipments/export', [ShipmentController::class, 'export'])->name('shipment.export');
Route::get('/shipment/cost', [ShipmentController::class, 'getCost'])->name('shipment.cost');
Route::get('/shipment/available-gates', [ShipmentController::class, 'getAvailableGates'])->name('shipment.availableGates');
Route::resource('shipment', ShipmentController::class);
Route::prefix('delivery-scheduling')->group(function () {
    Route::get('/', [DeliverySchedulingController::class, 'index'])->name('delivery_scheduling.index');
    Route::get('/{id}/edit', [DeliverySchedulingController::class, 'edit'])->name('delivery_scheduling.edit');
    Route::put('/{id}', [DeliverySchedulingController::class, 'update'])->name('delivery_scheduling.update');
    Route::delete('/{id}', [DeliverySchedulingController::class, 'destroy'])->name('delivery_scheduling.destroy');

    // Route untuk tombol eksekusi DO terpilih
    Route::post('/delivery-scheduling/bulk-action', [DeliverySchedulingController::class, 'bulkAction'])->name('delivery_scheduling.bulkAction');
});
Route::get('/do/edit', [ShipmentController::class, 'editWithDo'])->name('do.edit');
Route::get('/do/search', [ShipmentController::class, 'searchShipment'])->name('do.search');
Route::post('/do/update', [ShipmentController::class, 'updateDoDetail'])->name('do.update');
Route::get('/do/suratjalan', [ShipmentController::class, 'printSuratJalan'])->name('do.suratjalan');
Route::get('/do/receipt', [ShipmentController::class, 'receipt'])->name('do.receipt');
Route::post('/do/receipt', [ShipmentController::class, 'storeReceipt'])->name('do.receipt.store');
Route::get('/do', [ShipmentController::class, 'indexCheck'])->name('do.index.check');
Route::get('/do/search-list', [ShipmentController::class, 'searchList'])->name('do.searchlist');
// Tampilkan halaman konfirmasi check-in
Route::get('/do/checkin/{noshipment}', [ShipmentController::class, 'doCheckin'])->name('do.checkin');
Route::get('/do/checkout/{noshipment}', [ShipmentController::class, 'doCheckout'])->name('do.checkout');
// Proses update check-in
Route::post('/do/checkin/{noshipment}', [ShipmentController::class, 'storeCheckin'])->name('do.checkin.store');
Route::post('/do/checkout/{noshipment}', [ShipmentController::class, 'storeCheckout'])->name('do.checkout.store');
Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');
Route::get('/api/tracking/latest', [TrackingController::class, 'getLatest'])->name('tracking.latest');

Route::get('/tracking/history', [TrackingController::class, 'history'])->name('tracking.history');
Route::get('/api/tracking/history', [TrackingController::class, 'getHistory'])->name('tracking.history.data');


});

