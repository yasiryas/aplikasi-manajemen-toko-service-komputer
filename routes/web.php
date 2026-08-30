<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ServiceOrderController;
use App\Http\Controllers\SettingsController;
use App\Models\Customer;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
    Route::get('/dashboard/activity', [DashboardController::class, 'activity'])->name('dashboard.activity');

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store')->middleware('role:admin');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update')->middleware('role:admin');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy')->middleware('role:admin');

    Route::get('/api/customers/search', [CustomerController::class, 'search'])->name('api.customers.search');
    Route::get('/api/customers/{customer}/devices', fn (Customer $customer) => response()->json(['devices' => $customer->devices]))->name('api.customers.devices');

    Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
    Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');
    Route::put('/devices/{device}', [DeviceController::class, 'update'])->name('devices.update')->middleware('role:admin');
    Route::delete('/devices/{device}', [DeviceController::class, 'destroy'])->name('devices.destroy')->middleware('role:admin');

    Route::get('/service-orders', [ServiceOrderController::class, 'index'])->name('service-orders.index');
    Route::get('/progres-saya', [ServiceOrderController::class, 'progress'])->name('service-orders.progress');
    Route::post('/service-orders', [ServiceOrderController::class, 'store'])->name('service-orders.store');
    Route::get('/service-orders/{order}', [ServiceOrderController::class, 'show'])->name('service-orders.show');
    Route::put('/service-orders/{order}', [ServiceOrderController::class, 'update'])->name('service-orders.update');
    Route::patch('/service-orders/{order}/status', [ServiceOrderController::class, 'changeStatus'])->name('service-orders.status');
    Route::post('/service-orders/{order}/notify', [ServiceOrderController::class, 'notify'])->name('service-orders.notify');
    Route::delete('/service-orders/{order}', [ServiceOrderController::class, 'destroy'])->name('service-orders.destroy')->middleware('role:admin');

    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index')->middleware('role:admin');
    Route::get('/api/invoices/ready-orders', [InvoiceController::class, 'readyOrders'])->name('api.invoices.ready-orders')->middleware('role:admin');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store')->middleware('role:admin');
    Route::patch('/invoices/{invoice}/payment', [InvoiceController::class, 'updatePayment'])->name('invoices.update-payment')->middleware('role:admin');
    Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print')->middleware('role:admin');

    Route::get('/dokumentasi', fn () => view('dokumentasi.index', ['page' => 'dokumentasi']))->name('dokumentasi');

    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit')->middleware('role:admin');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update')->middleware('role:admin');
});
