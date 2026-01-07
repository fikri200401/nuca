@extends('layouts.base')

@section('title', 'Daftar Akun Baru')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Daftar Akun Baru
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Atau
                <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    login ke akun Anda
                </a>
            </p>
        </div>

        <div id="registerApp" class="mt-8 space-y-6">
            <!-- Step Indicator -->
            <div class="flex items-center justify-center space-x-2">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium" :class="step >= 1 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-600'">1</div>
                    <span class="ml-2 text-xs font-medium" :class="step >= 1 ? 'text-indigo-600' : 'text-gray-500'">WhatsApp</span>
                </div>
                <div class="w-8 h-0.5" :class="step >= 2 ? 'bg-indigo-600' : 'bg-gray-200'"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium" :class="step >= 2 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-600'">2</div>
                    <span class="ml-2 text-xs font-medium" :class="step >= 2 ? 'text-indigo-600' : 'text-gray-500'">OTP</span>
                </div>
                <div class="w-8 h-0.5" :class="step >= 3 ? 'bg-indigo-600' : 'bg-gray-200'"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium" :class="step >= 3 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-600'">3</div>
                    <span class="ml-2 text-xs font-medium" :class="step >= 3 ? 'text-indigo-600' : 'text-gray-500'">Data</span>
                </div>
            </div>

            <!-- Alert Messages -->
            <div v-if="errorMessage" class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded" role="alert">
                <span v-text="errorMessage"></span>
            </div>
            <div v-if="successMessage" class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded" role="alert">
                <span v-text="successMessage"></span>
            </div>

            <!-- Step 1: WhatsApp Number -->
            <form v-if="step === 1" @submit.prevent="sendOtp" class="space-y-4">
                <div>
                    <label for="whatsapp_number" class="block text-sm font-medium text-gray-700">Nomor WhatsApp</label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">+62</span>
                        <input type="text" v-model="whatsappNumber" id="whatsapp_number" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" placeholder="81234567890" @input="whatsappNumber = whatsappNumber.replace(/[^0-9]/g, '')" required>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Contoh: 81234567890 (tanpa +62 atau 0)</p>
                </div>
                <button type="submit" :disabled="loading || !whatsappNumber" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                    <span v-if="loading">Mengirim...</span>
                    <span v-else>Kirim OTP</span>
                </button>
            </form>

            <!-- Step 2: OTP Verification -->
            <form v-if="step === 2" @submit.prevent="verifyOtp" class="space-y-4">
                <div>
                    <label for="otp_code" class="block text-sm font-medium text-gray-700">Kode OTP</label>
                    <p class="text-sm text-gray-600 mb-2">Kode OTP telah dikirim ke <strong>+62@{{ whatsappNumber }}</strong></p>
                    <input type="text" v-model="otpCode" id="otp_code" maxlength="6" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-center text-2xl tracking-widest focus:ring-indigo-500 focus:border-indigo-500" placeholder="123456" @input="otpCode = otpCode.replace(/[^0-9]/g, '')" required>
                </div>
                <div class="text-center">
                    <button type="button" @click="resendOtp" :disabled="!canResend || loading" class="text-sm text-indigo-600 hover:text-indigo-500 disabled:text-gray-400">
                        <span v-if="!canResend">Kirim ulang dalam @{{ countdown }}s</span>
                        <span v-else>Kirim Ulang OTP</span>
                    </button>
                </div>
                <div class="flex space-x-2">
                    <button type="button" @click="step = 1" class="flex-1 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Kembali</button>
                    <button type="submit" :disabled="loading || otpCode.length !== 6" class="flex-1 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50">
                        <span v-if="loading">Memverifikasi...</span>
                        <span v-else>Verifikasi</span>
                    </button>
                </div>
            </form>

            <!-- Step 3: Complete Registration -->
            <form v-if="step === 3" @submit.prevent="register" class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap *</label>
                    <input type="text" v-model="formData.name" id="name" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username *</label>
                    <input type="text" v-model="formData.username" id="username" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                    <input type="email" v-model="formData.email" id="email" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password * (min. 8 karakter)</label>
                    <input type="password" v-model="formData.password" id="password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password *</label>
                    <input type="password" v-model="formData.password_confirmation" id="password_confirmation" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                        <select v-model="formData.gender" id="gender" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Pilih</option>
                            <option value="male">Laki-laki</option>
                            <option value="female">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                        <input type="date" v-model="formData.birth_date" id="birth_date" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea v-model="formData.address" id="address" rows="2" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
                <div class="flex space-x-2">
                    <button type="button" @click="step = 1" class="flex-1 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Kembali</button>
                    <button type="submit" :disabled="loading" class="flex-1 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50">
                        <span v-if="loading">Mendaftar...</span>
                        <span v-else>Daftar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.js"></script>
