@extends('layouts.base')

@section('title', 'Beranda')

@section('content')
<!-- Hero Section with Background Image -->
<div class="relative bg-cover bg-center h-[600px]" style="background-image: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('https://images.unsplash.com/photo-1560750588-73207b1ef5b8?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center justify-center">
        <div class="text-center text-white">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4">
                Klinik Kecantikan Terbaik #untukmu
            </h1>
            <p class="text-lg md:text-xl mb-8 max-w-2xl mx-auto">
                Wujudkan Kulit Sehat Dan Bercahaya Bersama Kami Dengan Layanan Terbaik Kami
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                    <a href="{{ route('customer.bookings.create') }}" class="bg-pink-500 text-white px-10 py-4 rounded-full text-lg font-semibold hover:bg-pink-600 transition-all inline-block shadow-lg">
                        Pesan Sekarang
                    </a>
                @else
                    <a href="{{ route('register') }}" class="bg-pink-500 text-white px-10 py-4 rounded-full text-lg font-semibold hover:bg-pink-600 transition-all inline-block shadow-lg">
                        Pesan Sekarang
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>

<!-- Popular Treatments Section -->
@if($popularTreatments->count() > 0)
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="text-center mb-12">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Perawatan Populer</h2>
        <p class="text-gray-600 text-lg">Temukan perawatan wajah dan kulit terbaik dari Nuca Beauty Skin Clinic</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($popularTreatments->take(6) as $treatment)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all group">
            <!-- Treatment Image/Icon -->
            <div class="relative h-64 bg-gradient-to-br from-pink-100 via-pink-50 to-white overflow-hidden">
                @if($treatment->image)
                    <img src="{{ asset('storage/' . $treatment->image) }}" alt="{{ $treatment->name }}" class="w-full h-full object-cover">
                @else
                    <div class="absolute inset-0 flex items-center justify-center">
                        <svg class="w-32 h-32 text-pink-300 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                @endif
                @if($treatment->is_popular)
                <div class="absolute top-4 left-4 bg-pink-500 text-white px-4 py-2 rounded-full text-xs font-semibold shadow-lg">
                    Promo 15%
                </div>
                @endif
            </div>
            
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-3 min-h-[56px]">{{ $treatment->name }}</h3>
                <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ Str::limit($treatment->description, 100) }}</p>
                
                <div class="border-t border-gray-100 pt-4 mt-4">
                    <a href="{{ route('treatments.detail', $treatment->id) }}" class="block text-center bg-pink-500 text-white px-6 py-3 rounded-full font-semibold hover:bg-pink-600 transition-all shadow-md">
                        Lihat Selengkapnya
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Promo Section -->
<div class="bg-gradient-to-br from-pink-50 via-purple-50 to-pink-50 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Promo Klinik Kecantikan Nusa Bulan Ini</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <!-- Promo Card 1 -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden text-center hover:shadow-2xl transition-all">
                <div class="h-64 bg-gradient-to-br from-pink-100 to-pink-50 flex items-center justify-center p-8">
                    <div class="w-48 h-48 bg-white rounded-full flex items-center justify-center shadow-lg">
                        <svg class="w-24 h-24 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Diskon 50% Perawatan Pertama</h3>
                    <ul class="text-gray-600 text-sm space-y-2 mb-4">
                        <li>• Berlaku untuk pelanggan baru</li>
                        <li>• Pilihan beberapa hingga akhir bulan</li>
                        <li>• Tidak dapat digabungkan dengan promo lain</li>
                    </ul>
                </div>
            </div>

            <!-- Promo Card 2 -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden text-center hover:shadow-2xl transition-all">
                <div class="h-64 bg-gradient-to-br from-purple-100 to-purple-50 flex items-center justify-center p-8">
                    <div class="w-48 h-48 bg-white rounded-full flex items-center justify-center shadow-lg">
                        <svg class="w-24 h-24 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Gratis Konsultasi Dokter</h3>
                    <ul class="text-gray-600 text-sm space-y-2 mb-4">
                        <li>• Dapatkan konsultasi gratis dengan dokter spesialis</li>
                        <li>• Wajib reservasi sebelumnya</li>
                        <li>• Tersedia untuk semua jenis perawatan</li>
                    </ul>
                </div>
            </div>

            <!-- Promo Card 3 -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden text-center hover:shadow-2xl transition-all">
                <div class="h-64 bg-gradient-to-br from-pink-100 to-pink-50 flex items-center justify-center p-8">
                    <div class="w-48 h-48 bg-white rounded-full flex items-center justify-center shadow-lg">
                        <svg class="w-24 h-24 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                        </svg>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Cicilan 0% untuk Semua Treatment</h3>
                    <ul class="text-gray-600 text-sm space-y-2 mb-4">
                        <li>• Minimum transaksi Rp 1.000.000</li>
                        <li>• Tenor 3 & 6 bulan</li>
                        <li>• Kerja dengan beragam bank pilihan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Why Choose Us Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="text-center mb-16">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Keunggulan Nuca Beauty Skin Dibanding Klinik Lain</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
        <!-- Feature 1 -->
        <div class="text-center group">
            <div class="bg-pink-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-pink-500 transition-all">
                <svg class="w-10 h-10 text-pink-500 group-hover:text-white transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-3">Tenaga Medis Profesional</h3>
            <p class="text-gray-600 text-sm">Ditangani oleh Dokter berpengalaman dan berpengalaman</p>
        </div>

        <!-- Feature 2 -->
        <div class="text-center group">
            <div class="bg-pink-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-pink-500 transition-all">
                <svg class="w-10 h-10 text-pink-500 group-hover:text-white transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-3">Teknologi Terkini</h3>
            <p class="text-gray-600 text-sm">Mesin berkualitas tinggi untuk hasil maksimal</p>
        </div>

        <!-- Feature 3 -->
        <div class="text-center group">
            <div class="bg-pink-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-pink-500 transition-all">
                <svg class="w-10 h-10 text-pink-500 group-hover:text-white transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-3">Pelayanan Terbaik</h3>
            <p class="text-gray-600 text-sm">Klinik kesehatan yang nyaman dan aman</p>
        </div>

        <!-- Feature 4 -->
        <div class="text-center group">
            <div class="bg-pink-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-pink-500 transition-all">
                <svg class="w-10 h-10 text-pink-500 group-hover:text-white transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-3">Konsultasi Dokter Gratis</h3>
            <p class="text-gray-600 text-sm">Berkonsultasi sebelum dalam 100%!</p>
        </div>

        <!-- Feature 5 -->
        <div class="text-center group">
            <div class="bg-pink-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-pink-500 transition-all">
                <svg class="w-10 h-10 text-pink-500 group-hover:text-white transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-3">Harga Terjangkau</h3>
            <p class="text-gray-600 text-sm">Perawatan premium dengan harga terjangkau</p>
        </div>

        <!-- Feature 6 -->
        <div class="text-center group">
            <div class="bg-pink-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-pink-500 transition-all">
                <svg class="w-10 h-10 text-pink-500 group-hover:text-white transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-3">Klinik Kecantikan Terdekat</h3>
            <p class="text-gray-600 text-sm">50 cabang di Depok, Tangerang, Bekasi, Jakarta, dan Bogor</p>
        </div>
    </div>
