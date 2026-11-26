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

use App\Http\Controllers\TemporarySaleController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\TemporaryPurchaseController;
use App\Http\Controllers\ProductController;


// ==========================================
// ADMIN AUTHENTICATION
// ==========================================

Route::middleware(\App\Http\Middleware\AdminGuest::class)->group(function () {

    Route::get('/login', [AuthController::class, 'loginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.store');

    Route::get('/register', [AuthController::class, 'registerForm'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.store');

});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:web')
    ->name('logout');


// ==========================================
// HOME ROUTE
// ==========================================

Route::get('/', function () {
    return redirect()->route('credits.index');
});


// ==========================================
// ADMIN CREDIT MANAGEMENT
// ==========================================

Route::middleware('auth')
    ->prefix('credits')
    ->name('credits.')
    ->group(function () {


        // ======================================
        // DASHBOARD
        // ======================================

        Route::get('/', [CreditDashboardController::class, 'index'])
            ->name('index');


        // ======================================
        // CUSTOMER MANAGEMENT
        // ======================================

        Route::get('/customers/create', [CustomerController::class, 'create'])
            ->name('customers.create');

        Route::post('/customers', [CustomerController::class, 'store'])
            ->name('customers.store');

        Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])
            ->name('customers.edit');

        Route::put('/customers/{customer}', [CustomerController::class, 'update'])
            ->name('customers.update');

        Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])
            ->name('customers.destroy');

        Route::get('/customers/{customer}/account', [CustomerAccountController::class, 'edit'])
            ->name('customers.account.edit');

        Route::put('/customers/{customer}/account', [CustomerAccountController::class, 'update'])
            ->name('customers.account.update');

        Route::get('/customers/{customer}', [CustomerController::class, 'show'])
            ->name('customers.show');


        // ======================================
        // CREDIT SALES
        // ======================================

        Route::get('/sales/create', [CreditSaleController::class, 'create'])
            ->name('sales.create');

        Route::post('/sales', [CreditSaleController::class, 'store'])
            ->name('sales.store');

        Route::get('/sales/{sale}', [CreditSaleController::class, 'show'])
            ->name('sales.show');


        // ======================================
        // CUSTOMER PAYMENTS
        // ======================================

        Route::get('/payments/create/{customer}', [CreditPaymentController::class, 'create'])
            ->name('payments.create');

        Route::post('/payments', [CreditPaymentController::class, 'store'])
            ->name('payments.store');

        Route::get('/payments/{payment}', [CreditPaymentController::class, 'show'])
            ->name('payments.show');


        // ======================================
        // TEMPORARY SALES
        // ======================================

        Route::prefix('temporary-sales')
            ->name('temporary-sales.')
            ->controller(TemporarySaleController::class)
            ->group(function () {

                Route::get('/', 'index')
                    ->name('index');

                Route::get('/create', 'create')
                    ->name('create');

                Route::post('/', 'store')
                    ->name('store');

                Route::get('/{id}/edit', 'edit')
                    ->name('edit');

                Route::post('/{id}/items', 'addItem')
                    ->name('items.store');

                Route::put('/{id}/items/{itemId}', 'updateItem')
                    ->name('items.update');

                Route::delete('/{id}/items/{itemId}', 'deleteItem')
                    ->name('items.destroy');

                Route::delete('/{id}', 'destroy')
                    ->name('destroy');

            });


        // ======================================
        // PURCHASE MANAGEMENT
        // ======================================

        Route::prefix('purchases')
            ->name('purchases.')
            ->group(function () {

                // ----------------------------------
                // PERMANENT PURCHASE HISTORY
                // ----------------------------------

                Route::get('/', [PurchaseController::class, 'index'])
                    ->name('index');


                // ----------------------------------
                // PURCHASE ENTRY PAGE
                // ----------------------------------

                Route::get('/create', [TemporaryPurchaseController::class, 'index'])
                    ->name('create');


                // ----------------------------------
                // CREATE TEMPORARY PURCHASE BILL
                // ----------------------------------

                Route::post('/drafts', [TemporaryPurchaseController::class, 'startDraft'])
                    ->name('drafts.store');


                // ----------------------------------
                // OPEN TEMPORARY PURCHASE BILL
                // ----------------------------------

                Route::get('/drafts/{id}', [TemporaryPurchaseController::class, 'openDraft'])
                    ->name('drafts.open');


                // ----------------------------------
                // ADD PURCHASE PRODUCT
                // ----------------------------------

                Route::post('/drafts/{purchaseId}/items', [TemporaryPurchaseController::class, 'addItem'])
                    ->name('drafts.items.store');


                // ----------------------------------
                // UPDATE PURCHASE PRODUCT
                // ----------------------------------

                Route::put('/drafts/{purchaseId}/items/{itemId}', [TemporaryPurchaseController::class, 'updateItem'])
                    ->name('drafts.items.update');


                // ----------------------------------
                // DELETE PURCHASE PRODUCT
                // ----------------------------------

                Route::delete('/drafts/{purchaseId}/items/{itemId}', [TemporaryPurchaseController::class, 'deleteItem'])
                    ->name('drafts.items.delete');


                // ----------------------------------
                // UPLOAD PURCHASE BILL IMAGES
                // ----------------------------------

                Route::post('/drafts/{purchaseId}/images', [TemporaryPurchaseController::class, 'uploadImages'])
                    ->name('drafts.images.store');


                // ----------------------------------
                // SAVE TEMPORARY PURCHASE BILL
                // ----------------------------------

                Route::post('/drafts/{id}/save', [TemporaryPurchaseController::class, 'saveDraft'])
                    ->name('drafts.save');


                // ----------------------------------
                // CONFIRM PURCHASE PERMANENTLY
                // ----------------------------------

                Route::post('/drafts/{id}/confirm', [TemporaryPurchaseController::class, 'confirmPurchase'])
                    ->name('drafts.confirm');


                // ----------------------------------
                // DELETE TEMPORARY PURCHASE BILL
                // ----------------------------------

                Route::delete('/drafts/{id}', [TemporaryPurchaseController::class, 'deleteDraft'])
                    ->name('drafts.delete');


                // ----------------------------------
                // PERMANENT PURCHASE DETAILS
                // ----------------------------------

                Route::get('/{id}', [PurchaseController::class, 'show'])
                    ->name('show');

            });


        // ======================================
        // PRODUCT MANAGEMENT
        // ======================================

        Route::prefix('products')
            ->name('products.')
            ->controller(ProductController::class)
            ->group(function () {

                // Display all products
                Route::get('/', 'index')
                    ->name('index');

                // Save new product
                Route::post('/', 'store')
                    ->name('store');

                // View product details
                Route::get('/{product}', 'show')
                    ->name('show');

                // Update product
                Route::put('/{product}', 'update')
                    ->name('update');

                // Delete product
                Route::delete('/{product}', 'destroy')
                    ->name('destroy');

            });

    });


