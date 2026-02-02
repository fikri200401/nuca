@extends('layouts.admin')

@section('title', 'Konfigurasi WhatsApp')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
            Konfigurasi WhatsApp API
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            Kelola integrasi WhatsApp menggunakan Fonnte API untuk notifikasi otomatis
        </p>
    </div>

    @if(session('success'))
    <div class="mb-6 rounded-md bg-green-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Shop Open/Close Toggle -->
    <div class="mb-6 bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">
                        Status Toko
                    </h3>
                    <p class="mt-1 text-sm {{ \App\Models\Setting::get('is_shop_open', true) ? 'text-green-600' : 'text-red-600' }} font-semibold" id="shopStatusText">
                        Toko saat ini: {{ \App\Models\Setting::get('is_shop_open', true) ? 'BUKA' : 'TUTUP' }}
                    </p>
                    <p class="mt-1 text-xs text-gray-400">
                        Jika toko ditutup, customer tidak bisa membuat booking baru
                    </p>
                </div>
                <button type="button" id="toggleShopBtn" data-is-open="{{ \App\Models\Setting::get('is_shop_open', true) ? '1' : '0' }}" class="{{ \App\Models\Setting::get('is_shop_open', true) ? 'bg-green-600' : 'bg-gray-200' }} relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <span class="sr-only">Toggle shop status</span>
                    <span class="{{ \App\Models\Setting::get('is_shop_open', true) ? 'translate-x-5' : 'translate-x-0' }} pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                </button>
            </div>
        </div>
    </div>

    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Informasi API</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Konfigurasi API Key dan Device dari Fonnte untuk mengirim notifikasi WhatsApp ke customer.
                </p>
                <div class="mt-4 p-4 bg-blue-50 rounded-md">
                    <p class="text-sm text-blue-800 font-medium">📌 Cara mendapatkan API Key:</p>
                    <ol class="mt-2 text-sm text-blue-700 list-decimal list-inside space-y-1">
                        <li>Daftar di <a href="https://fonnte.com" target="_blank" class="underline">fonnte.com</a></li>
                        <li>Login ke dashboard, pilih menu device</li>
                        <li>Add Device, isi formnya, lalu klik connect</li>
                        <li>Salin API Key dari button Token</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                <div class="shadow sm:rounded-md sm:overflow-hidden">
                    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                        
                        <!-- API Key -->
                        <div>
                            <label for="fonnte_api_key" class="block text-sm font-medium text-gray-700">
                                Fonnte API Key <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <input type="text" 
                                       id="fonnte_api_key" 
                                       name="fonnte_api_key" 
                                       value="{{ old('fonnte_api_key', $settings['fonnte_api_key'] ?? '') }}"
                                       placeholder="zM9K3vmF4uGHdSA1fb2y"
                                       class="flex-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('fonnte_api_key') border-red-300 @enderror">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                API Key dari dashboard Fonnte Anda
                            </p>
                            @error('fonnte_api_key')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Device Name/Number -->
                        <div>
                            <label for="fonnte_device" class="block text-sm font-medium text-gray-700">
                                Nama Device / Nomor WhatsApp
                            </label>
                            <input type="text" 
                                   id="fonnte_device" 
                                   name="fonnte_device" 
                                   value="{{ old('fonnte_device', $settings['fonnte_device'] ?? '') }}"
                                   placeholder="081234567890"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('fonnte_device') border-red-300 @enderror">
                            <p class="mt-2 text-sm text-gray-500">
                                Nomor WhatsApp yang terhubung dengan Fonnte (format internasional, tanpa tanda +, misal 6281234567890)
                            </p>
                            @error('fonnte_device')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Enable WhatsApp -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="whatsapp_enabled" 
                                       name="whatsapp_enabled" 
                                       type="checkbox" 
                                       value="1"
                                       {{ old('whatsapp_enabled', $settings['whatsapp_enabled'] ?? false) ? 'checked' : '' }}
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="whatsapp_enabled" class="font-medium text-gray-700">Aktifkan Notifikasi WhatsApp</label>
                                <p class="text-gray-500">Kirim notifikasi otomatis ke customer via WhatsApp</p>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="pt-4 border-t border-gray-200"></div>

                        <!-- Clinic Information Section -->
                        <div>
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Informasi Klinik</h4>
                            
                            <!-- Address -->
                            <div class="mb-4">
                                <label for="address" class="block text-sm font-medium text-gray-700">
                                    Alamat Klinik
                                </label>
                                <textarea id="address" 
                                       name="address" 
                                       rows="3"
                                       placeholder="Jl. Example No. 123, Jakarta"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('address') border-red-300 @enderror">{{ old('address', $settings['address'] ?? '') }}</textarea>
                                <p class="mt-2 text-sm text-gray-500">
                                    Alamat lengkap klinik yang akan ditampilkan di landing page
                                </p>
                                @error('address')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Google Maps URL -->
                            <div>
                                <label for="google_maps_url" class="block text-sm font-medium text-gray-700">
                                    Link Google Maps
                                </label>
                                <input type="url" 
                                       id="google_maps_url" 
                                       name="google_maps_url" 
                                       value="{{ old('google_maps_url', $settings['google_maps_url'] ?? '') }}"
                                       placeholder="https://maps.google.com/..."
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('google_maps_url') border-red-300 @enderror">
                                <p class="mt-2 text-sm text-gray-500">
                                    URL Google Maps untuk navigasi ke lokasi klinik
                                </p>
                                @error('google_maps_url')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Test Connection -->
                        <div class="pt-4 border-t border-gray-200">
                            <button type="button" 
                                    id="testConnectionBtn"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Test Koneksi
                            </button>
                            <div id="testResult" class="mt-3 hidden"></div>
                        </div>

                        <!-- Info Box -->
                        <div class="rounded-md bg-yellow-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Informasi Penting</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <ul class="list-disc pl-5 space-y-1">
                                            <li>Pastikan nomor WhatsApp sudah terhubung dengan Fonnte</li>
                                            <li>API Key akan disimpan di database dan file .env</li>
                                            <li>Notifikasi akan dikirim untuk: OTP, konfirmasi booking, reminder, dll</li>
                                            <li>Test koneksi sebelum menyimpan untuk memastikan API Key valid</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <button type="submit" 
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Simpan Konfigurasi
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Usage Stats (Optional) -->
    <div class="mt-8">
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Notifikasi yang Dikirim Otomatis
                </h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">OTP Verification</p>
                                <p class="text-xs text-gray-500">Kode OTP untuk registrasi & reset password</p>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Booking Confirmation</p>
                                <p class="text-xs text-gray-500">Konfirmasi booking berhasil dibuat</p>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Booking Reminder</p>
                                <p class="text-xs text-gray-500">Pengingat H-1 sebelum booking</p>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-orange-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Deposit Notification</p>
                                <p class="text-xs text-gray-500">Notifikasi status DP & reminder</p>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Cancellation Notice</p>
                                <p class="text-xs text-gray-500">Pemberitahuan pembatalan booking</p>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Feedback Request</p>
                                <p class="text-xs text-gray-500">Permintaan feedback setelah treatment</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test Connection Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle Shop Status
    const toggleShopBtn = document.getElementById('toggleShopBtn');
    if (toggleShopBtn) {
        toggleShopBtn.addEventListener('click', function() {
            const button = this;
            const isOpen = button.getAttribute('data-is-open') === '1';
            const statusText = document.getElementById('shopStatusText');

            fetch('{{ route("admin.settings.toggle-shop") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const newIsOpen = data.is_open;
                    button.setAttribute('data-is-open', newIsOpen ? '1' : '0');
                    
                    // Update button appearance
                    if (newIsOpen) {
                        button.classList.remove('bg-gray-200');
                        button.classList.add('bg-green-600');
                        button.querySelector('span:last-child').classList.remove('translate-x-0');
                        button.querySelector('span:last-child').classList.add('translate-x-5');
                        statusText.textContent = 'Toko saat ini: BUKA';
                        statusText.classList.remove('text-red-600');
                        statusText.classList.add('text-green-600');
                    } else {
                        button.classList.remove('bg-green-600');
                        button.classList.add('bg-gray-200');
                        button.querySelector('span:last-child').classList.remove('translate-x-5');
                        button.querySelector('span:last-child').classList.add('translate-x-0');
                        statusText.textContent = 'Toko saat ini: TUTUP';
                        statusText.classList.remove('text-green-600');
                        statusText.classList.add('text-red-600');
                    }
                    
                    // Show notification
                    alert(data.message);
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        });
    }

// Test Connection
const testConnectionBtn = document.getElementById('testConnectionBtn');
if (testConnectionBtn) {
    testConnectionBtn.addEventListener('click', function() {
    const apiKey = document.getElementById('fonnte_api_key').value;
    const button = this;
    const resultDiv = document.getElementById('testResult');

    if (!apiKey) {
        resultDiv.className = 'mt-3 rounded-md bg-red-50 p-4';
        resultDiv.innerHTML = `
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">API Key tidak boleh kosong</p>
                </div>
            </div>
        `;
        resultDiv.classList.remove('hidden');
        return;
    }

    // Disable button
    button.disabled = true;
    button.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Testing...';

    // Send AJAX request
    fetch('{{ route("admin.settings.test-connection") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ api_key: apiKey })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.className = 'mt-3 rounded-md bg-green-50 p-4';
            resultDiv.innerHTML = `
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">${data.message}</p>
                    </div>
                </div>
            `;
        } else {
            resultDiv.className = 'mt-3 rounded-md bg-red-50 p-4';
            resultDiv.innerHTML = `
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">${data.message}</p>
                    </div>
                </div>
            `;
        }
        resultDiv.classList.remove('hidden');
    })
    .catch(error => {
        resultDiv.className = 'mt-3 rounded-md bg-red-50 p-4';
        resultDiv.innerHTML = `
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">Error: ${error.message}</p>
                </div>
            </div>
        `;
        resultDiv.classList.remove('hidden');
    })
    .finally(() => {
        // Re-enable button
        button.disabled = false;
        button.innerHTML = `
            <svg class="mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Test Koneksi
        `;
    });
    });
}
});
</script>
@endsection
