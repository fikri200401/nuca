<?php

namespace App\Services;

use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Support\Str;

class OtpService
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Generate and send OTP
     */
    public function generateAndSend($whatsappNumber, $purpose = 'register')
    {
        // Generate 6 digit OTP
        $otpCode = $this->generateOTPCode();

        // Save to database
        $otp = OtpVerification::create([
            'whatsapp_number' => $whatsappNumber,
            'otp_code' => $otpCode,
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes(10), // 10 menit
            'attempts' => 0,
            'verified' => false,
        ]);

        // Send via WhatsApp
        $sent = $this->whatsappService->sendOTP($whatsappNumber, $otpCode, $purpose);

        return [
            'success' => $sent,
            'otp_id' => $otp->id,
            'expires_at' => $otp->expires_at,
        ];
    }

    /**
     * Verify OTP
     */
    public function verify($whatsappNumber, $otpCode, $purpose)
    {
        $otp = OtpVerification::where('whatsapp_number', $whatsappNumber)
            ->where('purpose', $purpose)
            ->where('verified', false)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$otp) {
            return [
                'success' => false,
                'message' => 'Kode OTP tidak ditemukan atau sudah digunakan.',
            ];
        }

        // Check if expired
        if ($otp->isExpired()) {
            return [
                'success' => false,
                'message' => 'Kode OTP sudah kadaluarsa.',
            ];
        }

        // Check attempts
        if ($otp->attempts >= 5) {
            return [
                'success' => false,
                'message' => 'Terlalu banyak percobaan. Silakan minta kode OTP baru.',
            ];
        }

        // Verify code
        if ($otp->otp_code !== $otpCode) {
            $otp->incrementAttempts();
            
            return [
                'success' => false,
                'message' => 'Kode OTP salah. Sisa percobaan: ' . (5 - $otp->attempts),
            ];
        }

        // Mark as verified
        $otp->markAsVerified();

        return [
            'success' => true,
            'message' => 'Verifikasi berhasil!',
        ];
    }

    /**
     * Generate 6 digit OTP code
     */
    protected function generateOTPCode()
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Check if can resend OTP (no cooldown for development)
     */
    public function canResend($whatsappNumber, $purpose)
    {
        return true; // Always allow resend
    }

    /**
     * Get remaining cooldown time
     */
    public function getRemainingCooldown($whatsappNumber, $purpose)
    {
        return 0; // No cooldown
    }
}
