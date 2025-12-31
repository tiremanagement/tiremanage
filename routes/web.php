<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    LoginController,
    DashboardController,
    SupplierController,
    TireController,
    VehicleController,
    TireRequestController,
    DriverController,
    ForgotPasswordController,
    ReceiptController,
    SectionManagerController,
    MechanicOfficerController,
    TransportOfficerController,
    ReportController
};
use App\Http\Middleware\ForcePasswordChange;

/*
|--------------------------------------------------------------------------
| Root Redirect
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => redirect()->route('login'));

/*
|--------------------------------------------------------------------------
| Authentication Routes (Public)
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Forgot Password Routes (Public)
|--------------------------------------------------------------------------
*/
Route::prefix('driver')->name('driver.')->controller(ForgotPasswordController::class)->group(function () {
    Route::get('/forgot-password', 'showRequestForm')->name('password.request.form');
    Route::post('/forgot-password', 'sendOtp')->name('password.send.otp');
    Route::get('/verify-otp', 'showVerifyForm')->name('password.verify.form');
    Route::post('/verify-otp', 'verifyOtp')->name('password.verify');
    Route::get('/reset-password', 'showResetForm')->name('password.reset.form');
    Route::post('/reset-password', 'resetPassword')->name('password.reset');
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Require Auth)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |----------------------------------------------------------------------
    | Admin Routes
    |----------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

        Route::resource('vehicles', VehicleController::class);
        Route::resource('tires', TireController::class);
        Route::resource('suppliers', SupplierController::class);

        Route::get('/requests/pending', [DashboardController::class, 'pendingRequests'])->name('request.pending');

    Route::get('/drivers/check-id', [DriverController::class, 'checkId'])->name('drivers.checkId');
    Route::get('/drivers/create', [DriverController::class, 'create'])->name('drivers.create');
        Route::post('/drivers', [DriverController::class, 'store'])->name('drivers.store');
        Route::delete('/drivers/{id}', [DriverController::class, 'destroy'])->name('drivers.destroy');

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/vehicles', [ReportController::class, 'exportVehicles'])->name('vehicles');
            Route::get('/drivers', [ReportController::class, 'exportDrivers'])->name('drivers');
            Route::get('/suppliers', [ReportController::class, 'exportSuppliers'])->name('suppliers');
            Route::get('/tires', [ReportController::class, 'exportTires'])->name('tires');
            Route::get('/section-manager/{status}', [ReportController::class, 'exportSectionManager'])->name('section_manager');
            Route::get('/mechanic-officer/{status}', [ReportController::class, 'exportMechanicOfficer'])->name('mechanic_officer');
            Route::get('/transport-officer/{status}', [ReportController::class, 'exportTransportOfficer'])->name('transport_officer');
        });
    });

    /*
    |----------------------------------------------------------------------
    | Driver Routes
    |----------------------------------------------------------------------
    */
    Route::prefix('driver')
        ->name('driver.')
        ->middleware(ForcePasswordChange::class)
        ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'driver'])->name('dashboard');

        Route::get('/requests', [TireRequestController::class, 'index'])->name('requests.index');
        Route::get('/requests/create', [TireRequestController::class, 'create'])->name('requests.create');
        Route::post('/requests', [TireRequestController::class, 'store'])->name('requests.store');
        Route::delete('/requests/{request}', [TireRequestController::class, 'destroy'])->name('requests.destroy');

        Route::get('/vehicles/lookup', [VehicleController::class, 'lookup'])->name('vehicles.lookup');

        Route::get('/profile', [DriverController::class, 'editProfile'])->name('profile.edit');
        Route::post('/profile', [DriverController::class, 'updateProfile'])->name('profile.update');

        Route::get('/change-password', [DriverController::class, 'changePasswordForm'])->name('password.form');
        Route::post('/change-password', [DriverController::class, 'updatePassword'])->name('password.update');

        Route::get('/receipts', [DriverController::class, 'receipts'])->name('receipts');
        Route::get('/receipts/{id}/download', [DriverController::class, 'downloadReceipt'])->name('receipt.download');
    });

    /*
    |----------------------------------------------------------------------
    | Section Manager Routes
    |----------------------------------------------------------------------
    */
    Route::prefix('section-manager')->name('section_manager.')->group(function () {
        Route::get('/dashboard', [SectionManagerController::class, 'index'])->name('dashboard');

        // Search
        Route::get('/requests/search', [SectionManagerController::class, 'search'])->name('requests.search');

        Route::get('/pending', [SectionManagerController::class, 'pending'])->name('requests.pending');
        Route::get('/approved', [SectionManagerController::class, 'approved'])->name('requests.approved_list');
        Route::get('/rejected', [SectionManagerController::class, 'rejected'])->name('requests.rejected_list');
        Route::get('/requests/{id}/edit', [SectionManagerController::class, 'edit'])->name('requests.edit');
        Route::put('/requests/{id}', [SectionManagerController::class, 'update'])->name('requests.update');
        Route::post('/{id}/approve', [SectionManagerController::class, 'approve'])->name('requests.approve');
        Route::post('/{id}/reject', [SectionManagerController::class, 'reject'])->name('requests.reject');

        Route::resource('vehicles', VehicleController::class);
        Route::get('/drivers', [SectionManagerController::class, 'drivers'])->name('drivers.index');
        Route::get('/drivers/create', [SectionManagerController::class, 'createDriver'])->name('drivers.create');
        Route::post('/drivers', [SectionManagerController::class, 'storeDriver'])->name('drivers.store');
        Route::delete('/drivers/{id}', [SectionManagerController::class, 'destroyDriver'])->name('drivers.destroy');
    });

    /*
    |----------------------------------------------------------------------
    | Mechanic Officer Routes
    |----------------------------------------------------------------------
    */
    Route::prefix('mechanic-officer')->name('mechanic_officer.')->group(function () {
        Route::get('/pending', [MechanicOfficerController::class, 'pending'])->name('pending');
        Route::post('/approve/{id}', [MechanicOfficerController::class, 'approve'])->name('approve');
        Route::post('/reject/{id}', [MechanicOfficerController::class, 'reject'])->name('reject');
        Route::get('/approved', [MechanicOfficerController::class, 'approved'])->name('approved');
        Route::get('/rejected', [MechanicOfficerController::class, 'rejected'])->name('rejected');
        Route::get('/edit-request/{id}', [MechanicOfficerController::class, 'edit'])->name('edit_request');
        Route::put('/update-request/{id}', [MechanicOfficerController::class, 'update'])->name('update');
    });

    /*
    |----------------------------------------------------------------------
    | Transport Officer Routes
    |----------------------------------------------------------------------
    */
    Route::prefix('transport-officer')->name('transport_officer.')->group(function () {
        Route::get('/', [TransportOfficerController::class, 'pending'])->name('dashboard');
        Route::get('/pending', [TransportOfficerController::class, 'pending'])->name('pending');
        Route::get('/approved', [TransportOfficerController::class, 'approved'])->name('approved');
        Route::get('/rejected', [TransportOfficerController::class, 'rejected'])->name('rejected');

        Route::get('/edit/{id}', [TransportOfficerController::class, 'edit'])->name('edit_request');
        Route::put('/update/{id}', [TransportOfficerController::class, 'update'])->name('update');
        Route::post('/approve/{id}', [TransportOfficerController::class, 'approve'])->name('approve');
        Route::post('/reject/{id}', [TransportOfficerController::class, 'reject'])->name('reject');

        Route::get('/receipt/create/{id}', [TransportOfficerController::class, 'createReceipt'])->name('receipt.create');
        Route::post('/receipt/store', [TransportOfficerController::class, 'storeReceipt'])->name('receipt.store');
        Route::post('/receipt/send', [TransportOfficerController::class, 'sendReceiptEmail'])->name('receipt.send');
        Route::get('/receipts/{id}/pdf', [ReceiptController::class, 'generatePDF'])->name('receipts.pdf');
    });
});