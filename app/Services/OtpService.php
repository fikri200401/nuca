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
        // Check cooldown (60 detik)
        if (!$this->canResend($whatsappNumber, $purpose)) {
            $remainingSeconds = $this->getRemainingCooldown($whatsappNumber, $purpose);
            return [
                'success' => false,
                'message' => "Tunggu {$remainingSeconds} detik sebelum mengirim ulang OTP.",
                'remaining_seconds' => $remainingSeconds,
            ];
        }

        // Generate 6 digit OTP
        $otpCode = $this->generateOTPCode();

        // Invalidate previous OTP with same purpose
        OtpVerification::where('whatsapp_number', $whatsappNumber)
            ->where('purpose', $purpose)
            ->where('verified', false)
            ->update(['verified' => true]); // Mark old as used

        // Save to database
        $otp = OtpVerification::create([
            'whatsapp_number' => $whatsappNumber,
            'otp_code' => $otpCode,
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes(10), // 10 menit masa aktif
            'attempts' => 0,
            'verified' => false,
            'last_resend_at' => now(),
        ]);

        // Send via WhatsApp
        $sent = $this->whatsappService->sendOTP($whatsappNumber, $otpCode, $purpose);

        return [
            'success' => $sent,
            'otp_id' => $otp->id,
            'expires_at' => $otp->expires_at,
            'message' => 'OTP berhasil dikirim ke WhatsApp Anda.',
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
     * Check if can resend OTP (cooldown 60 detik)
     */
    public function canResend($whatsappNumber, $purpose)
    {
        $lastOtp = OtpVerification::where('whatsapp_number', $whatsappNumber)
            ->where('purpose', $purpose)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$lastOtp || !$lastOtp->last_resend_at) {
            return true;
        }

        // Cooldown 60 detik
        $cooldownSeconds = 60;
        $secondsSinceLastResend = now()->diffInSeconds($lastOtp->last_resend_at);

        return $secondsSinceLastResend >= $cooldownSeconds;
    }

    /**
     * Get remaining cooldown time in seconds
     */
    public function getRemainingCooldown($whatsappNumber, $purpose)
    {
        $lastOtp = OtpVerification::where('whatsapp_number', $whatsappNumber)
            ->where('purpose', $purpose)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$lastOtp || !$lastOtp->last_resend_at) {
            return 0;
        }

        $cooldownSeconds = 60;
        $secondsSinceLastResend = now()->diffInSeconds($lastOtp->last_resend_at);
        $remaining = $cooldownSeconds - $secondsSinceLastResend;

        return max(0, $remaining);
    }
}