</div>

<!-- Booking Flow & Vouchers -->
<div class="bg-white py-20 border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Alur Pendaftaran & Booking Mudah</h2>
            <p class="text-gray-600 text-lg max-w-3xl mx-auto">Kami menyederhanakan proses pendaftaran dan pemesanan temu agar Anda dapat fokus pada perawatan kulit. Ikuti langkah-langkah mudah berikut:</p>
        </div>

        <!-- Booking Flow Steps -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-20">
            <div class="text-center">
                <div class="bg-pink-500 text-white w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl font-bold shadow-lg">
                    1
                </div>
                <h3 class="font-bold text-gray-900 mb-3 text-lg">Kunjungi website Nuca Beauty Skin dan klik 'Reservasi Sekarang'</h3>
                <p class="text-gray-600 text-sm">Pilih perawatan yang diinginkan dari daftar menu</p>
            </div>

            <div class="text-center">
                <div class="bg-pink-500 text-white w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl font-bold shadow-lg">
                    2
                </div>
                <h3 class="font-bold text-gray-900 mb-3 text-lg">Pilih tanggal dan waktu yang tersedia untuk jam temu</h3>
                <p class="text-gray-600 text-sm">Pilih tanggal dan waktu yang tersedia untuk jam temu</p>
            </div>

            <div class="text-center">
                <div class="bg-pink-500 text-white w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl font-bold shadow-lg">
                    3
                </div>
                <h3 class="font-bold text-gray-900 mb-3 text-lg">Pilih dokter spesialis (opsional) atau biarkan sistem memilihkan</h3>
                <p class="text-gray-600 text-sm">Pilih dokter yang Anda inginkan atau biarkan sistem memilih</p>
            </div>

            <div class="text-center">
                <div class="bg-pink-500 text-white w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl font-bold shadow-lg">
                    4
                </div>
                <h3 class="font-bold text-gray-900 mb-3 text-lg">Verifikasi nomor WhatsApp Anda dengan kode OTP</h3>
                <p class="text-gray-600 text-sm">Terima kode OTP melalui WhatsApp untuk verifikasi</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
            <div class="text-center">
                <div class="bg-pink-500 text-white w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl font-bold shadow-lg">
                    5
                </div>
                <h3 class="font-bold text-gray-900 mb-3 text-lg">Lengkapi detail profil lengkap Anda dan buat booking</h3>
                <p class="text-gray-600 text-sm">Isi data diri dengan lengkap untuk proses booking</p>
            </div>

            <div class="text-center">
                <div class="bg-pink-500 text-white w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl font-bold shadow-lg">
                    6
                </div>
                <h3 class="font-bold text-gray-900 mb-3 text-lg">Konfirmasi janji temu Anda dan lakukan pembayaran</h3>
                <p class="text-gray-600 text-sm">Bayar DP untuk mengkonfirmasi booking Anda</p>
            </div>

            <div class="text-center">
                <div class="bg-pink-500 text-white w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl font-bold shadow-lg">
                    7
                </div>
                <h3 class="font-bold text-gray-900 mb-3 text-lg">Kembali janji temu Anda dan lakukan pembayaran</h3>
                <p class="text-gray-600 text-sm">Admin akan menghubungi Anda via WhatsApp</p>
            </div>
        </div>

        <div class="text-center">
            <a href="{{ route('register') }}" class="inline-block bg-pink-500 text-white px-10 py-4 rounded-full text-lg font-semibold hover:bg-pink-600 transition-all shadow-lg">
                Mulai Booking via WhatsApp
            </a>
        </div>
    </div>