<script>
const { createApp } = Vue;
createApp({
    data() {
        return {
            step: 1,
            whatsappNumber: '',
            otpCode: '',
            loading: false,
            errorMessage: '',
            successMessage: '',
            canResend: true,
            countdown: 60,
            countdownInterval: null,
            formData: {
                name: '',
                username: '',
                email: '',
                password: '',
                password_confirmation: '',
                gender: '',
                birth_date: '',
                address: ''
            }
        }
    },
    methods: {
        async sendOtp() {
            this.loading = true;
            this.errorMessage = '';
            this.successMessage = '';
            try {
                const response = await fetch('{{ route("register.send-otp") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ whatsapp_number: this.whatsappNumber })
                });
                const data = await response.json();
                if (response.ok) {
                    this.step = 2;
                    this.successMessage = data.message;
                    this.startCountdown();
                } else {
                    this.errorMessage = data.message || 'Terjadi kesalahan';
                }
            } catch (error) {
                this.errorMessage = 'Gagal mengirim OTP';
            } finally {
                this.loading = false;
            }
        },
        async resendOtp() {
            await this.sendOtp();
        },
        startCountdown() {
            this.canResend = false;
            this.countdown = 60;
            if (this.countdownInterval) clearInterval(this.countdownInterval);
            this.countdownInterval = setInterval(() => {
                this.countdown--;
                if (this.countdown <= 0) {
                    clearInterval(this.countdownInterval);
                    this.canResend = true;
                }
            }, 1000);
        },
        async verifyOtp() {
            this.loading = true;
            this.errorMessage = '';
            try {
                const response = await fetch('{{ route("register.verify-otp") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        whatsapp_number: this.whatsappNumber,
                        otp_code: this.otpCode
                    })
                });
                const data = await response.json();
                if (response.ok) {
                    this.step = 3;
                    this.successMessage = 'OTP berhasil diverifikasi!';
                    if (this.countdownInterval) clearInterval(this.countdownInterval);
                } else {
                    this.errorMessage = data.message || 'Kode OTP salah';
                }
            } catch (error) {
                this.errorMessage = 'Gagal memverifikasi OTP';
            } finally {
                this.loading = false;
            }
        },
        async register() {
            this.loading = true;
            this.errorMessage = '';
            this.successMessage = '';
            try {
                const response = await fetch('{{ route("register") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        whatsapp_number: this.whatsappNumber,
                        ...this.formData
                    })
                });
                const data = await response.json();
                console.log('Register response:', data);
                
                if (response.ok) {
                    this.successMessage = 'Registrasi berhasil! Mengalihkan...';
                    setTimeout(() => {
                        window.location.href = data.redirect || '{{ route("customer.dashboard") }}';
                    }, 1500);
                } else {
                    this.errorMessage = data.message || 'Gagal mendaftar';
                    if (data.errors) {
                        this.errorMessage = Object.values(data.errors).flat().join(', ');
                    }
                }
            } catch (error) {
                console.error('Register error:', error);
                this.errorMessage = 'Terjadi kesalahan: ' + error.message;
            } finally {
                this.loading = false;
            }
        }
    },
    beforeUnmount() {
        if (this.countdownInterval) clearInterval(this.countdownInterval);
    }
}).mount('#registerApp');
</script>
@endsection
