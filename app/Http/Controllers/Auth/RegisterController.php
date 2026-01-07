<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OtpVerification;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Step 1: Send OTP to WhatsApp
     */
    public function sendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'whatsapp_number' => ['required', 'string', 'regex:/^[0-9]{10,15}$/', 'unique:users,whatsapp_number'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check cooldown
        if (!$this->otpService->canResend($request->whatsapp_number, 'register')) {
            $remaining = $this->otpService->getRemainingCooldown($request->whatsapp_number, 'register');
            
            return response()->json([
                'success' => false,
                'message' => "Silakan tunggu {$remaining} detik sebelum mengirim ulang OTP.",
            ], 429);
        }

        // Generate and send OTP
        $result = $this->otpService->generateAndSend($request->whatsapp_number, 'register');

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Kode OTP telah dikirim ke WhatsApp Anda.',
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
            'register'
        );

        return response()->json($result);
    }

    /**
     * Step 3: Complete registration with password
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'whatsapp_number' => ['required', 'string', 'unique:users,whatsapp_number'],
                'name' => ['required', 'string', 'max:255'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'email' => ['nullable', 'email', 'unique:users,email'],
                'username' => ['nullable', 'string', 'unique:users,username', 'max:50'],
                'birth_date' => ['nullable', 'date'],
                'gender' => ['nullable', 'in:male,female'],
                'address' => ['nullable', 'string'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Check if OTP was verified for this number (within last 10 minutes)
            $otpVerification = OtpVerification::where('whatsapp_number', $request->whatsapp_number)
                ->where('purpose', 'register')
                ->where('verified', true)
                ->where('expires_at', '>', now())
                ->first();

            if (!$otpVerification) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP belum diverifikasi atau sudah kadaluarsa. Silakan ulangi proses registrasi.',
                ], 422);
            }

            // Create user
            $user = User::create([
                'name' => $request->name,
                'whatsapp_number' => $request->whatsapp_number,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'address' => $request->address,
                'role' => 'customer',
            ]);

            // Auto login
            auth()->login($user);

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil!',
                'redirect' => route('customer.dashboard'),
            ]);
        } catch (\Exception $e) {
            \Log::error('Registration error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