</div>

<!-- Status Pemesanan & Janji Temu -->
<div class="bg-gradient-to-br from-gray-50 to-gray-100 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Status Pemesanan & Janji Temu Mendatang</h2>
            <p class="text-gray-600 text-lg">Lihat ringkasan janji temu Anda dan status terbaru di sini</p>
        </div>

        <!-- Appointment Card -->
        <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-8 mb-12">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-pink-500">Janji Temu Aktif</h3>
                <span class="text-gray-600 text-sm">Riwayat Janji Temu</span>
            </div>

            @auth
                @if(Auth::user()->bookings()->whereIn('status', ['pending', 'confirmed'])->exists())
                    @php
                        $nextBooking = Auth::user()->bookings()->whereIn('status', ['pending', 'confirmed'])->orderBy('booking_date')->first();
                    @endphp
                    <div class="border-l-4 border-pink-500 pl-6 mb-6">
                        <h4 class="font-bold text-gray-900 text-lg mb-2">{{ $nextBooking->treatment->name }}</h4>
                        <p class="text-gray-600 text-sm mb-1">Dengan Dr. {{ $nextBooking->doctor->name }}</p>
                        <p class="text-gray-600 text-sm mb-1">📅 {{ \Carbon\Carbon::parse($nextBooking->booking_date)->format('d Desember Y') }}</p>
                        <p class="text-gray-600 text-sm mb-1">🕐 {{ $nextBooking->booking_time }} WIB</p>
                        <p class="text-pink-600 font-semibold text-sm mt-3">{{ ucfirst($nextBooking->status) }}</p>
                    </div>
                    <div class="flex gap-4">
                        <a href="{{ route('customer.bookings.show', $nextBooking->id) }}" class="flex-1 text-center bg-pink-500 text-white px-6 py-3 rounded-full font-semibold hover:bg-pink-600 transition-all">
                            Konfirmasi
                        </a>
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-gray-500 mb-4">Anda belum memiliki riwayat janji temu</p>
                        <a href="{{ route('customer.bookings.create') }}" class="inline-block bg-pink-500 text-white px-8 py-3 rounded-full font-semibold hover:bg-pink-600 transition-all">
                            Buat Booking Baru
                        </a>
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500 mb-4">Login untuk melihat janji temu Anda</p>
                    <a href="{{ route('login') }}" class="inline-block bg-pink-500 text-white px-8 py-3 rounded-full font-semibold hover:bg-pink-600 transition-all">
                        Login
                    </a>
                </div>
            @endauth
        </div>

        <!-- Testimonials -->
        <div class="mb-16">
            <h3 class="text-2xl font-bold text-gray-900 mb-8 text-center">Apa Kata Klien Kami?</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-white rounded-2xl shadow-md p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-pink-200 rounded-full flex items-center justify-center mr-4">
                            <span class="text-pink-600 font-bold">SW</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">Sarah W.</h4>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm italic">"Pelayanan di Nuca Beauty Skin luar biasa! Wajah saya sekarang lebih cerah dan sehat. Dokternya sangat ramah dan informatif. Sangat direkomendasi!"</p>
                </div>

                <!-- Testimonial 2 -->
                <div class="bg-white rounded-2xl shadow-md p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-purple-200 rounded-full flex items-center justify-center mr-4">
                            <span class="text-purple-600 font-bold">DP</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">Dewi P.</h4>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm italic">"Saya sangat puas dengan hasil laser rejuvenation di sini. Flek hitam saya memudar signifikan. Susunanya nyaman dan bersih. Pasti akan kembali!"</p>
                </div>

                <!-- Testimonial 3 -->
                <div class="bg-white rounded-2xl shadow-md p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-pink-200 rounded-full flex items-center justify-center mr-4">
                            <span class="text-pink-600 font-bold">DL</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">Dina L.</h4>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm italic">"Pertama kali mencoba skin booster, hasilnya di luar ekspektasi. Kulit terasa lebih kencang dan glowing. Terima kasih Nuca Beauty Skin!"</p>
                </div>
            </div>
        </div>

        <!-- Payment Options -->
        <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Bayar Pakai Paylater, Cicilan 0%</h3>
            <div class="flex flex-wrap justify-center gap-6 items-center">
                <div class="bg-gray-100 px-6 py-3 rounded-lg">
                    <span class="font-semibold text-gray-700">BCA</span>
                </div>
                <div class="bg-gray-100 px-6 py-3 rounded-lg">
                    <span class="font-semibold text-gray-700">Mandiri</span>
                </div>
                <div class="bg-gray-100 px-6 py-3 rounded-lg">
                    <span class="font-semibold text-gray-700">BNI</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Articles Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="text-center mb-12">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Artikel Kecantikan</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
        <!-- Article 1 -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-all">
            <div class="h-48 bg-gradient-to-br from-pink-100 to-pink-50"></div>
            <div class="p-6">
                <span class="bg-pink-500 text-white px-3 py-1 rounded-full text-xs font-semibold">Kulit</span>
                <h3 class="text-lg font-bold text-gray-900 mt-4 mb-2">5 Tips Kulit Sehat Alami untuk Wajah Glowing</h3>
                <p class="text-gray-600 text-sm mb-4">BACA SELENGKAPNYA »</p>
                <p class="text-gray-500 text-xs">14 November 2025</p>
            </div>
        </div>

        <!-- Article 2 -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-all">
            <div class="h-48 bg-gradient-to-br from-purple-100 to-purple-50"></div>
            <div class="p-6">
                <span class="bg-pink-500 text-white px-3 py-1 rounded-full text-xs font-semibold">Perawatan</span>
                <h3 class="text-lg font-bold text-gray-900 mt-4 mb-2">Rambut Berkilau dengan Perawatan Alami di Rumah</h3>
                <p class="text-gray-600 text-sm mb-4">BACA SELENGKAPNYA »</p>
                <p class="text-gray-500 text-xs">01 Oktober 2025</p>
            </div>
        </div>

        <!-- Article 3 -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-all">
            <div class="h-48 bg-gradient-to-br from-pink-100 to-pink-50"></div>
            <div class="p-6">
                <span class="bg-pink-500 text-white px-3 py-1 rounded-full text-xs font-semibold">Kecantikan</span>
                <h3 class="text-lg font-bold text-gray-900 mt-4 mb-2">Tren Kecantikan 2025: Apa Saja yang Wajib Kamu Coba?</h3>
                <p class="text-gray-600 text-sm mb-4">BACA SELENGKAPNYA »</p>
                <p class="text-gray-500 text-xs">28 September 2025</p>
            </div>
        </div>
    </div>

    <div class="text-center">
        <a href="#" class="inline-block bg-pink-500 text-white px-8 py-3 rounded-full font-semibold hover:bg-pink-600 transition-all">
            Lihat Semua Artikel
        </a>
    </div>
