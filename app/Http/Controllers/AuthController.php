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
    
    public function checkIfEmailBelongsToAnAccount(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
        ]);

        $email = $validatedData['email'];

        try {
            $user = User::where('email', $email)->first();

            if ($user) {

                return response()->json(
                    [
                        'email' => $email,
                        'message' => "Email cannot be used, it's already belongs to an account."
                    ],
                    404 // HTTP status code for OK
                );
            }

            return response()->json(
                [
                    'email' => $email,
                    'message' => 'Email can be use, it does not belong to any account yet.'
                ],
                200
            );
        } catch (\Exception $e) {
            // Handle any unexpected errors during the query
            return response()->json(
                [
                    'email' => $email,
                    'message' => 'An error occurred while checking the email.',
                    'error' => $e->getMessage()
                ],
                500 // HTTP status code for Internal Server Error
            );
        }
    }

    public function generateOtp(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
        ]);

        $email = $validatedData['email'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'email' => $email,
                'message' => 'Invalid email format.'
            ], 400);
        }

        try {
            // Delete existing OTP if present
            $existingOtp = VerificationCode::where('email', $email)->first();
            if ($existingOtp) {
                $existingOtp->delete();
            }

            // Generate new OTP and set expiration
            $otp = random_int(100000, 999999);
            $start_date = Carbon::now('Asia/Manila');
            $expiration_date = $start_date->copy()->addMinutes(5);

            // Insert into database
            VerificationCode::create([
                'email' => $email,
                'otp' => $otp,
                'expiration_date' => $expiration_date,
                'start_date' => $start_date,
            ]);

            return response()->json([
                'email' => $email,
                'otp' => $otp,
                'message' => 'OTP generated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'email' => $email,
                'message' => 'Failed to generate OTP.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function sendOtpToEmail(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string',
            'title' => 'required|string',
            'message' => 'required|string',
            'otp' => 'required|string|min:6|max:6',
        ]);

        // Prepare email data to be passed to the Mailable class
        $data = [
            'subject' => $request->subject,
            'title' => $request->title,
            'message' => $request->message,
            'otp' => $request->otp
        ];

        try {
            // Attempt to send the email using the SendMail Mailable class
            Mail::to($request->email)->send(new SendMail($data));

            // Return success response if email was sent successfully
            return response()->json(['message' => 'Email sent successfully!', 'email' => $request->email], 200);
        } catch (\Exception $e) {
            // Return error response if sending email fails
            return response()->json([
                'message' => 'Failed to send email.',
                'error' => $e->getMessage() // Include the error message for debugging
            ], 500);
        }
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'profile_url' => 'required|url|max:255'
        ]);

        try {
            $user = User::where('email', $validatedData['email'])->first();

            if ($user) {

                return response()->json(
                    [
                        'email' => $validatedData['email'],
                        'message' => "Email cannot be used, it's already belongs to an account."
                    ],
                    404 // HTTP status code for OK
                );
            }
            $validatedData['password'] = Hash::make($validatedData['password']);

            $user = User::create($validatedData);

            return response()->json([
                'message' => 'User registered successfully!',
                'user' => new UserResource($user)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to register user.',
                'error' => $e->getMessage()
            ], 500);
        }
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
    

    public function logout(Request $request) {}
}
