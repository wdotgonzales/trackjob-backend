<?php

use App\Http\Controllers\ForgotYourPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\SubscriptionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Change User's Profile Url And Full Name. (Logged In) 
Route::put('/user/update-profile-url-and-full-name', [UserController::class, 'changeProfilePictureAndFullName'])->middleware('auth:sanctum');

/* -------- Auth Controller Routes -------- */
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/auth/check-if-email-belongs-to-an-account', [AuthController::class, 'checkIfEmailBelongsToAnAccount']);
Route::post('/auth/generate-otp', [AuthController::class, 'generateOtp']);
Route::post('/auth/send-otp-to-email', [AuthController::class, 'sendOtpToEmail']);
Route::post('/auth/register', [AuthController::class, 'register']);

/* -------- Forgot Your Password Routes -------- */
Route::post('/forgot-your-password', [ForgotYourPasswordController::class, 'handleForgotYourPasssword']);
Route::post('/validate-otp', [ForgotYourPasswordController::class, 'handleValidateOtp']);

/* Job Application Routes */
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('user/job-applications', JobApplicationController::class);
});

/* Reminder Routes */
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('user/job-applications/{job_application}/reminders', ReminderController::class);
});

/* Subscription Routes */
Route::middleware('auth:sanctum')->group(function () {
    Route::post('user/purchase-subscription', [SubscriptionController::class, 'handleSubscription']);
});