</div>

<!-- FAQ Section -->
<div class="bg-gradient-to-br from-gray-50 to-gray-100 py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">FAQ (Frequently Asked Question)</h2>
            <p class="text-pink-500 font-semibold text-lg">Nuca Beauty Skin Terbaik dengan Harga Terjangkau</p>
            <p class="text-gray-600 mt-2">Mau perawatan kulit tapi efektif? Daftar dan reservasi ke Nuca Beauty Skin Clinic terdekat sekarang!</p>
        </div>

        <div class="space-y-4">
            <!-- FAQ Item 1 -->
            <details class="bg-white rounded-lg shadow-md p-6 group">
                <summary class="font-bold text-gray-900 cursor-pointer list-none flex items-center justify-between">
                    <span>Apa itu Nuca Beauty Skin?</span>
                    <svg class="w-5 h-5 text-pink-500 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </summary>
                <p class="text-gray-600 mt-4 text-sm">Nuca Beauty Skin merupakan klinik kecantikan terpercaya yang menyediakan berbagai perawatan wajah dan kulit dengan teknologi terkini dan tenaga medis profesional. Kami berkomitmen untuk membantu Anda mencapai kulit sehat dan cantik alami.</p>
            </details>

            <!-- FAQ Item 2 -->
            <details class="bg-white rounded-lg shadow-md p-6 group">
                <summary class="font-bold text-gray-900 cursor-pointer list-none flex items-center justify-between">
                    <span>Mengapa memilih klinik kecantikan Nuca Beauty Skin?</span>
                    <svg class="w-5 h-5 text-pink-500 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </summary>
                <p class="text-gray-600 mt-4 text-sm">Kami memiliki dokter spesialis berpengalaman, teknologi modern, harga terjangkau, lokasi strategis, dan telah dipercaya oleh ribuan pelanggan. Semua perawatan dilakukan dengan standar medis tertinggi.</p>
            </details>

            <!-- FAQ Item 3 -->
            <details class="bg-white rounded-lg shadow-md p-6 group">
                <summary class="font-bold text-gray-900 cursor-pointer list-none flex items-center justify-between">
                    <span>Apa saja layanan yang ditawarkan di Nuca Beauty Skin?</span>
                    <svg class="w-5 h-5 text-pink-500 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </summary>
                <p class="text-gray-600 mt-4 text-sm">Kami menyediakan berbagai treatment seperti facial, laser rejuvenation, anti-aging, skin booster, brightening peel, dan masih banyak lagi. Konsultasikan kebutuhan kulit Anda dengan dokter kami.</p>
            </details>

            <!-- FAQ Item 4 -->
            <details class="bg-white rounded-lg shadow-md p-6 group">
                <summary class="font-bold text-gray-900 cursor-pointer list-none flex items-center justify-between">
                    <span>Apakah perawatan di Nuca Beauty Skin aman untuk semua jenis kulit?</span>
                    <svg class="w-5 h-5 text-pink-500 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </summary>
                <p class="text-gray-600 mt-4 text-sm">Ya, semua perawatan kami telah disesuaikan dengan jenis kulit masing-masing pasien. Dokter kami akan melakukan konsultasi dan analisis kulit terlebih dahulu untuk memastikan treatment yang tepat dan aman.</p>
            </details>

            <!-- FAQ Item 5 -->
            <details class="bg-white rounded-lg shadow-md p-6 group">
                <summary class="font-bold text-gray-900 cursor-pointer list-none flex items-center justify-between">
                    <span>Bagaimana cara melakukan reservasi di Nuca Beauty Skin?</span>
                    <svg class="w-5 h-5 text-pink-500 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </summary>
                <p class="text-gray-600 mt-4 text-sm">Anda dapat melakukan reservasi melalui website kami dengan mengklik tombol "Reservasi Sekarang", memilih treatment, tanggal, dan waktu yang diinginkan. Atau hubungi WhatsApp kami untuk bantuan lebih lanjut.</p>
            </details>
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('register') }}" class="inline-block bg-pink-500 text-white px-10 py-4 rounded-full text-lg font-semibold hover:bg-pink-600 transition-all shadow-lg">
                Reservasi Sekarang
            </a>
        </div>
    </div>
</div>

<!-- Call to Action -->
<div class="bg-gradient-to-r from-pink-500 via-pink-600 to-purple-500 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">Siap Untuk Tampil Lebih Cantik?</h2>
        <p class="text-xl mb-8">Booking treatment Anda sekarang dan dapatkan konsultasi gratis!</p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            @auth
                <a href="{{ route('customer.bookings.create') }}" class="bg-white text-pink-600 px-10 py-4 rounded-full text-lg font-semibold hover:bg-gray-100 transition-all inline-block shadow-xl">
                    Mulai Booking Sekarang
                </a>
            @else
                <a href="{{ route('register') }}" class="bg-white text-pink-600 px-10 py-4 rounded-full text-lg font-semibold hover:bg-gray-100 transition-all inline-block shadow-xl">
                    Mulai Booking Sekarang
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection
