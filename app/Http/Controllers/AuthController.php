<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Models\VerificationCode;
use Carbon\Carbon;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function handleOtpProcess(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|unique:users,email',
        ]);

        return $this->generateOtp($validatedData['email']);
    }

    private function generateOtp(string $email)
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

    private function sendOtpEmail(string $otp, string $email)
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

    public function handleValidateOtp(Request $request)
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
            'message' => "You can now proceed to register.",
            'email' => $validatedData['email']
        ], 200);
    }

    private function isVerificationCodeExpired(VerificationCode $verification_code)
    {
        $current_date = Carbon::now('Asia/Manila');
        $expiration_date = Carbon::parse($verification_code->expiration_date);

        if ($current_date->greaterThanOrEqualTo($expiration_date)) {
            return true;
        }
        return false;
    }

    public function handleRegister(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'profile_url' => 'required|url|max:255'
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);
        $user = User::create($validatedData);

        return response()->json([
            'message' => 'User registered successfully!',
            'user' => new UserResource($user)
        ], 201);
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $validatedData['email'])->first();

        if (!$user || !Hash::check($validatedData['password'], $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect'
            ], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
