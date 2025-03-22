<?php

use App\Http\Controllers\ForgotYourPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('user', UserController::class);

// Change User's Profile Url And Full Name. (Logged In) 
Route::put('/user/{id}/update-profile-url-and-full-name', [UserController::class, 'changeProfilePictureAndFullName']);


/* -------- Auth Controller Routes -------- */
Route::get('/auth/check-if-email-belongs-to-an-account', [AuthController::class, 'checkIfEmailBelongsToAnAccount']);
Route::post('/auth/generate-otp', [AuthController::class, 'generateOtp']);
Route::post('/auth/send-otp-to-email', [AuthController::class, 'sendOtpToEmail']);
Route::post('/auth/register', [AuthController::class, 'register']);
/* -------- End of Auth Controller Routes -------- */

/* -------- Forgot Your Password Routes -------- */
Route::get('/forgotyourpassword/check-if-email-belongs-to-an-account', [ForgotYourPasswordController::class, 'checkIfEmailBelongsToAnAccount']);
Route::post('/forgotyourpassword/generate-otp', [ForgotYourPasswordController::class, 'generateOtp']);
Route::post('/forgotyourpassword/send-otp-to-email', [ForgotYourPasswordController::class, 'sendOtpToEmail']);
Route::get('/forgotyourpassword/validate-otp', [ForgotYourPasswordController::class, 'validateOtp']);
Route::post('/forgotyourpassword/change-user-password', [ForgotYourPasswordController::class, 'changeUserPassword']);
/* -------- End of Forgot Your Password Routes -------- */
