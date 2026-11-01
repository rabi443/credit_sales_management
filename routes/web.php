<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CreditDashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CreditSaleController;
use App\Http\Controllers\CreditPaymentController;

use App\Http\Controllers\Admin\CustomerAccountController;
use App\Http\Controllers\CustomerPortalAuthController;
use App\Http\Controllers\CustomerPortalController;
use App\Http\Controllers\CustomerPasswordController;


// ==============================
// AUTHENTICATION ROUTES
// ==============================

Route::get('/login', [AuthController::class, 'loginForm'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.store');

Route::get('/register', [AuthController::class, 'registerForm'])
    ->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register.store');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


// ==============================
// HOME ROUTE
// ==============================

Route::get('/', function () {
    return redirect()->route('credits.index');
});


// ==============================
// CREDIT MANAGEMENT ROUTES
// ==============================

Route::middleware('auth')
->prefix('credits')
->name('credits.')
->group(function () {

    // Admin manages customer login accounts
    Route::get('/customers/{customer}/account',
        [CustomerAccountController::class, 'edit']
    )->name('customers.account.edit');

    Route::put('/customers/{customer}/account',
        [CustomerAccountController::class, 'update']
    )->name('customers.account.update');

    // --------------------------
    // CUSTOMER DASHBOARD
    // --------------------------

    Route::get('/', [CreditDashboardController::class, 'index'])
        ->name('index');


    // --------------------------
    // CUSTOMER CREATE
    // --------------------------

    Route::get('/customers/create', [CustomerController::class, 'create'])
        ->name('customers.create');

    Route::post('/customers', [CustomerController::class, 'store'])
        ->name('customers.store');


    // --------------------------
    // CUSTOMER VIEW
    // --------------------------

    Route::get('/customers/{customer}', [CustomerController::class, 'show'])
        ->name('customers.show');


    // --------------------------
    // CUSTOMER EDIT
    // --------------------------

    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])
        ->name('customers.edit');

    // --------------------------
    // CUSTOMER UPDATE
    // --------------------------

    Route::put('/customers/{customer}', [CustomerController::class, 'update'])
        ->name('customers.update');

    // --------------------------
    // CUSTOMER DELETE
    // --------------------------

    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])
        ->name('customers.destroy');


    // --------------------------
    // CREDIT SALES
    // --------------------------

    Route::get('/sales/create', [CreditSaleController::class, 'create'])
        ->name('sales.create');

    Route::post('/sales', [CreditSaleController::class, 'store'])
        ->name('sales.store');


    // --------------------------
    // CUSTOMER PAYMENTS
    // --------------------------

    Route::get('/payments/create/{customer}', [CreditPaymentController::class, 'create'])
        ->name('payments.create');

    Route::post('/payments', [CreditPaymentController::class, 'store'])
        ->name('payments.store');

});

// Customer authentication
Route::prefix('customer')->name('customer.')->group(function () {

    Route::middleware('guest:customer')->group(function () {
        Route::get('/login', [CustomerPortalAuthController::class, 'showLogin'])
            ->name('login');

        Route::post('/login', [CustomerPortalAuthController::class, 'login'])
            ->name('login.store');
    });

    Route::middleware('auth:customer')->group(function () {
        Route::get('/dashboard', [CustomerPortalController::class, 'index'])
            ->name('dashboard');

        Route::get('/invoices/{sale}', [CustomerPortalController::class, 'invoice'])
            ->name('invoices.show');

        Route::post('/logout', [CustomerPortalAuthController::class, 'logout'])
            ->name('logout');

        Route::get('/profile', [CustomerPortalController::class, 'profile'])
            ->name('profile');

        Route::get('/change-password', [CustomerPasswordController::class, 'edit'])
            ->name('password.edit');

        Route::put('/change-password', [CustomerPasswordController::class, 'update'])
            ->name('password.update');
    });
});