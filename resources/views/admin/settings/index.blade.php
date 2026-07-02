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

    @if($errors->any())
    <div class="mb-6 rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                @foreach($errors->all() as $error)
                <p class="text-sm font-medium text-red-800">{{ $error }}</p>
                @endforeach
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
                <button type="button" id="toggleShopBtn" 
                        @cannotDo('settings', 'edit') disabled @endCannotDo
                        data-is-open="{{ \App\Models\Setting::get('is_shop_open', true) ? '1' : '0' }}" class="{{ \App\Models\Setting::get('is_shop_open', true) ? 'bg-green-600' : 'bg-gray-200' }} relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2{{ auth()->user()->canDo('settings','edit') ? '' : ' opacity-50 cursor-not-allowed' }}">
                    <span class="sr-only">Toggle shop status</span>
                    <span class="{{ \App\Models\Setting::get('is_shop_open', true) ? 'translate-x-5' : 'translate-x-0' }} pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                </button>
            </div>
            <div id="shopStatusAlert" class="mt-4 hidden"></div>
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
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
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

                            <!-- Hero Image -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Gambar Banner / Hero Landing Page
                                </label>
                                @php $heroImage = $settings['hero_image'] ?? null; @endphp
                                @if($heroImage)
                                <div class="mb-3">
                                    <img src="{{ Storage::url($heroImage) }}" alt="Hero Image" class="w-full max-h-48 object-cover rounded-md border border-gray-200">
                                    <div class="mt-2 flex items-center gap-3">
                                        <span class="text-xs text-gray-500">Gambar saat ini</span>
                                        <label class="flex items-center gap-1 text-xs text-red-600 cursor-pointer">
                                            <input type="checkbox" name="remove_hero_image" value="1" class="h-3 w-3 text-red-600 border-gray-300 rounded">
                                            Hapus gambar ini
                                        </label>
                                    </div>
                                </div>
                                @endif
                                <input type="file"
                                       id="hero_image"
                                       name="hero_image"
                                       accept="image/jpeg,image/png,image/webp"
                                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 @error('hero_image') border-red-300 @enderror"
                                       onchange="previewHeroImage(this)">
                                <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, WebP. Maks 3 MB. Resolusi disarankan minimal 1200×500px.</p>
                                @error('hero_image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <img id="heroPreview" src="" alt="" class="mt-2 hidden w-full max-h-48 object-cover rounded-md border border-gray-200">
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
                        @canDo('settings', 'edit')
                        <button type="submit" 
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Simpan Konfigurasi
                        </button>
                        @endCanDo
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= PENGATURAN BOOKING ================= --}}
    <div class="mt-10 mb-2">
        <h2 class="text-xl font-bold text-gray-900">Pengaturan Booking</h2>
        <p class="mt-1 text-sm text-gray-500">Semua aturan terkait booking online ada di sini.</p>
    </div>

    <!-- Kebijakan Booking -->
    <div class="mt-4 bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900">Kebijakan Booking</h3>
            <p class="mt-1 text-sm text-gray-500">Atur persetujuan booking online dan kebijakan DP.</p>

            @php
                $bAuto = \App\Models\Setting::get('booking_auto_approval', true);
                $bDepositEnabled = \App\Models\Setting::get('deposit_enabled', true);
                $bThreshold = \App\Models\Setting::get('deposit_threshold_days', 7);
                $bMinDeposit = \App\Models\Setting::get('min_deposit', 50000);
                $bDeadline = \App\Models\Setting::get('deposit_deadline_hours', 24);
            @endphp

            <form action="{{ route('admin.settings.booking-policy') }}" method="POST" class="mt-5 space-y-5">
                @csrf

                {{-- Auto Approval --}}
                <div class="flex items-start p-4 rounded-lg border border-gray-200 bg-gray-50">
                    <div class="flex items-center h-5">
                        <input id="booking_auto_approval" name="booking_auto_approval" type="checkbox" value="1"
                               {{ old('booking_auto_approval', $bAuto ? '1' : '0') ? 'checked' : '' }}
                               class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="booking_auto_approval" class="font-medium text-gray-700">Auto-approve booking untuk hari ini</label>
                        <p class="text-gray-500">
                            Berlaku khusus booking yang <strong>jadwalnya hari ini</strong>. Jika <strong>aktif</strong>, langsung dikonfirmasi otomatis.
                            Jika <strong>dimatikan</strong>, booking jadwal hari ini ditahan (<em>Menunggu Konfirmasi</em>) untuk kamu ACC, cocok saat walk-in ramai.
                            Untuk tanggal lain, atur di <strong>Auto-Approval OFF per Tanggal</strong> di bawah.
                        </p>
                    </div>
                </div>

                {{-- Enable Deposit --}}
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="deposit_enabled" name="deposit_enabled" type="checkbox" value="1"
                               {{ old('deposit_enabled', $bDepositEnabled ? '1' : '0') ? 'checked' : '' }}
                               class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="deposit_enabled" class="font-medium text-gray-700">Aktifkan Kebijakan DP</label>
                        <p class="text-gray-500">Wajibkan DP untuk booking yang jauh dari tanggal sekarang.</p>
                    </div>
                </div>

                {{-- DP parameters --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label for="deposit_threshold_days" class="block text-sm font-medium text-gray-700">Ambang Hari (butuh DP)</label>
                        <input type="number" min="0" max="365" id="deposit_threshold_days" name="deposit_threshold_days"
                               value="{{ old('deposit_threshold_days', $bThreshold) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">Booking >= sekian hari perlu DP.</p>
                    </div>
                    <div>
                        <label for="min_deposit" class="block text-sm font-medium text-gray-700">Nominal DP (Rp)</label>
                        <input type="number" min="0" step="1000" id="min_deposit" name="min_deposit"
                               value="{{ old('min_deposit', $bMinDeposit) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">Minimal DP per booking.</p>
                    </div>
                    <div>
                        <label for="deposit_deadline_hours" class="block text-sm font-medium text-gray-700">Batas Bayar DP (jam)</label>
                        <input type="number" min="1" max="720" id="deposit_deadline_hours" name="deposit_deadline_hours"
                               value="{{ old('deposit_deadline_hours', $bDeadline) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">Sejak booking dibuat.</p>
                    </div>
                </div>

                @canDo('settings', 'edit')
                <div>
                    <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Simpan Kebijakan Booking
                    </button>
                </div>
                @endCanDo
            </form>
        </div>
    </div>

    <!-- Hari Libur & Tutup -->
    <div class="mt-8 bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex flex-wrap items-center gap-2">
                <h3 class="text-lg font-medium text-gray-900">Hari Libur & Tutup</h3>
                <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-700">Tidak bisa dipesan</span>
            </div>
            <p class="mt-1 text-sm text-gray-500">
                Customer tidak bisa membuat booking online pada hari atau tanggal berikut.
            </p>

            {{-- 1. Hari tutup rutin (mingguan) --}}
            <form action="{{ route('admin.settings.closed-weekdays') }}" method="POST" class="mt-6">
                @csrf
                @php $closedWeekdays = (array) \App\Models\Setting::get('closed_weekdays', []); @endphp
                <h4 class="text-sm font-semibold text-gray-800">1. Tutup rutin (setiap minggu)</h4>
                <p class="mt-1 mb-3 text-xs text-gray-500">Centang hari yang klinik selalu tutup - berlaku berulang tiap minggu.</p>
                <div class="flex flex-wrap gap-2">
                    @foreach(['monday'=>'Senin','tuesday'=>'Selasa','wednesday'=>'Rabu','thursday'=>'Kamis','friday'=>'Jumat','saturday'=>'Sabtu','sunday'=>'Minggu'] as $val => $label)
                    <label class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" name="closed_weekdays[]" value="{{ $val }}"
                               {{ in_array($val, $closedWeekdays) ? 'checked' : '' }}
                               class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        {{ $label }}
                    </label>
                    @endforeach
                </div>
                @canDo('settings', 'edit')
                <div class="mt-3">
                    <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Simpan Hari Tutup
                    </button>
                </div>
                @endCanDo
            </form>

            {{-- 2. Tanggal libur khusus (sekali) --}}
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="text-sm font-semibold text-gray-800">2. Tanggal libur khusus (sekali)</h4>
                <p class="mt-1 mb-3 text-xs text-gray-500">Untuk libur nasional atau tanggal tertentu yang tidak berulang.</p>

                @canDo('settings', 'edit')
                <form action="{{ route('admin.settings.closed-dates.store') }}" method="POST" class="flex flex-col sm:flex-row gap-3 mb-4">
                    @csrf
                    <input type="date" name="date" required
                           class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <input type="text" name="note" maxlength="255" placeholder="Keterangan (mis. HUT RI)"
                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Tambah
                    </button>
                </form>
                @endCanDo

                @if($closedDates->isEmpty())
                    <p class="text-sm text-gray-400">Belum ada tanggal libur khusus.</p>
                @else
                    <ul class="divide-y divide-gray-100 rounded-md border border-gray-200">
                        @foreach($closedDates as $cd)
                        @php $c = \Carbon\Carbon::parse($cd->date); @endphp
                        <li class="flex items-center justify-between gap-3 px-4 py-2 text-sm">
                            <div>
                                <span class="font-medium text-gray-900">{{ $c->format('d/m/Y') }}</span>
                                <span class="text-gray-400">({{ \App\Services\BookingService::weekdayLabelId(strtolower($c->format('l'))) }})</span>
                                @if($cd->note)<span class="text-gray-600"> - {{ $cd->note }}</span>@endif
                                @if($c->isPast() && !$c->isToday())<span class="ml-1 text-xs text-gray-400">(lewat)</span>@endif
                            </div>
                            @canDo('settings', 'edit')
                            <form action="{{ route('admin.settings.closed-dates.destroy', $cd) }}" method="POST"
                                  onsubmit="return confirm('Hapus tanggal libur ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="flex-shrink-0 text-red-600 hover:text-red-800 text-xs font-medium">Hapus</button>
                            </form>
                            @endCanDo
                        </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    <!-- Auto-Approval OFF per Tanggal -->
    <div class="mt-8 bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex flex-wrap items-center gap-2">
                <h3 class="text-lg font-medium text-gray-900">Auto-Approval OFF per Tanggal</h3>
                <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800">Ditahan untuk di-ACC</span>
            </div>
            <p class="mt-1 text-sm text-gray-500">
                Booking yang <strong>tanggal janji temu-nya</strong> (jadwal treatment) jatuh pada salah satu tanggal di daftar
                akan otomatis berstatus <strong>Menunggu Konfirmasi</strong> (bukan langsung auto-approve), supaya kamu ACC manual
                dari menu Booking. Cocok untuk mengamankan hari yang sudah diprediksi ramai walk-in.
            </p>
            <p class="mt-2 text-xs text-gray-500">
                Contoh: kalau kamu tambahkan <strong>09/07/2026</strong>, maka booking dengan jadwal 09/07/2026 akan ditahan,
                kapan pun booking itu dibuat. Booking untuk tanggal lain tetap auto-approve.
            </p>

            <div class="mt-3 rounded-md bg-blue-50 border border-blue-200 px-3 py-2 text-xs text-blue-800">
                Daftar ini khusus <strong>tanggal ke depan</strong> (mulai besok). Untuk menahan booking yang <strong>jadwalnya hari ini</strong>,
                matikan <strong>"Auto-approve booking untuk hari ini"</strong> di kartu Kebijakan Booking di atas.
            </div>

            {{-- Tambah tanggal janji temu --}}
            @canDo('settings', 'edit')
            <div class="mt-5">
                <h4 class="text-sm font-semibold text-gray-800">Tambah tanggal janji temu</h4>
                <p class="mt-1 mb-3 text-xs text-gray-500">Masukkan tanggal jadwal treatment yang ingin ditahan untuk di-ACC manual.</p>
                <form action="{{ route('admin.settings.manual-approval-dates.store') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                    @csrf
                    <input type="date" name="date" required
                           min="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}"
                           class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <input type="text" name="note" maxlength="255" placeholder="Keterangan (mis. prediksi ramai)"
                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Tambah
                    </button>
                </form>
            </div>
            @endCanDo

            <div class="mt-4">
                @if($manualApprovalDates->isEmpty())
                    <p class="text-sm text-gray-400">Belum ada tanggal wajib approval manual.</p>
                @else
                    <ul class="divide-y divide-gray-100 rounded-md border border-gray-200">
                        @foreach($manualApprovalDates as $md)
                        @php $m = \Carbon\Carbon::parse($md->date); @endphp
                        <li class="flex items-center justify-between gap-3 px-4 py-2 text-sm">
                            <div>
                                <span class="font-medium text-gray-900">{{ $m->format('d/m/Y') }}</span>
                                <span class="text-gray-400">({{ \App\Services\BookingService::weekdayLabelId(strtolower($m->format('l'))) }})</span>
                                @if($md->note)<span class="text-gray-600"> - {{ $md->note }}</span>@endif
                                @if($m->isToday())
                                    <span class="ml-1 inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">hari ini</span>
                                @elseif($m->isPast())
                                    <span class="ml-1 text-xs text-gray-400">(lewat)</span>
                                @endif
                            </div>
                            @canDo('settings', 'edit')
                            <form action="{{ route('admin.settings.manual-approval-dates.destroy', $md) }}" method="POST"
                                  onsubmit="return confirm('Hapus tanggal ini dari daftar wajib approval?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="flex-shrink-0 text-red-600 hover:text-red-800 text-xs font-medium">Hapus</button>
                            </form>
                            @endCanDo
                        </li>
                        @endforeach
                    </ul>
                @endif
            </div>
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
    const shopStatusAlert = document.getElementById('shopStatusAlert');

    if (toggleShopBtn) {
        toggleShopBtn.addEventListener('click', function() {
            const button = this;
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
                    if (!shopStatusAlert) return;

                    if (data.success) {
                        const newIsOpen = data.is_open;
                        button.setAttribute('data-is-open', newIsOpen ? '1' : '0');

                        const knob = button.querySelector('span:last-child');

                        if (newIsOpen) {
                            button.classList.remove('bg-gray-200');
                            button.classList.add('bg-green-600');
                            if (knob) {
                                knob.classList.remove('translate-x-0');
                                knob.classList.add('translate-x-5');
                            }
                            if (statusText) {
                                statusText.textContent = 'Toko saat ini: BUKA';
                                statusText.classList.remove('text-red-600');
                                statusText.classList.add('text-green-600');
                            }

                            shopStatusAlert.className = 'mt-4 rounded-md bg-green-50 border border-green-200 p-4';
                            shopStatusAlert.innerHTML = `
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
                            button.classList.remove('bg-green-600');
                            button.classList.add('bg-gray-200');
                            if (knob) {
                                knob.classList.remove('translate-x-5');
                                knob.classList.add('translate-x-0');
                            }
                            if (statusText) {
                                statusText.textContent = 'Toko saat ini: TUTUP';
                                statusText.classList.remove('text-green-600');
                                statusText.classList.add('text-red-600');
                            }

                            shopStatusAlert.className = 'mt-4 rounded-md bg-red-50 border border-red-200 p-4';
                            shopStatusAlert.innerHTML = `
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

                        shopStatusAlert.classList.remove('hidden');
                    } else {
                        shopStatusAlert.className = 'mt-4 rounded-md bg-red-50 border border-red-200 p-4';
                        shopStatusAlert.innerHTML = `
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">${data.message || 'Gagal mengubah status toko.'}</p>
                                </div>
                            </div>
                        `;
                        shopStatusAlert.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    if (!shopStatusAlert) return;
                    shopStatusAlert.className = 'mt-4 rounded-md bg-red-50 border border-red-200 p-4';
                    shopStatusAlert.innerHTML = `
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
                    shopStatusAlert.classList.remove('hidden');
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

<script>
function previewHeroImage(input) {
    const preview = document.getElementById('heroPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.classList.add('hidden');
    }
}
</script>
@endsection
