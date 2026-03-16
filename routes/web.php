<?php

use App\Http\Controllers\LandingController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Customer\FeedbackController as CustomerFeedbackController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\TreatmentController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\DepositController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\FeedbackController as AdminFeedbackController;
use App\Http\Controllers\Admin\BeforeAfterPhotoController;
use App\Http\Controllers\Admin\NoShowNoteController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\LaporanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (Landing Page)
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::post('/check-booking', [LandingController::class, 'checkBooking'])->name('check-booking');
Route::get('/treatments', [LandingController::class, 'treatments'])->name('treatments');
Route::get('/treatments/{id}', [LandingController::class, 'treatmentDetail'])->name('landing.treatment-detail');
Route::get('/vouchers', [LandingController::class, 'vouchers'])->name('vouchers');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
// Register
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register/send-otp', [RegisterController::class, 'sendOTP'])->name('register.send-otp');
Route::post('/register/verify-otp', [RegisterController::class, 'verifyOTP'])->name('register.verify-otp');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

// Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Forgot Password
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('forgot-password');
Route::post('/forgot-password/send-otp', [ForgotPasswordController::class, 'sendOTP'])->name('forgot-password.send-otp');
Route::post('/forgot-password/verify-otp', [ForgotPasswordController::class, 'verifyOTP'])->name('forgot-password.verify-otp');
Route::post('/forgot-password/reset', [ForgotPasswordController::class, 'resetPassword'])->name('forgot-password.reset');

