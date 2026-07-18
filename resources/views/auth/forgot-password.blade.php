<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lupa Password - Nuca Aesthetic Clinic</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-pink-100 via-purple-50 to-blue-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Logo -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent mb-2">Nuca Aesthetic</h1>
            <p class="text-gray-600">Reset Password Anda</p>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8 border border-pink-100">
            <!-- Alert Messages -->
            <div id="alertMessage" class="hidden mb-6 p-4 rounded-xl"></div>

            <!-- Step 1: Enter Identifier -->
            <div id="step1" class="step">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Lupa Password?</h2>
                    <p class="text-gray-600 text-sm">Masukkan nomor WhatsApp, username, atau nomor member Anda</p>
                </div>

                <form id="identifierForm">
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">WhatsApp / Username / Member</label>
                        <input type="text" id="identifier" name="identifier" 
                            class="w-full px-4 py-3 border-2 border-pink-200 rounded-xl focus:outline-none focus:border-pink-500 transition"
                            placeholder="08123456789 / username / NUC001" required>
                    </div>

                    <button type="submit" id="sendOtpBtn"
                        class="w-full px-6 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:from-pink-600 hover:to-purple-700 transition-all duration-200 transform hover:-translate-y-0.5">
                        Kirim Kode OTP
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="text-pink-600 hover:text-pink-700 font-semibold text-sm">
                        Kembali ke Login
                    </a>
                </div>
            </div>

            <!-- Step 2: Verify OTP & Reset Password -->
            <div id="step2" class="step hidden">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Verifikasi OTP</h2>
                    <p class="text-gray-600 text-sm">Kode OTP telah dikirim ke WhatsApp: <span id="whatsappNumber" class="font-semibold"></span></p>
                </div>

                <form id="resetForm">
                    <input type="hidden" id="whatsapp_number" name="whatsapp_number">

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kode OTP</label>
                        <input type="text" id="otp_code" name="otp_code" maxlength="6"
                            class="w-full px-4 py-3 border-2 border-pink-200 rounded-xl focus:outline-none focus:border-pink-500 transition text-center text-2xl font-bold tracking-widest"
                            placeholder="000000" required>
                        <p class="text-xs text-gray-500 mt-1">Berlaku selama <span id="otpTimer" class="font-semibold text-pink-600">10:00</span></p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Password Baru</label>
                        <input type="password" id="password" name="password" minlength="8"
                            class="w-full px-4 py-3 border-2 border-pink-200 rounded-xl focus:outline-none focus:border-pink-500 transition"
                            placeholder="Minimal 8 karakter" required>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" minlength="8"
                            class="w-full px-4 py-3 border-2 border-pink-200 rounded-xl focus:outline-none focus:border-pink-500 transition"
                            placeholder="Ketik ulang password" required>
                    </div>

                    <button type="submit" id="resetPasswordBtn"
                        class="w-full px-6 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:from-pink-600 hover:to-purple-700 transition-all duration-200 transform hover:-translate-y-0.5">
                        Reset Password
                    </button>

                    <button type="button" id="resendOtpBtn"
                        class="w-full mt-3 px-6 py-3 border-2 border-pink-300 text-gray-700 font-semibold rounded-xl hover:bg-pink-50 transition">
                        Kirim Ulang OTP
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <button onclick="backToStep1()" class="text-pink-600 hover:text-pink-700 font-semibold text-sm">
                        Gunakan Akun Lain
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let otpCountdown;
        let canResend = false;

        // Step 1: Send OTP
        document.getElementById('identifierForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const identifier = document.getElementById('identifier').value;
            const btn = document.getElementById('sendOtpBtn');
            
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

            try {
                const response = await fetch('{{ route("forgot-password.send-otp") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ identifier })
                });

                const data = await response.json();

                if (data.success) {
                    showAlert('success', data.message);
                    document.getElementById('whatsapp_number').value = data.whatsapp_number;
                    document.getElementById('whatsappNumber').textContent = data.whatsapp_number;
                    showStep2();
                    startOtpTimer(600); // 10 minutes
                } else {
                    showAlert('error', data.message);
                }
            } catch (error) {
                showAlert('error', 'Terjadi kesalahan. Silakan coba lagi.');
            }

            btn.disabled = false;
            btn.innerHTML = 'Kirim Kode OTP';
        });

        // Step 2: Reset Password
        document.getElementById('resetForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            const password_confirmation = document.getElementById('password_confirmation').value;

            if (password !== password_confirmation) {
                showAlert('error', 'Password dan konfirmasi password tidak sama.');
                return;
            }

            const formData = {
                whatsapp_number: document.getElementById('whatsapp_number').value,
                otp_code: document.getElementById('otp_code').value,
                password: password,
                password_confirmation: password_confirmation
            };

            const btn = document.getElementById('resetPasswordBtn');
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

            try {
                const response = await fetch('{{ route("forgot-password.reset") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1500);
                } else {
                    showAlert('error', data.message);
                    btn.disabled = false;
                    btn.innerHTML = 'Reset Password';
                }
            } catch (error) {
                showAlert('error', 'Terjadi kesalahan. Silakan coba lagi.');
                btn.disabled = false;
                btn.innerHTML = 'Reset Password';
            }
        });

        // Resend OTP
        document.getElementById('resendOtpBtn').addEventListener('click', async function() {
            if (!canResend) {
                showAlert('error', 'Silakan tunggu hingga timer habis.');
                return;
            }

            const identifier = document.getElementById('whatsapp_number').value;
            const btn = this;
            
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

            try {
                const response = await fetch('{{ route("forgot-password.send-otp") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ identifier })
                });

                const data = await response.json();

                if (data.success) {
                    showAlert('success', 'Kode OTP baru telah dikirim!');
                    startOtpTimer(600);
                    document.getElementById('otp_code').value = '';
                } else {
                    showAlert('error', data.message);
                }
            } catch (error) {
                showAlert('error', 'Terjadi kesalahan. Silakan coba lagi.');
            }

            btn.disabled = false;
            btn.innerHTML = 'Kirim Ulang OTP';
        });

        function showStep2() {
            document.getElementById('step1').classList.add('hidden');
            document.getElementById('step2').classList.remove('hidden');
        }

        function backToStep1() {
            clearInterval(otpCountdown);
            document.getElementById('step2').classList.add('hidden');
            document.getElementById('step1').classList.remove('hidden');
            document.getElementById('identifierForm').reset();
            document.getElementById('resetForm').reset();
        }

        function showAlert(type, message) {
            const alert = document.getElementById('alertMessage');
            alert.className = `mb-6 p-4 rounded-xl ${type === 'success' ? 'bg-green-50 border-l-4 border-green-400' : 'bg-red-50 border-l-4 border-red-400'}`;
            alert.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-6 h-6 ${type === 'success' ? 'text-green-400' : 'text-red-400'} mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${type === 'success' ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'}"/>
                    </svg>
                    <p class="text-sm font-medium ${type === 'success' ? 'text-green-800' : 'text-red-800'}">${message}</p>
                </div>
            `;
            alert.classList.remove('hidden');

            setTimeout(() => {
                alert.classList.add('hidden');
            }, 5000);
        }

        function startOtpTimer(seconds) {
            canResend = false;
            let remaining = seconds;
            const timerElement = document.getElementById('otpTimer');

            clearInterval(otpCountdown);
            otpCountdown = setInterval(() => {
                const minutes = Math.floor(remaining / 60);
                const secs = remaining % 60;
                timerElement.textContent = `${minutes}:${secs.toString().padStart(2, '0')}`;
                
                remaining--;

                if (remaining < 0) {
                    clearInterval(otpCountdown);
                    canResend = true;
                    timerElement.textContent = 'Habis';
                    timerElement.classList.add('text-red-600');
                }
            }, 1000);
        }
    </script>
</body>
</html>
