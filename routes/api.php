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

/* Register */
Route::post('/register/otp-process', [AuthController::class, 'handleOtpProcess']);
Route::post('/register/validate-otp', [AuthController::class, 'handleValidateOtp']);
Route::post('/register', [AuthController::class, 'handleRegister']);

/* -------- Forgot Your Password Routes -------- */
Route::post('/forgot-your-password', [ForgotYourPasswordController::class, 'handleForgotYourPasssword']);
Route::post('/forgot-your-password/validate-otp', [ForgotYourPasswordController::class, 'handleValidateOtp']);
Route::post('/forgot-your-password/change-password', [ForgotYourPasswordController::class, 'handleChangeUserPassword']);

/* Job Application Routes */
Route::middleware('auth:sanctum')->group(function () {
    Route::get('user/job-applications', [JobApplicationController::class, "index"]);
    Route::post('user/job-applications', [JobApplicationController::class, "store"]);
});

Route::middleware(['auth:sanctum', 'job.application.owner.check'])->group(function () {
    Route::get('user/job-applications/{job_application}', [JobApplicationController::class, 'show']);
    Route::put('user/job-applications/{job_application}', [JobApplicationController::class, 'update']);
    Route::delete('user/job-applications/{job_application}', [JobApplicationController::class, 'destroy']);
});

/* Reminder Routes */
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('user/job-applications/{job_application}/reminders', ReminderController::class);
});

/* Subscription Routes */
Route::middleware('auth:sanctum')->group(function () {
    // Route::get('/user/check-if-subscription-exist', [SubscriptionController::class, 'handleSubscriptionChecker']);
    Route::post('/user/purchase-subscription', [SubscriptionController::class, 'handlePurchaseSubscription']);
});

Route::middleware(['auth:sanctum', 'subscription.check'])->group(function () {
    Route::get('/user/subscription', [SubscriptionController::class, 'subscription']);
});
