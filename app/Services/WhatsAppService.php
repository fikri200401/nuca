<?php

namespace App\Services;

use App\Models\OtpVerification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $apiUrl;
    protected $apiKey;

    public function __construct()
    {
        // Bisa pakai Fonnte, Wablas, atau service WhatsApp lainnya
        $this->apiUrl = config('services.whatsapp.api_url', 'https://api.fonnte.com/send');
        $this->apiKey = config('services.whatsapp.api_key', '');
    }

    /**
     * Send OTP via WhatsApp
     */
    public function sendOTP($whatsappNumber, $otpCode, $purpose = 'verification')
    {
        $message = $this->formatOTPMessage($otpCode, $purpose);
        
        return $this->sendMessage($whatsappNumber, $message);
    }

    /**
     * Send booking confirmation
     */
    public function sendBookingConfirmation($booking)
    {
        $user = $booking->user;
        $treatment = $booking->treatment;
        $doctor = $booking->doctor;

        $message = "*KONFIRMASI BOOKING* 🎉\n\n";
        $message .= "Halo {$user->name},\n\n";
        $message .= "Booking Anda telah dikonfirmasi!\n\n";
        $message .= "*Detail Booking:*\n";
        $message .= "📋 Kode: {$booking->booking_code}\n";
        $message .= "💆 Treatment: {$treatment->name}\n";
        $message .= "👨‍⚕️ Dokter: {$doctor->name}\n";
        $message .= "📅 Tanggal: " . \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') . "\n";
        $message .= "🕐 Jam: {$booking->booking_time}\n";
        $message .= "💰 Total: Rp " . number_format($booking->final_price, 0, ',', '.') . "\n\n";
        $message .= "Terima kasih! 😊";

        return $this->sendMessage($user->whatsapp_number, $message);
    }

    /**
     * Send booking reminder (H-1)
     */
    public function sendBookingReminder($booking)
    {
        $user = $booking->user;
        $treatment = $booking->treatment;

        $message = "*REMINDER BOOKING* ⏰\n\n";
        $message .= "Halo {$user->name},\n\n";
        $message .= "Mengingatkan booking Anda besok:\n\n";
        $message .= "💆 Treatment: {$treatment->name}\n";
        $message .= "📅 Tanggal: " . \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') . "\n";
        $message .= "🕐 Jam: {$booking->booking_time}\n\n";
        $message .= "Sampai jumpa besok! 👋";

        return $this->sendMessage($user->whatsapp_number, $message);
    }

    /**
     * Send deposit waiting notification
     */
    public function sendDepositWaiting($booking, $deposit)
    {
        $user = $booking->user;

        $message = "*MENUNGGU PEMBAYARAN DP* 💳\n\n";
        $message .= "Halo {$user->name},\n\n";
        $message .= "Booking Anda memerlukan DP sebesar:\n";
        $message .= "💰 Rp " . number_format($deposit->amount, 0, ',', '.') . "\n\n";
        $message .= "⏰ *Batas waktu:* " . $deposit->deadline_at->format('d/m/Y H:i') . "\n";
        $message .= "(24 jam dari sekarang)\n\n";
        $message .= "Silakan transfer dan upload bukti pembayaran melalui website.\n\n";
        $message .= "Terima kasih! 😊";

        return $this->sendMessage($user->whatsapp_number, $message);
    }

    /**
     * Send deposit approved notification
     */
    public function sendDepositApproved($booking)
    {
        $user = $booking->user;

        $message = "*DP DISETUJUI* ✅\n\n";
        $message .= "Halo {$user->name},\n\n";
        $message .= "DP Anda telah diverifikasi dan disetujui!\n\n";
        $message .= "Booking Anda terkonfirmasi untuk:\n";
        $message .= "📅 " . \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') . "\n";
        $message .= "🕐 {$booking->booking_time}\n\n";
        $message .= "Sampai jumpa! 👋";

        return $this->sendMessage($user->whatsapp_number, $message);
    }

    /**
     * Send deposit rejected notification
     */
    public function sendDepositRejected($booking, $deposit)
    {
        $user = $booking->user;

        $message = "*DP DITOLAK* ❌\n\n";
        $message .= "Halo {$user->name},\n\n";
        $message .= "Maaf, DP Anda ditolak.\n\n";
        
        if ($deposit->rejection_reason) {
            $message .= "*Alasan:* {$deposit->rejection_reason}\n\n";
        }
        
        $message .= "Silakan upload ulang bukti pembayaran yang benar.\n\n";
        $message .= "Terima kasih!";

        return $this->sendMessage($user->whatsapp_number, $message);
    }

    /**
     * Send deposit expired notification
     */
    public function sendDepositExpired($booking)
    {
        $user = $booking->user;

        $message = "*BOOKING EXPIRED* ⏰\n\n";
        $message .= "Halo {$user->name},\n\n";
        $message .= "Booking Anda telah expired karena DP tidak dikonfirmasi dalam 24 jam.\n\n";
        $message .= "Silakan buat booking baru jika masih berminat.\n\n";
        $message .= "Terima kasih!";

        return $this->sendMessage($user->whatsapp_number, $message);
    }

    /**
     * Send message via WhatsApp API
     */
    protected function sendMessage($phoneNumber, $message)
    {
        try {
            // Format nomor (pastikan dimulai dengan 62)
            $phoneNumber = $this->formatPhoneNumber($phoneNumber);

            // Kirim via Fonnte (sesuaikan dengan API yang digunakan)
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->post($this->apiUrl, [
                'target' => $phoneNumber,
                'message' => $message,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp sent successfully', [
                    'phone' => $phoneNumber,
                    'message' => $message
                ]);
                return true;
            }

            Log::error('WhatsApp send failed', [
                'phone' => $phoneNumber,
                'response' => $response->body()
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('WhatsApp send error', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Format OTP message
     */
    protected function formatOTPMessage($otpCode, $purpose)
    {
        $purposeText = match($purpose) {
            'register' => 'pendaftaran akun',
            'login' => 'login',
            'reset_password' => 'reset password',
            default => 'verifikasi'
        };

        $message = "*KODE OTP VERIFIKASI* 🔐\n\n";
        $message .= "Kode OTP untuk {$purposeText}:\n\n";
        $message .= "*{$otpCode}*\n\n";
        $message .= "Kode ini berlaku 10 menit.\n";
        $message .= "Jangan berikan kode ini kepada siapapun!\n\n";
        $message .= "Terima kasih 😊";

        return $message;
    }

    /**
     * Format phone number to international format
     */
    protected function formatPhoneNumber($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // If starts with 0, replace with 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        // If doesn't start with 62, add it
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
