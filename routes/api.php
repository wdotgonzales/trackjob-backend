<?php

use App\Http\Controllers\ForgotYourPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\ReminderController;

use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Change User's Profile Url And Full Name. (Logged In) 
Route::put('/user/update-profile-url-and-full-name', [UserController::class, 'changeProfilePictureAndFullName'])->middleware('auth:sanctum');

/* -------- Auth Controller Routes -------- */
Route::post('/auth/login', [AuthController::class, 'login']);

Route::get('/auth/check-if-email-belongs-to-an-account', [AuthController::class, 'checkIfEmailBelongsToAnAccount']);
Route::post('/auth/generate-otp', [AuthController::class, 'generateOtp']);
Route::post('/auth/send-otp-to-email', [AuthController::class, 'sendOtpToEmail']);
Route::post('/auth/register', [AuthController::class, 'register']);

/* -------- Forgot Your Password Routes -------- */
Route::get('/forgotyourpassword/check-if-email-belongs-to-an-account', [ForgotYourPasswordController::class, 'checkIfEmailBelongsToAnAccount']);
Route::post('/forgotyourpassword/generate-otp', [ForgotYourPasswordController::class, 'generateOtp']);
Route::post('/forgotyourpassword/send-otp-to-email', [ForgotYourPasswordController::class, 'sendOtpToEmail']);
Route::get('/forgotyourpassword/validate-otp', [ForgotYourPasswordController::class, 'validateOtp']);
Route::post('/forgotyourpassword/change-user-password', [ForgotYourPasswordController::class, 'changeUserPassword']);

/* Job Application Routes */
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('user/job-applications', JobApplicationController::class);
});

/* Reminder Routes */
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('user/job-applications/{job_application}/reminders', ReminderController::class);
});
