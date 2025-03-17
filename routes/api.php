<?php

use App\Http\Controllers\ForgotYourPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;


use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('user', UserController::class);

// Change User's Profile Url And Full Name. (Logged In) 
Route::put('/user/{id}/update-profile-url-and-full-name', [UserController::class, 'changeProfilePictureAndFullName']);


// This is used on forgot your password page. Before sending otp to email, it will check if user input email does belong to an account. (Logged Out)
Route::get('/forgotyourpassword/check-if-email-belongs-to-an-account', [ForgotYourPasswordController::class, 'checkIfEmailBelongsToAnAccount']);

// This is used to generate otp for the email that was validated in `/forgotyourpassword/check-if-email-belongs-to-an-account`
Route::post('/forgotyourpassword/generate-otp', [ForgotYourPasswordController::class, 'generateOtp']);

// This is used to send the otp into email
Route::post('/forgotyourpassword/send-otp-to-email', [ForgotYourPasswordController::class, 'sendOtpToEmail']);

// Validate if otp is correct
Route::get('/forgotyourpassword/validate-otp', [ForgotYourPasswordController::class, 'validateOtp']);