@extends('layouts.base')

@section('title', 'Beranda')

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                {{ $clinicInfo['name'] ?? 'Klinik Kecantikan' }}
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-indigo-100">
                Wujudkan Kecantikan Impian Anda Bersama Kami
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                    <a href="{{ route('customer.bookings.create') }}" class="bg-white text-indigo-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition-all inline-block">
                        Booking Sekarang
                    </a>
                @else
                    <a href="{{ route('register') }}" class="bg-white text-indigo-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition-all inline-block">
                        Booking Sekarang
                    </a>
                    <a href="{{ route('login') }}" class="border-2 border-white text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-white hover:text-indigo-600 transition-all inline-block">
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>

<!-- Info Cards -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-lg p-6 text-center">
            <div class="text-indigo-600 mb-4">
                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold mb-2">Dokter Profesional</h3>
            <p class="text-gray-600">Tim dokter berpengalaman dan bersertifikat</p>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-6 text-center">
            <div class="text-indigo-600 mb-4">
                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold mb-2">Booking Mudah</h3>
            <p class="text-gray-600">Reservasi online 24/7 atau via WhatsApp</p>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-6 text-center">
            <div class="text-indigo-600 mb-4">
                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold mb-2">Harga Terjangkau</h3>
            <p class="text-gray-600">Berbagai promo dan diskon member 10%</p>
        </div>
    </div>
</div>

<!-- Popular Treatments -->
@if($popularTreatments->count() > 0)
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-12">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Treatment Populer</h2>
        <p class="text-gray-600 text-lg">Pilihan treatment favorit pelanggan kami</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($popularTreatments as $treatment)
        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-48 flex items-center justify-center">
                <svg class="w-24 h-24 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $treatment->name }}</h3>
                <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $treatment->description }}</p>
                
                <div class="flex items-center justify-between mb-4">
                    <div class="text-sm text-gray-500">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $treatment->duration_minutes }} menit
                    </div>
                    <div class="flex items-center text-yellow-500">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= round($treatment->average_rating) ? 'fill-current' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        @endfor
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="text-2xl font-bold text-indigo-600">
                        {{ $treatment->formatted_price }}
                    </div>
                    <a href="{{ route('treatments.detail', $treatment->id) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold text-sm">
                        Lihat Detail →
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="text-center mt-12">
        <a href="{{ route('treatments') }}" class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-all">
            Lihat Semua Treatment
        </a>
    </div>
</div>
@endif

<!-- Active Vouchers -->
@if($activeVouchers->count() > 0)
<div class="bg-gray-100 py-16">
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
<div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">Siap Untuk Tampil Lebih Cantik?</h2>
        <p class="text-xl mb-8 text-indigo-100">Booking treatment Anda sekarang dan dapatkan konsultasi gratis!</p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            @auth
                <a href="{{ route('customer.bookings.create') }}" class="bg-white text-indigo-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition-all inline-block">
                    Mulai Booking
                </a>
            @else
                <a href="{{ route('register') }}" class="bg-white text-indigo-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition-all inline-block">
                    Daftar Sekarang
                </a>
            @endauth
            <a href="https://wa.me/{{ $clinicInfo['whatsapp'] ?? '6281234567890' }}" target="_blank" class="bg-green-500 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-green-600 transition-all inline-block">
                <svg class="w-6 h-6 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg>
                Hubungi WhatsApp
            </a>
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
