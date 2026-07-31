<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SavingsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\SavingsGoalController;
use App\Http\Controllers\SmartPricingController;
use App\Http\Controllers\ProfitDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Categories
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);

    // Products
    Route::post('products/{product}/adjust-stock', [ProductController::class, 'adjustStock'])->name('products.adjust-stock');
    Route::resource('products', ProductController::class)->except(['show']);

    // Smart Pricing
    Route::get('/profit-dashboard', [ProfitDashboardController::class, 'index'])->name('profit-dashboard.index');
    Route::post('/profit-dashboard/sales', [ProfitDashboardController::class, 'store'])->name('profit-dashboard.sales.store');
    Route::delete('/profit-dashboard/sales/{sale}', [ProfitDashboardController::class, 'destroy'])->name('profit-dashboard.sales.destroy');
    Route::get('/smart-pricing', [SmartPricingController::class, 'index'])->name('smart-pricing.index');
    Route::post('/smart-pricing/products', [SmartPricingController::class, 'storeProduct'])->name('smart-pricing.products.store');
    Route::put('/smart-pricing/{product}/costs', [SmartPricingController::class, 'updateCosts'])->name('smart-pricing.costs.update');
    Route::post('/smart-pricing/{product}/approve', [SmartPricingController::class, 'approve'])->name('smart-pricing.approve');
    Route::post('/smart-pricing/{product}/override', [SmartPricingController::class, 'override'])->name('smart-pricing.override');

    // POS
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
    Route::get('/pos/search', [PosController::class, 'searchProducts'])->name('pos.search');

    // Customers
    Route::get('customers/{customer}/soa-pdf', [App\Http\Controllers\CustomerController::class, 'downloadSoaPdf'])->name('customers.soa-pdf');
    Route::resource('customers', App\Http\Controllers\CustomerController::class);
    Route::get('customers/{customer}/soa', [CustomerController::class, 'soa'])->name('customers.soa');

    // Invoices
    Route::get('invoices/{invoice}/pdf', [App\Http\Controllers\InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
    Route::resource('invoices', App\Http\Controllers\InvoiceController::class);

    // Payments
    Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

    // Expenses
    Route::resource('expenses', ExpenseController::class)->except(['show']);

    // Savings
    Route::resource('savings', SavingsController::class)->only(['index', 'store', 'destroy']);
    Route::resource('savings-goals', SavingsGoalController::class)->only(['store', 'update', 'destroy']);
    
    // Fallback routes for savings-goals to prevent MethodNotAllowedHttpException on redirects
    Route::get('savings-goals/{savings_goal}', function () {
        return redirect()->route('savings.index');
    });
    Route::get('savings-goals', function () {
        return redirect()->route('savings.index');
    });

    // Restaurant Tables
    Route::get('/tables', [TableController::class, 'index'])->name('tables.index');
    Route::post('/tables', [TableController::class, 'store'])->name('tables.store');
    Route::patch('/tables/{table}/status', [TableController::class, 'updateStatus'])->name('tables.update-status');
    Route::delete('/tables/{table}', [TableController::class, 'destroy'])->name('tables.destroy');

    // Orders (Restaurant)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/orders/{order}/complete', [OrderController::class, 'complete'])->name('orders.complete');

    // Kitchen Display
    Route::get('/kitchen', [KitchenController::class, 'index'])->name('kitchen.index');
    Route::patch('/kitchen/item/{orderItem}/status', [KitchenController::class, 'updateItemStatus'])->name('kitchen.item-status');
    Route::post('/kitchen/order/{order}/ready', [KitchenController::class, 'markOrderReady'])->name('kitchen.order-ready');
    Route::get('/kitchen/poll', [KitchenController::class, 'poll'])->name('kitchen.poll');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/income', [ReportController::class, 'income'])->name('income');
        Route::get('/expenses', [ReportController::class, 'expenses'])->name('expenses');
        Route::get('/profit', [ReportController::class, 'profit'])->name('profit');
        Route::get('/outstanding', [ReportController::class, 'outstanding'])->name('outstanding');
        Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
        Route::get('/audit', [ReportController::class, 'auditTrail'])->name('audit');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/best-sellers', [ReportController::class, 'bestSellers'])->name('best-sellers');
    });
});

require __DIR__.'/auth.php';
