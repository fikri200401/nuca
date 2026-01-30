@extends('layouts.base')

@section('title', 'Beranda')

@section('content')
<!-- Hero Section with Background Image -->
<div class="relative bg-cover bg-center" style="background-image: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('https://images.unsplash.com/photo-1560750588-73207b1ef5b8?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32">
        <div class="text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                Klinik Kecantikan Terbaik #untukmu
            </h1>
            <p class="text-lg md:text-xl mb-8 max-w-2xl mx-auto">
                Wujudkan Kulit Sehat Dan Bercahaya Bersama Kami Dengan Layanan Terbaik Kami
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                    <a href="{{ route('customer.bookings.create') }}" class="bg-pink-500 text-white px-8 py-3 rounded-full text-lg font-semibold hover:bg-pink-600 transition-all inline-block shadow-lg">
                        Konsultasi Sekarang
                    </a>
                @else
                    <a href="{{ route('register') }}" class="bg-pink-500 text-white px-8 py-3 rounded-full text-lg font-semibold hover:bg-pink-600 transition-all inline-block shadow-lg">
                        Konsultasi Sekarang
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>

<!-- Popular Treatments -->
@if($popularTreatments->count() > 0)
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-12">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Perawatan Populer</h2>
        <p class="text-gray-600 text-lg">Temukan perawatan favorit kami yang banyak diminati oleh pelanggan kami</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($popularTreatments as $treatment)
        <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-all group">
            <!-- Treatment Image/Icon -->
            <div class="relative h-64 bg-gradient-to-br from-pink-100 to-pink-50 overflow-hidden">
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-32 h-32 text-pink-300 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                @if($treatment->is_popular)
                <div class="absolute top-4 right-4 bg-pink-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                    Populer
                </div>
                @endif
            </div>
            
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $treatment->name }}</h3>
                <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $treatment->description }}</p>
                
                <div class="border-t border-gray-100 pt-4 mt-4">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-2xl font-bold text-pink-600">
                            {{ $treatment->formatted_price }}
                        </span>
                        <div class="text-sm text-gray-500">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $treatment->duration_minutes }} menit
                        </div>
                    </div>
                    
                    <a href="{{ route('treatments.detail', $treatment->id) }}" class="block text-center bg-pink-500 text-white px-6 py-3 rounded-full font-semibold hover:bg-pink-600 transition-all">
                        Lihat Treatment
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="text-center mt-12">
        <a href="{{ route('treatments') }}" class="inline-block bg-white border-2 border-pink-500 text-pink-500 px-8 py-3 rounded-full font-semibold hover:bg-pink-500 hover:text-white transition-all">
            Lihat Semua Treatment
        </a>
    </div>
</div>
@endif