// ==========================================
// CUSTOMER PORTAL AUTHENTICATION
// ==========================================

Route::prefix('customer')
    ->name('customer.')
    ->group(function () {

        Route::middleware(\App\Http\Middleware\CustomerGuest::class)
            ->group(function () {

                Route::get('/login', [CustomerPortalAuthController::class, 'showLogin'])
                    ->name('login');

                Route::post('/login', [CustomerPortalAuthController::class, 'login'])
                    ->name('login.store');

            });


        // ======================================
        // CUSTOMER PORTAL
        // ======================================

        Route::middleware('auth:customer')
            ->group(function () {

                Route::get('/dashboard', [CustomerPortalController::class, 'index'])
                    ->name('dashboard');

                Route::get('/invoices/{sale}', [CustomerPortalController::class, 'invoice'])
                    ->name('invoices.show');

                Route::get('/payments/{payment}', [CustomerPortalController::class, 'payment'])
                    ->name('payments.show');

                Route::get('/profile', [CustomerPortalController::class, 'profile'])
                    ->name('profile');

                Route::get('/change-password', [CustomerPasswordController::class, 'edit'])
                    ->name('password.edit');

                Route::put('/change-password', [CustomerPasswordController::class, 'update'])
                    ->name('password.update');

                Route::post('/logout', [CustomerPortalAuthController::class, 'logout'])
                    ->name('logout');

            });

    });