/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
*/
Route::prefix('customer')->name('customer.')->middleware(['auth', 'role:customer'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');

    // Booking
    Route::get('/bookings', [CustomerBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [CustomerBookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [CustomerBookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{id}', [CustomerBookingController::class, 'show'])->name('bookings.show');
    
    // AJAX endpoints
    Route::post('/bookings/available-slots', [CustomerBookingController::class, 'getAvailableSlots'])->name('bookings.available-slots');
    Route::post('/bookings/available-doctors', [CustomerBookingController::class, 'getAvailableDoctors'])->name('bookings.available-doctors');
    Route::post('/bookings/check-voucher', [CustomerBookingController::class, 'checkVoucher'])->name('bookings.check-voucher');
    
    // Deposit
    Route::post('/bookings/{id}/upload-deposit', [CustomerBookingController::class, 'uploadDepositProof'])->name('bookings.upload-deposit');

    // Feedback
    Route::get('/bookings/{booking}/feedback', [CustomerFeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/bookings/{booking}/feedback', [CustomerFeedbackController::class, 'store'])->name('feedback.store');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,owner,doctor,frontdesk'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Only Admin/Owner
    Route::middleware('role:admin,owner')->group(function () {
        // Treatments
        Route::get('treatments', [TreatmentController::class, 'index'])->name('treatments.index')->middleware('permission:treatments,view');
        Route::get('treatments/create', [TreatmentController::class, 'create'])->name('treatments.create')->middleware('permission:treatments,add');
        Route::post('treatments', [TreatmentController::class, 'store'])->name('treatments.store')->middleware('permission:treatments,add');
        Route::get('treatments/{treatment}', [TreatmentController::class, 'show'])->name('treatments.show')->middleware('permission:treatments,view');
        Route::get('treatments/{treatment}/edit', [TreatmentController::class, 'edit'])->name('treatments.edit')->middleware('permission:treatments,edit');
        Route::put('treatments/{treatment}', [TreatmentController::class, 'update'])->name('treatments.update')->middleware('permission:treatments,edit');
        Route::delete('treatments/{treatment}', [TreatmentController::class, 'destroy'])->name('treatments.destroy')->middleware('permission:treatments,delete');
        Route::post('treatments/{treatment}/toggle-status', [TreatmentController::class, 'toggleStatus'])->name('treatments.toggle-status')->middleware('permission:treatments,edit');

        // Doctors
        Route::get('doctors', [DoctorController::class, 'index'])->name('doctors.index')->middleware('permission:doctors,view');
        Route::get('doctors/create', [DoctorController::class, 'create'])->name('doctors.create')->middleware('permission:doctors,add');
        Route::post('doctors', [DoctorController::class, 'store'])->name('doctors.store')->middleware('permission:doctors,add');
        Route::get('doctors/{doctor}', [DoctorController::class, 'show'])->name('doctors.show')->middleware('permission:doctors,view');
        Route::get('doctors/{doctor}/edit', [DoctorController::class, 'edit'])->name('doctors.edit')->middleware('permission:doctors,edit');
        Route::put('doctors/{doctor}', [DoctorController::class, 'update'])->name('doctors.update')->middleware('permission:doctors,edit');
        Route::delete('doctors/{doctor}', [DoctorController::class, 'destroy'])->name('doctors.destroy')->middleware('permission:doctors,delete');
        Route::post('doctors/{doctor}/toggle-status', [DoctorController::class, 'toggleStatus'])->name('doctors.toggle-status')->middleware('permission:doctors,edit');
        Route::get('doctors/{doctor}/schedules', [DoctorController::class, 'schedules'])->name('doctors.schedules')->middleware('permission:doctors,view');
        Route::post('doctors/{doctor}/schedules', [DoctorController::class, 'storeSchedule'])->name('doctors.schedules.store')->middleware('permission:doctors,edit');
        Route::delete('doctors/{doctor}/schedules/{schedule}', [DoctorController::class, 'deleteSchedule'])->name('doctors.schedules.delete')->middleware('permission:doctors,delete');
        Route::post('doctors/{doctor}/schedules/{schedule}/toggle', [DoctorController::class, 'toggleScheduleStatus'])->name('doctors.schedules.toggle')->middleware('permission:doctors,edit');

        // Bookings
        Route::get('bookings', [AdminBookingController::class, 'index'])->name('bookings.index')->middleware('permission:bookings,view');
        Route::get('bookings/create', [AdminBookingController::class, 'create'])->name('bookings.create')->middleware('permission:bookings,add');
        Route::post('bookings', [AdminBookingController::class, 'store'])->name('bookings.store')->middleware('permission:bookings,add');
        Route::get('bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show')->middleware('permission:bookings,view');
        Route::post('bookings/available-slots', [AdminBookingController::class, 'getAvailableSlots'])->name('bookings.available-slots')->middleware('permission:bookings,view');
        Route::post('bookings/{booking}/reschedule', [AdminBookingController::class, 'reschedule'])->name('bookings.reschedule')->middleware('permission:bookings,edit');
        Route::post('bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel')->middleware('permission:bookings,edit');
        Route::post('bookings/{booking}/complete', [AdminBookingController::class, 'complete'])->name('bookings.complete')->middleware('permission:bookings,edit');
        Route::post('bookings/{booking}/no-show', [AdminBookingController::class, 'markAsNoShow'])->name('bookings.no-show')->middleware('permission:bookings,edit');
        Route::post('bookings/{booking}/update-notes', [AdminBookingController::class, 'updateNotes'])->name('bookings.update-notes')->middleware('permission:bookings,edit');

        // Deposits
        Route::get('deposits', [DepositController::class, 'index'])->name('deposits.index')->middleware('permission:deposits,view');
        Route::get('deposits/{deposit}', [DepositController::class, 'show'])->name('deposits.show')->middleware('permission:deposits,view');
        Route::post('deposits/{deposit}/approve', [DepositController::class, 'approve'])->name('deposits.approve')->middleware('permission:deposits,edit');
        Route::post('deposits/{deposit}/reject', [DepositController::class, 'reject'])->name('deposits.reject')->middleware('permission:deposits,edit');

        // Vouchers
        Route::get('vouchers', [VoucherController::class, 'index'])->name('vouchers.index')->middleware('permission:vouchers,view');
        Route::get('vouchers/create', [VoucherController::class, 'create'])->name('vouchers.create')->middleware('permission:vouchers,add');
        Route::post('vouchers', [VoucherController::class, 'store'])->name('vouchers.store')->middleware('permission:vouchers,add');
        Route::get('vouchers/{voucher}', [VoucherController::class, 'show'])->name('vouchers.show')->middleware('permission:vouchers,view');
        Route::get('vouchers/{voucher}/edit', [VoucherController::class, 'edit'])->name('vouchers.edit')->middleware('permission:vouchers,edit');
        Route::put('vouchers/{voucher}', [VoucherController::class, 'update'])->name('vouchers.update')->middleware('permission:vouchers,edit');
        Route::delete('vouchers/{voucher}', [VoucherController::class, 'destroy'])->name('vouchers.destroy')->middleware('permission:vouchers,delete');
        Route::post('vouchers/{voucher}/toggle-status', [VoucherController::class, 'toggleStatus'])->name('vouchers.toggle-status')->middleware('permission:vouchers,edit');
        Route::get('vouchers/{voucher}/usage', [VoucherController::class, 'usage'])->name('vouchers.usage')->middleware('permission:vouchers,view');

        // Members
        Route::get('members', [MemberController::class, 'index'])->name('members.index')->middleware('permission:members,view');
        Route::get('members/{member}', [MemberController::class, 'show'])->name('members.show')->middleware('permission:members,view');
        Route::post('members/{member}/activate', [MemberController::class, 'activateMember'])->name('members.activate')->middleware('permission:members,edit');
        Route::post('members/{member}/deactivate', [MemberController::class, 'deactivateMember'])->name('members.deactivate')->middleware('permission:members,edit');
        Route::post('members/{member}/update-discount', [MemberController::class, 'updateDiscount'])->name('members.update-discount')->middleware('permission:members,edit');

        // Feedbacks
        Route::get('feedbacks', [AdminFeedbackController::class, 'index'])->name('feedbacks.index')->middleware('permission:feedbacks,view');
        Route::get('feedbacks/{feedback}', [AdminFeedbackController::class, 'show'])->name('feedbacks.show')->middleware('permission:feedbacks,view');
        Route::post('feedbacks/{feedback}/toggle-visibility', [AdminFeedbackController::class, 'toggleVisibility'])->name('feedbacks.toggle-visibility')->middleware('permission:feedbacks,edit');
        Route::delete('feedbacks/{feedback}', [AdminFeedbackController::class, 'destroy'])->name('feedbacks.destroy')->middleware('permission:feedbacks,delete');

        // Before-After Photos
        Route::get('before-after-photos', [BeforeAfterPhotoController::class, 'index'])->name('before-after-photos.index')->middleware('permission:bookings,view');
        Route::post('bookings/{booking}/before-after', [BeforeAfterPhotoController::class, 'upload'])->name('before-after.upload')->middleware('permission:bookings,edit');
        Route::delete('bookings/{booking}/before-after', [BeforeAfterPhotoController::class, 'destroy'])->name('before-after.destroy')->middleware('permission:bookings,delete');

        // No-Show Notes
        Route::post('no-show-notes', [NoShowNoteController::class, 'store'])->name('no-show-notes.store')->middleware('permission:bookings,edit');
        Route::delete('no-show-notes/{noShowNote}', [NoShowNoteController::class, 'destroy'])->name('no-show-notes.destroy')->middleware('permission:bookings,delete');

        // Settings
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index')->middleware('permission:settings,view');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update')->middleware('permission:settings,edit');
        Route::post('settings/test-connection', [SettingController::class, 'testConnection'])->name('settings.test-connection')->middleware('permission:settings,view');
        Route::post('settings/toggle-shop', [SettingController::class, 'toggleShopStatus'])->name('settings.toggle-shop')->middleware('permission:settings,edit');

        // Articles
        Route::resource('articles', \App\Http\Controllers\Admin\ArticleController::class);
        Route::post('articles/{article}/toggle-status', [\App\Http\Controllers\Admin\ArticleController::class, 'toggleStatus'])->name('articles.toggle-status');

        // User Management
        Route::get('users', [UserManagementController::class, 'index'])->name('users.index')->middleware('permission:manajemen_user,view');
        Route::post('users', [UserManagementController::class, 'store'])->name('users.store')->middleware('permission:manajemen_user,add');
        Route::get('users/{user}', [UserManagementController::class, 'show'])->name('users.show')->middleware('permission:manajemen_user,view');
        Route::put('users/{user}', [UserManagementController::class, 'update'])->name('users.update')->middleware('permission:manajemen_user,edit');
        Route::post('users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status')->middleware('permission:manajemen_user,edit');
        Route::delete('users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy')->middleware('permission:manajemen_user,delete');

        // Role Management
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index')->middleware('permission:manajemen_role,view');
        Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show')->middleware('permission:manajemen_role,view');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store')->middleware('permission:manajemen_role,add');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('permission:manajemen_role,edit');
        Route::put('roles/{role}/permissions', [RoleController::class, 'savePermissions'])->name('roles.permissions')->middleware('permission:manajemen_role,edit');
        Route::post('roles/{role}/toggle-status', [RoleController::class, 'toggleStatus'])->name('roles.toggle-status')->middleware('permission:manajemen_role,edit');
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:manajemen_role,delete');

        // Laporan Analitik
        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index')->middleware('permission:laporan,view');
        Route::get('laporan/export-csv', [LaporanController::class, 'exportCsv'])->name('laporan.export-csv')->middleware('permission:laporan,view');
    });
});

// Landing - Articles
Route::get('/articles', [LandingController::class, 'articles'])->name('articles');
Route::get('/articles/{slug}', [LandingController::class, 'articleDetail'])->name('article.detail');

