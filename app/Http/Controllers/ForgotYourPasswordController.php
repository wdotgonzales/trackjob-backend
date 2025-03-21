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
    public function checkIfEmailBelongsToAnAccount(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
        ]);

        $email = $validatedData['email'];

        try {
            // Attempt to find a user by the provided email address
            $user = User::where('email', $email)->first();

            // If user is not found, return a 404 response
            if (!$user) {
                return response()->json(
                    [
                        'email' => $email,
                        'user' => null,
                        'message' => 'Email does not belong to any account.'
                    ],
                    404 // HTTP status code for Not Found
                );
            }

            // If user is found, return user details using a resource wrapper
            return response()->json(
                [
                    'email' => $email,
                    'user' => new UserResource($user), // Format the user data using UserResource
                    'message' => 'Email belongs to an account.'
                ],
                200 // HTTP status code for OK
            );
        } catch (\Exception $e) {
            // Handle any unexpected errors during the query
            return response()->json(
                [
                    'email' => $email,
                    'user' => null,
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
                'user' => null,
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
                'user' => null,
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


    // -- REVISE THIS CHECK IF OTP IS STILL VALID via start_date and end_data.
    public function validateOtp(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'otp' => 'required|string|min:6|max:6',
            'email' => 'required|email',
        ]);

        // Extract validated inputs
        $otp = $validatedData['otp'];
        $email = $validatedData['email'];

        // Check if an OTP exists for the given email
        $existingOtp = VerificationCode::where('email', $email)->first();

        if (!$existingOtp) {
            return response()->json(['message' => 'Invalid OTP or email.'], 400);
        }

        // Validate if the OTP matches the one in the database
        if ($existingOtp->otp !== $otp) {
            return response()->json(['message' => 'Invalid OTP.'], 400);
        }

        if (Carbon::now()->setTimezone('Asia/Manila')->greaterThan(Carbon::parse($existingOtp->expiration_date)->setTimezone('Asia/Manila'))) {
            return response()->json(['message' => 'OTP has expired.'], 400);
        }

        return response()->json([
            'message' => 'OTP is valid.',
            'otp' => $otp,
            'email' => $email,

        ], 200);
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
