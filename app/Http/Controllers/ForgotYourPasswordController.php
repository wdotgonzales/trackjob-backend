<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\VerificationCode;
use App\Http\Resources\UserResource;

use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class ForgotYourPasswordController extends Controller
{
    public function handleForgotYourPasssword(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
        ]);

        $user = $this->isEmailBelongToAnAccount($validatedData['email']);

        if (!$user) {
            return response()->json([
                'message' => 'Email does not belong to any account'
            ], 404);
        }

        return $this->generateOtp($user->email);
    }

    public function isEmailBelongToAnAccount(string $email)
    {
        return User::where('email', $email)->first();
    }

    public function generateOtp(string $email)
    {
        $existingOtp = VerificationCode::where('email', $email)->first();

        if ($existingOtp) {
            $existingOtp->delete();
        }

        $otp = random_int(100000, 999999);
        $start_date = Carbon::now('Asia/Manila');
        $expiration_date = $start_date->copy()->addMinutes(5);

        // Insert into database
        $verification_code = VerificationCode::create([
            'email' => $email,
            'otp' => $otp,
            'expiration_date' => $expiration_date,
            'start_date' => $start_date,
        ]);

        return $this->sendOtpEmail($verification_code->otp, $verification_code->email);
    }

    public function sendOtpEmail(string $otp, string $email)
    {
        if (empty($otp)) {
            return response()->json([
                'message' => 'OTP is missing',
            ], 400);
        }

        if (empty($email)) {
            return response()->json([
                'message' => 'Email is missing',
            ], 400);
        }

        $subject = 'Forgot Your Password - OTP';
        $title = 'Hello from TrackJob';
        $message = 'Your OTP code is';

        $data = [
            'email' => $email,
            'subject' => $subject,
            'title' => $title,
            'message' => $message,
            'otp' => $otp
        ];

        try {
            Mail::to($email)->send(new SendMail($data));
            return response()->json(['message' => 'Otp send to email successfully!', 'email' => $email], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send email.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    function handleValidateOtp(Request $request)
    {
        $validatedData = $request->validate([
            'otp' => 'required|string|min:6|max:6',
            'email' => 'required|email',
        ]);

        $verification_code = VerificationCode::where('otp', $validatedData['otp'])
            ->where('email', $validatedData['email'])
            ->first();

        if (!$verification_code) {
            return response()->json([
                'message' => 'OTP is incorrect',
            ], 404);
        }

        if ($this->isVerificationCodeExpired($verification_code)) {
            return response()->json([
                'message' => 'OTP is expired'
            ], 400);
        }

        $verification_code->delete();

        return response()->json([
            'message' => "You can now change user's password",
            'email' => $validatedData['email']
        ], 200);
    }

    function isVerificationCodeExpired(VerificationCode $verification_code)
    {
        $current_date = Carbon::now('Asia/Manila');
        $expiration_date = Carbon::parse($verification_code->expiration_date);

        if ($current_date->greaterThanOrEqualTo($expiration_date)) {
            return true;
        }
        return false;
    }

    public function changeUserPassword(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'new_password' => 'required|string|min:8',
                'email' => 'required|email',
            ]);

            $user = User::where('email', $validatedData['email'])->first();

            if (!$user) {
                return response()->json([
                    'email' => $validatedData['email'],
                    'user' => null,
                    'message' => 'Email does not belong to any account.'
                ], 404);
            }

            $new_password = Hash::make($validatedData['new_password']);

            $user->update([
                'password' => $new_password,
            ]);

            return response()->json([
                'message' => "User's password is successfully changed",
                'user' => new UserResource($user)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