<!-- Promo Section - Nusa Beauty Skin -->
<div class="bg-gradient-to-r from-pink-50 to-purple-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Promo Klinik Kecantikan Nusa Bulan Ini</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <!-- Doctor Card 1 -->
            <div class="bg-white rounded-2xl shadow-md overflow-hidden text-center">
                <div class="h-64 bg-gradient-to-br from-pink-100 to-pink-50 flex items-center justify-center">
                    <div class="w-48 h-48 bg-pink-200 rounded-full flex items-center justify-center">
                        <svg class="w-24 h-24 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Dokter dr. Kecantikan Pertama</h3>
                    <p class="text-gray-600 text-sm mb-4">Spesialis treatment wajah dan kulit dengan pengalaman lebih dari 10 tahun</p>
                </div>
            </div>

            <!-- Doctor Card 2 -->
            <div class="bg-white rounded-2xl shadow-md overflow-hidden text-center">
                <div class="h-64 bg-gradient-to-br from-purple-100 to-purple-50 flex items-center justify-center">
                    <div class="w-48 h-48 bg-purple-200 rounded-full flex items-center justify-center">
                        <svg class="w-24 h-24 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Estheia Konsultasi Dokter</h3>
                    <p class="text-gray-600 text-sm mb-4">Ahli perawatan kulit berjerawat dan anti aging treatment</p>
                </div>
            </div>

            <!-- Doctor Card 3 -->
            <div class="bg-white rounded-2xl shadow-md overflow-hidden text-center">
                <div class="h-64 bg-gradient-to-br from-pink-100 to-pink-50 flex items-center justify-center">
                    <div class="w-48 h-48 bg-pink-200 rounded-full flex items-center justify-center">
                        <svg class="w-24 h-24 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">dr.Ratmi dr. Seina Traitment</h3>
                    <p class="text-gray-600 text-sm mb-4">Spesialis laser treatment dan perawatan kulit sensitif</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Why Choose Us Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-12">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Keunggulan Nuca Beauty Skin Dibanding Klinik Lain</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Feature 1 -->
        <div class="text-center">
            <div class="bg-pink-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Produk Sesuai Profesional</h3>
            <p class="text-gray-600 text-sm">Hanya menggunakan produk berkualitas tinggi yang telah teruji klinis</p>
        </div>

        <!-- Feature 2 -->
        <div class="text-center">
            <div class="bg-pink-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Teknologi Terkemuka</h3>
            <p class="text-gray-600 text-sm">Peralatan modern dan teknologi terdepan untuk hasil maksimal</p>
        </div>

        <!-- Feature 3 -->
        <div class="text-center">
            <div class="bg-pink-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Pelayanan Terbaik</h3>
            <p class="text-gray-600 text-sm">Pelayanan ramah dan profesional untuk kenyamanan Anda</p>
        </div>

        <!-- Feature 4 -->
        <div class="text-center">
            <div class="bg-pink-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Konsultasi Dokter Gratis</h3>
            <p class="text-gray-600 text-sm">Konsultasi gratis dengan dokter ahli sebelum treatment</p>
        </div>

        <!-- Feature 5 -->
        <div class="text-center">
            <div class="bg-pink-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Harga Terjangkau</h3>
            <p class="text-gray-600 text-sm">Harga kompetitif dengan kualitas premium</p>
        </div>

        <!-- Feature 6 -->
        <div class="text-center">
            <div class="bg-pink-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Terbukti</h3>
            <p class="text-gray-600 text-sm">Ribuan customer puas dengan hasil perawatan kami</p>
        </div>
    </div>
</div>

