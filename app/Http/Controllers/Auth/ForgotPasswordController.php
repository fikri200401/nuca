<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Step 1: Send OTP for password reset
     */
    public function sendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Find user by identifier
        $user = User::where(function($query) use ($request) {
            $query->where('whatsapp_number', $request->identifier)
                  ->orWhere('username', $request->identifier)
                  ->orWhere('member_number', $request->identifier);
        })->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Akun tidak ditemukan.',
            ], 404);
        }

        // Check cooldown
        if (!$this->otpService->canResend($user->whatsapp_number, 'reset_password')) {
            $remaining = $this->otpService->getRemainingCooldown($user->whatsapp_number, 'reset_password');
            
            return response()->json([
                'success' => false,
                'message' => "Silakan tunggu {$remaining} detik sebelum mengirim ulang OTP.",
            ], 429);
        }

        // Generate and send OTP
        $result = $this->otpService->generateAndSend($user->whatsapp_number, 'reset_password');

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Kode OTP telah dikirim ke WhatsApp Anda.',
                'whatsapp_number' => $user->whatsapp_number,
                'expires_at' => $result['expires_at'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengirim OTP. Silakan coba lagi.',
        ], 500);
    }

    /**
     * Step 2: Verify OTP
     */
    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'whatsapp_number' => ['required', 'string'],
            'otp_code' => ['required', 'string', 'size:6'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->otpService->verify(
            $request->whatsapp_number,
            $request->otp_code,
            'reset_password'
        );

        return response()->json($result);
    }

    /**
     * Step 3: Reset password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'whatsapp_number' => ['required', 'string'],
            'otp_code' => ['required', 'string', 'size:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Verify OTP
        $otpResult = $this->otpService->verify(
            $request->whatsapp_number,
            $request->otp_code,
            'reset_password'
        );

        if (!$otpResult['success']) {
            return response()->json([
                'success' => false,
                'message' => 'OTP tidak valid atau sudah kadaluarsa.',
            ], 422);
        }

        // Find user and update password
        $user = User::where('whatsapp_number', $request->whatsapp_number)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Akun tidak ditemukan.',
            ], 404);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset! Silakan login dengan password baru.',
            'redirect' => route('login'),
        ]);
    }
}
