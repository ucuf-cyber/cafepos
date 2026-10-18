<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminMenuController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminTableController;
use App\Http\Controllers\Admin\AdminStockController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Owner\OwnerDashboardController;
use App\Http\Controllers\Owner\OwnerStockController;
use App\Http\Controllers\Owner\OwnerReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        return redirect("/{$role}/dashboard");
    }
    return redirect('/login');
});

// ── PUBLIC (Customer, tanpa auth) ────────────────────────────
Route::get('/table/{token}', [CustomerController::class, 'menu'])->name('customer.menu');
Route::post('/table/{token}/order', [CustomerController::class, 'store'])->name('customer.order');
Route::get('/table/{token}/order/{id}/track', [CustomerController::class, 'track'])->name('customer.track');
Route::get('/table/{token}/order/{id}/status', [CustomerController::class, 'status'])->name('customer.status');
Route::post('/table/{token}/order/{id}/paid', [CustomerController::class, 'markPaid'])->name('customer.paid');
Route::get('/payment/qris', [PaymentController::class, 'qrisImage'])->name('payment.qris');
Route::get('/payment/bank-accounts', [PaymentController::class, 'bankAccounts'])->name('payment.banks');

// ── AUTH ─────────────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── KASIR ─────────────────────────────────────────────────────
Route::middleware(['auth', 'role:kasir,admin'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [KasirController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders/check-new', [KasirController::class, 'checkNew'])->name('orders.check');
    Route::get('/orders/{id}', [KasirController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{id}/confirm', [KasirController::class, 'confirm'])->name('orders.confirm');
    Route::patch('/orders/{id}/status', [KasirController::class, 'updateStatus'])->name('orders.status');
    Route::patch('/orders/{id}/cancel', [KasirController::class, 'cancel'])->name('orders.cancel');
});

// ── ADMIN ─────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::resource('menu', AdminMenuController::class);
    Route::post('menu/{id}/images', [AdminMenuController::class, 'uploadImages'])->name('menu.images.upload');
    Route::delete('menu/{id}/images/{imageId}', [AdminMenuController::class, 'deleteImage'])->name('menu.images.delete');
    Route::patch('menu/{id}/stock', [AdminMenuController::class, 'updateStock'])->name('menu.stock');

    Route::resource('categories', AdminCategoryController::class)->except(['show']);
    Route::resource('tables', AdminTableController::class)->except(['show']);
    Route::get('tables/{id}/qr', [AdminTableController::class, 'downloadQr'])->name('tables.qr');
    Route::get('tables/{id}/print', [AdminTableController::class, 'printQr'])->name('tables.print');

    Route::get('/stock', [AdminStockController::class, 'index'])->name('stock.index');
    Route::patch('/stock/{id}', [AdminStockController::class, 'update'])->name('stock.update');

    Route::resource('users', AdminUserController::class)->except(['show']);

    Route::get('/settings/payment', [AdminSettingController::class, 'payment'])->name('settings.payment');
    Route::post('/settings/payment', [AdminSettingController::class, 'updatePayment'])->name('settings.payment.update');
    Route::resource('settings/bank-accounts', \App\Http\Controllers\Admin\AdminBankAccountController::class)
        ->names('settings.bank-accounts')->except(['show']);
    Route::post('/settings/qris', [AdminSettingController::class, 'updateQris'])->name('settings.qris');
});

// ── OWNER ─────────────────────────────────────────────────────
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart-data', [OwnerDashboardController::class, 'chartData'])->name('dashboard.data');
    Route::get('/stock', [OwnerStockController::class, 'index'])->name('stock');
    Route::get('/reports/export', [OwnerReportController::class, 'export'])->name('reports.export');
});