<!-- Booking Flow & Vouchers -->
@if($activeVouchers->count() > 0)
<div class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Alur Pendaftaran & Booking Mudah</h2>
            <p class="text-gray-600 text-lg">Ikuti langkah mudah berikut untuk mendapatkan treatment impian Anda</p>
        </div>

        <!-- Booking Flow Steps -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-16">
            <div class="text-center">
                <div class="bg-pink-500 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                    1
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Daftar/Login Akun</h3>
                <p class="text-gray-600 text-sm">Buat akun atau login ke akun Anda</p>
            </div>

            <div class="text-center">
                <div class="bg-pink-500 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                    2
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Pilih Treatment</h3>
                <p class="text-gray-600 text-sm">Pilih treatment yang Anda inginkan</p>
            </div>

            <div class="text-center">
                <div class="bg-pink-500 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                    3
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Pilih Jadwal</h3>
                <p class="text-gray-600 text-sm">Pilih tanggal dan jam yang tersedia</p>
            </div>

            <div class="text-center">
                <div class="bg-pink-500 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                    4
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Konfirmasi & Bayar DP</h3>
                <p class="text-gray-600 text-sm">Konfirmasi booking dan bayar deposit</p>
            </div>
        </div>

        <!-- Vouchers Grid -->
        <div class="mb-12">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">Voucher & Promo Aktif 🎉</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($activeVouchers->take(3) as $voucher)
                <div class="bg-gradient-to-br from-pink-50 to-purple-50 rounded-2xl shadow-md p-6 border-2 border-dashed border-pink-300">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $voucher->name }}</h3>
                            <p class="text-gray-600 text-sm">{{ $voucher->description }}</p>
                        </div>
                        <div class="bg-pink-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                            {{ $voucher->formatted_value }}
                        </div>
                    </div>

                    <div class="border-t border-pink-200 pt-4 mt-4">
                        <div class="flex items-center justify-between text-sm mb-2">
                            <span class="text-gray-600">Kode:</span>
                            <code class="bg-white px-3 py-1 rounded font-mono font-bold text-pink-600">{{ $voucher->code }}</code>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Berlaku s/d:</span>
                            <span class="font-semibold">{{ $voucher->valid_until->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif
@if($activeVouchers->count() > 0)
<div class="bg-gray-100 py-16 hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Promo Bulan Ini 🎉</h2>
            <p class="text-gray-600 text-lg">Dapatkan diskon menarik untuk treatment favoritmu</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($activeVouchers as $voucher)
            <div class="bg-white rounded-lg shadow-lg p-6 border-2 border-dashed border-indigo-300">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $voucher->name }}</h3>
                        <p class="text-gray-600 text-sm">{{ $voucher->description }}</p>
                    </div>
                    <div class="bg-indigo-600 text-white px-3 py-1 rounded-full text-sm font-bold">
                        {{ $voucher->formatted_value }}
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4 mt-4">
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-gray-600">Kode Voucher:</span>
                        <code class="bg-gray-100 px-3 py-1 rounded font-mono font-bold text-indigo-600">{{ $voucher->code }}</code>
                    </div>
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-gray-600">Min. Transaksi:</span>
                        <span class="font-semibold">Rp {{ number_format($voucher->min_transaction, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Berlaku hingga:</span>
                        <span class="font-semibold">{{ $voucher->valid_until->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('vouchers') }}" class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-all">
                Lihat Semua Promo
            </a>
        </div>
    </div>
</div>
@endif

<!-- Call to Action -->
<div class="bg-gradient-to-r from-pink-500 to-purple-500 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">Siap Untuk Tampil Lebih Cantik?</h2>
        <p class="text-xl mb-8">Booking treatment Anda sekarang dan dapatkan konsultasi gratis!</p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            @auth
                <a href="{{ route('customer.bookings.create') }}" class="bg-white text-pink-600 px-8 py-4 rounded-full text-lg font-semibold hover:bg-gray-100 transition-all inline-block shadow-lg">
                    Mulai Booking Sekarang
                </a>
            @else
                <a href="{{ route('register') }}" class="bg-white text-pink-600 px-8 py-4 rounded-full text-lg font-semibold hover:bg-gray-100 transition-all inline-block shadow-lg">
                    Mulai Booking Sekarang
                </a>
            @endauth
        </div>
    </div>
</div>

<!-- Clinic Info -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
        <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Tentang Kami</h2>
            <p class="text-gray-600 mb-4 leading-relaxed">
                {{ $clinicInfo['about'] ?? 'Kami adalah klinik kecantikan terpercaya dengan pengalaman lebih dari 10 tahun dalam memberikan layanan kecantikan terbaik. Didukung oleh dokter-dokter profesional dan peralatan modern, kami siap membantu Anda mencapai kecantikan impian.' }}
            </p>
            <div class="space-y-3">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-indigo-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold text-gray-900">Alamat</p>
                        <p class="text-gray-600">{{ $clinicInfo['address'] ?? 'Jl. Kecantikan No. 123, Jakarta' }}</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-indigo-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold text-gray-900">Jam Operasional</p>
                        <p class="text-gray-600">{{ $clinicInfo['operating_hours'] }}</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-indigo-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold text-gray-900">Kontak</p>
                        <p class="text-gray-600">{{ $clinicInfo['phone'] ?? '(021) 1234-5678' }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-indigo-500 to-purple-500 rounded-lg p-8 text-white">
            <h3 class="text-2xl font-bold mb-6">Keuntungan Member</h3>
            <ul class="space-y-4">
                <li class="flex items-start">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Diskon 10% untuk semua treatment</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Voucher eksklusif untuk transaksi tertentu</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Doorprize untuk transaksi di atas Rp 500.000</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Prioritas booking dan reminder via WhatsApp</span>
                </li>
            </ul>
            <div class="mt-8">
                <a href="{{ route('register') }}" class="block text-center bg-white text-indigo-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-all">
                    Daftar Jadi Member
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
