<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\BookingController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('bookings',[BookingController::class,'getListBookings']);
Route::get('user/bookings', [BookingController::class, 'getListBookingsUser']);
Route::get('bookings/status', [BookingController::class, 'getStatusBadge']);
Route::get('booking/{booking}', [BookingController::class, 'getDetailBookings']);
Route::post('booking/change-status/{booking}', [BookingController::class, 'changeStatus']);
Route::post('booking/change-payment-status/{booking}', [BookingController::class, 'changePaymentStatus']);
Route::get('rooms',[BookingController::class,'rooms']);