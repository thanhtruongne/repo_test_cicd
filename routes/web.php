<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\BankAccountController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\RoomController as AdminRoomController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\NotificationController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/callback', [LoginController::class, 'callback'])->name('callback');

//notifications
Route::post('/notifications', [NotificationController::class, 'store'])->name('notifications.store');
Route::middleware('auth')->group(function () {

    // Rooms
    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
    Route::get('/api/rooms/{room}/bookings', [RoomController::class, 'getBookings']);

    // Bookings
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::put('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/check-availability', [BookingController::class, 'checkAvailability'])->name('bookings.check-availability');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/get-info-user', [ProfileController::class, 'getUser3rdInfo'])->name('get-profile-3rd-info');
});

// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Trang login của admin
    Route::get('/', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AdminLoginController::class, 'login'])->name('login.submit');
    Route::get('logout', [AdminLoginController::class, 'logout'])->name('logout');

    //quen mat khau
    Route::get('reset-password', [AdminLoginController::class, 'showResetForm'])->name('reset');
    Route::post('reset-password', [AdminLoginController::class, 'resetPassword'])->name('reset.submit');

    // Trang dashboard chỉ cho admin đã đăng nhập
    Route::middleware(['is_admin'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        // user
        Route::prefix('user')->name('user.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::post('/{user}/update', [UserController::class, 'update'])->name('update');
            Route::post('/{user}/delete', [UserController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('booking')->name('booking.')->group(function () {
            Route::get('/', [AdminBookingController::class, 'index'])->name('index');
            Route::post('/store', [AdminBookingController::class, 'store'])->name('store');
            Route::post('/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('updateStatus');
            Route::post('/{booking}/payment-status', [AdminBookingController::class, 'updatePaymentStatus'])->name('updatePaymentStatus');
            Route::post('/{booking}/payment-method', [AdminBookingController::class, 'updatePaymentMethod'])->name('updatePaymentMethod');
            Route::post('/{booking}/delete', [AdminBookingController::class, 'destroy'])->name('destroy');
            Route::post('/{booking}/update', [AdminBookingController::class, 'update'])->name('update');
        });

        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [AdminNotificationController::class, 'index'])->name('index');
            Route::get('/{notification}', [AdminNotificationController::class, 'show'])->name('show');
            Route::post('/{notification}/read', [AdminNotificationController::class, 'markAsRead'])->name('markAsRead');
            Route::delete('/{notification}', [AdminNotificationController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('room')->name('room.')->group(function () {
            Route::get('/', [AdminRoomController::class, 'index'])->name('index');
            Route::post('/store', [AdminRoomController::class, 'store'])->name('store');
            Route::post('/{room}/status', [AdminRoomController::class, 'updateStatus'])->name('updateStatus');
            Route::post('/{room}/delete', [AdminRoomController::class, 'destroy'])->name('destroy');
            Route::post('/{room}/update', [AdminRoomController::class, 'update'])->name('update');
        });

        Route::prefix('bank')->name('bank.accounts.')->controller(BankAccountController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/store', 'store')->name('store');
            Route::post('/update/{bank}', 'update')->name('update');
            Route::post('/status/{bank}', 'updateStatus')->name('updateStatus');
            Route::post('/main/{bank}', 'updateMain')->name('updateMain');
            Route::delete('/delete/{bank}', 'destroy')->name('destroy');
        });

        // API: Lấy tất cả booking của tất cả phòng cho FullCalendar (admin)
        Route::get('/api/all-bookings', [AdminBookingController::class, 'allBookingsApi'])->name('allBookingsApi');
    });
});
// Route::prefix('admin')->name('admin.')->group(function () {
//     // Dashboard
//     Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

//     // // Users Management
//     // Route::resource('users', AdminUserController::class);
//     // Route::put('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');

//     // // Rooms Management
//     // Route::resource('rooms', AdminRoomController::class);
//     // Route::post('/rooms/{room}/upload-image', [AdminRoomController::class, 'uploadImage'])->name('rooms.upload-image');
//     // Route::delete('/rooms/{room}/delete-image/{image}', [AdminRoomController::class, 'deleteImage'])->name('rooms.delete-image');

//     // // Bookings Management
//     // Route::resource('bookings', AdminBookingController::class);
//     // Route::put('/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.update-status');
//     // Route::put('/bookings/{booking}/payment', [AdminBookingController::class, 'updatePayment'])->name('bookings.update-payment');

//     // // Settings
//     // Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
//     // Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
//     // Route::get('/settings/email', [SettingController::class, 'email'])->name('settings.email');
//     // Route::put('/settings/email', [SettingController::class, 'updateEmail'])->name('settings.update-email');
//     // Route::get('/settings/payment', [SettingController::class, 'payment'])->name('settings.payment');
//     // Route::put('/settings/payment', [SettingController::class, 'updatePayment'])->name('settings.update-payment');
// });

// Additional routes
Route::view('/contact', 'pages.contact')->name('contact');
Route::view('/pricing', 'pages.pricing')->name('pricing');
Route::view('/terms', 'pages.terms')->name('terms');
Route::view('/privacy', 'pages.privacy')->name('privacy');
