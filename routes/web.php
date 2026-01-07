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
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (Landing Page)
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/treatments', [LandingController::class, 'treatments'])->name('treatments');
Route::get('/treatments/{id}', [LandingController::class, 'treatmentDetail'])->name('treatments.detail');
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
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,owner'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Only Admin (not Owner)
    Route::middleware('role:admin')->group(function () {
        // Treatments
        Route::resource('treatments', TreatmentController::class);
        Route::post('treatments/{treatment}/toggle-status', [TreatmentController::class, 'toggleStatus'])->name('treatments.toggle-status');

        // Doctors
        Route::resource('doctors', DoctorController::class);
        Route::post('doctors/{doctor}/toggle-status', [DoctorController::class, 'toggleStatus'])->name('doctors.toggle-status');
        
        // Doctor Schedules
        Route::get('doctors/{doctor}/schedules', [DoctorController::class, 'schedules'])->name('doctors.schedules');
        Route::post('doctors/{doctor}/schedules', [DoctorController::class, 'storeSchedule'])->name('doctors.schedules.store');
        Route::delete('doctors/{doctor}/schedules/{schedule}', [DoctorController::class, 'deleteSchedule'])->name('doctors.schedules.delete');
        Route::post('doctors/{doctor}/schedules/{schedule}/toggle', [DoctorController::class, 'toggleScheduleStatus'])->name('doctors.schedules.toggle');

        // Bookings
        Route::get('bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::get('bookings/create', [AdminBookingController::class, 'create'])->name('bookings.create');
        Route::post('bookings', [AdminBookingController::class, 'store'])->name('bookings.store');
        Route::get('bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
        Route::post('bookings/{booking}/reschedule', [AdminBookingController::class, 'reschedule'])->name('bookings.reschedule');
        Route::post('bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
        Route::post('bookings/{booking}/complete', [AdminBookingController::class, 'complete'])->name('bookings.complete');
        Route::post('bookings/{booking}/update-notes', [AdminBookingController::class, 'updateNotes'])->name('bookings.update-notes');

        // Deposits
        Route::get('deposits', [DepositController::class, 'index'])->name('deposits.index');
        Route::get('deposits/{deposit}', [DepositController::class, 'show'])->name('deposits.show');
        Route::post('deposits/{deposit}/approve', [DepositController::class, 'approve'])->name('deposits.approve');
        Route::post('deposits/{deposit}/reject', [DepositController::class, 'reject'])->name('deposits.reject');

        // Vouchers
        Route::resource('vouchers', VoucherController::class);
        Route::post('vouchers/{voucher}/toggle-status', [VoucherController::class, 'toggleStatus'])->name('vouchers.toggle-status');
        Route::get('vouchers/{voucher}/usage', [VoucherController::class, 'usage'])->name('vouchers.usage');

        // Members
        Route::get('members', [MemberController::class, 'index'])->name('members.index');
        Route::get('members/{member}', [MemberController::class, 'show'])->name('members.show');
        Route::post('members/{member}/activate', [MemberController::class, 'activateMember'])->name('members.activate');
        Route::post('members/{member}/deactivate', [MemberController::class, 'deactivateMember'])->name('members.deactivate');
        Route::post('members/{member}/update-discount', [MemberController::class, 'updateDiscount'])->name('members.update-discount');

        // Feedbacks
        Route::get('feedbacks', [AdminFeedbackController::class, 'index'])->name('feedbacks.index');
        Route::get('feedbacks/{feedback}', [AdminFeedbackController::class, 'show'])->name('feedbacks.show');
        Route::post('feedbacks/{feedback}/toggle-visibility', [AdminFeedbackController::class, 'toggleVisibility'])->name('feedbacks.toggle-visibility');
        Route::delete('feedbacks/{feedback}', [AdminFeedbackController::class, 'destroy'])->name('feedbacks.destroy');

        // Before-After Photos
        Route::get('before-after-photos', [BeforeAfterPhotoController::class, 'index'])->name('before-after-photos.index');
        Route::post('bookings/{booking}/before-after', [BeforeAfterPhotoController::class, 'upload'])->name('before-after.upload');
        Route::delete('bookings/{booking}/before-after', [BeforeAfterPhotoController::class, 'destroy'])->name('before-after.destroy');

        // No-Show Notes
        Route::post('no-show-notes', [NoShowNoteController::class, 'store'])->name('no-show-notes.store');
        Route::delete('no-show-notes/{noShowNote}', [NoShowNoteController::class, 'destroy'])->name('no-show-notes.destroy');
    });
});

