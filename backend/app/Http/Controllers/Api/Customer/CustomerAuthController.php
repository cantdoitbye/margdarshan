<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\OtpVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CustomerAuthController extends Controller
{
    /**
     * Send OTP to customer's phone
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|regex:/^[0-9]{10}$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $phone = $request->phone;

        // Check rate limiting (max 3 OTP requests in 15 minutes)
        $recentOtps = OtpVerification::where('phone', $phone)
            ->where('created_at', '>=', Carbon::now()->subMinutes(15))
            ->count();

        // if ($recentOtps >= 3) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Too many OTP requests. Please try again after 15 minutes.',
        //     ], 429);
        // }

        // Generate 6-digit OTP
        // $otp = sprintf("%06d", mt_rand(1, 999999));
        $otp = '123456';

        // Store OTP in database
        OtpVerification::create([
            'phone' => $phone,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        // For development: Log OTP
        Log::info("OTP for {$phone}: {$otp}");

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'data' => [
                'phone' => $phone,
                'expires_in_seconds' => 300, // 5 minutes
                // TESTING ONLY: Include OTP in response
                'otp' => $otp,
            ]
        ]);
    }

    /**
     * Verify OTP and login/register customer
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|regex:/^[0-9]{10}$/',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $phone = $request->phone;
        $otp = $request->otp;

        // Find the latest OTP for this phone
        $otpRecord = OtpVerification::where('phone', $phone)
            ->where('otp', $otp)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP',
            ], 401);
        }

        // Check if OTP is expired
        if ($otpRecord->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'OTP has expired. Please request a new one.',
            ], 401);
        }

        // Check max attempts
        if ($otpRecord->attempts >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Maximum verification attempts exceeded. Please request a new OTP.',
            ], 401);
        }

        // Mark OTP as verified
        $otpRecord->markAsVerified();

        // Find or create customer
        $customer = Customer::firstOrCreate(
            ['phone' => $phone],
            ['phone_verified_at' => now(), 'status' => 'active']
        );

        // If customer exists, mark phone as verified
        if (!$customer->hasVerifiedPhone()) {
            $customer->markPhoneAsVerified();
        }

        // Generate Sanctum token
        $token = $customer->createToken('customer-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'customer' => $customer,
                'token' => $token,
            ]
        ]);
    }

    /**
     * Get authenticated customer details
     */
    public function me(Request $request)
    {
        $customer = $request->user()->load('students');

        return response()->json([
            'success' => true,
            'data' => $customer
        ]);
    }

    /**
     * Logout customer
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}
