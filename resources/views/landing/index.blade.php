<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Nuca Beauty Skin') }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            light: '#ff8fa3',
                            DEFAULT: '#ff4d88',
                            dark: '#e03e73',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    }
                }
            }
        }
    </script>
    <style>
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="bg-gray-50 text-gray-700 antialiased">

    <nav class="bg-white py-4 shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <div class="w-8 h-8 bg-brand rounded-full flex items-center justify-center text-white font-serif font-bold">N</div>
                <span class="text-xl font-serif font-bold text-gray-800">Nuca Beauty Skin</span>
            </a>

            <div class="hidden md:flex space-x-8 text-sm font-medium text-gray-600">
                <a href="#home" class="hover:text-brand transition">Home</a>
                <a href="#treatments" class="hover:text-brand transition">Perawatan</a>
                <a href="#articles" class="hover:text-brand transition">Artikel</a>
                <a href="#promo" class="hover:text-brand transition">Promo</a>
                <a href="#advantages" class="hover:text-brand transition">Klinik</a>
                <a href="#faq" class="hover:text-brand transition">Info</a>
            </div>

            <div class="hidden md:flex space-x-3">
                @auth
                    <a href="{{ route('customer.dashboard') }}" class="px-5 py-2 border border-brand text-brand rounded-full text-sm font-medium hover:bg-pink-50 transition">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2 border border-brand text-brand rounded-full text-sm font-medium hover:bg-pink-50 transition">Masuk Akun</a>
                @endauth
                <a href="{{ route('customer.bookings.create') }}" class="px-5 py-2 bg-brand text-white rounded-full text-sm font-medium hover:bg-brand-dark transition shadow-lg shadow-pink-200">Reservasi Sekarang</a>
            </div>
            
            <button id="mobileMenuBtn" class="md:hidden text-gray-600 text-2xl focus:outline-none">
                <i id="hamburgerIcon" class="fas fa-bars"></i>
                <i id="closeIcon" class="fas fa-times hidden"></i>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-white border-t mt-4">
            <div class="px-6 py-4 space-y-3">
                <a href="#home" class="block text-gray-600 hover:text-brand transition">Home</a>
                <a href="#treatments" class="block text-gray-600 hover:text-brand transition">Perawatan</a>
                <a href="#articles" class="block text-gray-600 hover:text-brand transition">Artikel</a>
                <a href="#promo" class="block text-gray-600 hover:text-brand transition">Promo</a>
                <a href="#advantages" class="block text-gray-600 hover:text-brand transition">Klinik</a>
                <a href="#faq" class="block text-gray-600 hover:text-brand transition">Info</a>
                <div class="pt-4 border-t space-y-3">
                    @auth
                        <a href="{{ route('customer.dashboard') }}" class="block px-5 py-2 border border-brand text-brand rounded-full text-sm font-medium text-center hover:bg-pink-50 transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="block px-5 py-2 border border-brand text-brand rounded-full text-sm font-medium text-center hover:bg-pink-50 transition">Masuk Akun</a>
                    @endauth
                    <a href="{{ route('customer.bookings.create') }}" class="block px-5 py-2 bg-brand text-white rounded-full text-sm font-medium text-center hover:bg-brand-dark transition">Reservasi Sekarang</a>
                </div>
            </div>
        </div>
    </nav>

    <header id="home" class="relative h-[500px] flex items-center justify-center bg-gray-100 overflow-hidden">
        <img src="https://images.unsplash.com/photo-1616394584738-fc6e612e71b9?q=80&w=2070&auto=format&fit=crop" alt="Beauty Clinic" class="absolute w-full h-full object-cover opacity-90">
        
        <div class="absolute inset-0 bg-white/30"></div>

        <div class="relative z-10 text-center px-4 max-w-3xl">
            <h1 class="text-4xl md:text-6xl font-serif font-bold text-gray-900 mb-4 leading-tight">
                Klinik Kecantikan Terbaik untukmu
            </h1>
            <p class="text-gray-700 mb-8 text-lg font-light">Wujudkan kulit sehat impianmu bersama {{ $clinicInfo['name'] ?? 'Nuca Beauty Skin' }}</p>
            <a href="{{ route('customer.bookings.create') }}" class="px-8 py-3 bg-brand text-white rounded-full font-medium hover:bg-brand-dark transition shadow-xl transform hover:-translate-y-1">
                Pesan Sekarang
            </a>
        </div>
    </header>

    <section id="treatments" class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-serif font-bold text-gray-900">Perawatan Populer</h2>
                <p class="text-gray-500 mt-2 text-sm">Pilihan perawatan favorit yang sering dipilih oleh klien Nuca Beauty Skin</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($popularTreatments as $treatment)
                <div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition border border-gray-100">
                    <div class="h-48 overflow-hidden relative">
                        @if($treatment->image)
                            <img src="{{ asset('storage/' . $treatment->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="{{ $treatment->name }}">
                        @else
                            <img src="https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&q=80&w=500" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="{{ $treatment->name }}">
                        @endif
                        @if($treatment->is_popular)
                        <span class="absolute top-4 left-4 bg-pink-100 text-brand text-xs font-bold px-3 py-1 rounded-full">Populer</span>
                        @endif
                    </div>
                    <div class="p-6 text-center">
                        <h3 class="font-serif font-bold text-lg mb-2">{{ $treatment->name }}</h3>
                        <p class="text-gray-500 text-xs mb-2 leading-relaxed">{{ Str::limit($treatment->description, 80) }}</p>
                        <p class="text-brand font-bold text-lg mb-4">{{ $treatment->formatted_price }}</p>
                        <a href="{{ route('landing.treatment-detail', $treatment->id) }}" class="w-full inline-block py-2 bg-brand text-white text-sm rounded-lg hover:bg-brand-dark transition">Lihat Selengkapnya</a>
                    </div>
                </div>
                @empty
                {{-- Fallback jika tidak ada data --}}
                <div class="col-span-3 text-center py-12">
                    <p class="text-gray-500">Belum ada perawatan populer tersedia.</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <section id="promo" class="py-16 bg-pink-50">
        <div class="container mx-auto px-6">
            <div class="flex justify-between items-end mb-8">
                <h2 class="text-3xl font-serif font-bold text-gray-900">Promo Klinik Kecantikan Nuca Bulan Ini</h2>
                <a href="#" class="text-brand text-sm font-medium hover:underline flex items-center gap-1">Lihat Semua Promo <i class="fas fa-arrow-right"></i></a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($activeVouchers->take(3) as $voucher)
                <div class="bg-white p-4 rounded-xl shadow-sm flex items-center gap-4">
                    <div class="w-24 h-24 rounded-lg bg-gradient-to-br from-pink-100 to-purple-100 flex items-center justify-center">
                        <i class="fas fa-tag text-brand text-3xl"></i>
                    </div>
                    <div>
                        <h4 class="font-serif font-bold text-gray-800">{{ $voucher->name }}</h4>
                        <p class="text-xs text-gray-500 mt-1">{{ Str::limit($voucher->description, 50) }}</p>
                        <p class="text-xs text-brand mt-2 font-medium">Berakhir {{ $voucher->valid_until->format('d M Y') }}</p>
                    </div>
                </div>
                @empty
                {{-- Fallback promo statis jika tidak ada data --}}
                <div class="bg-white p-4 rounded-xl shadow-sm flex items-center gap-4">
                    <img src="https://images.unsplash.com/photo-1580618672591-eb180b1a973f?auto=format&fit=crop&q=80&w=200" class="w-24 h-24 rounded-lg object-cover">
                    <div>
                        <h4 class="font-serif font-bold text-gray-800">Diskon 50% Perawatan Pertama</h4>
                        <p class="text-xs text-gray-500 mt-1">Khusus member pengguna baru.</p>
                        <p class="text-xs text-brand mt-2 font-medium">Hubungi kami</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <section id="advantages" class="py-16 bg-white">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl font-serif font-bold text-gray-900 mb-12">Keunggulan Nuca Beauty Skin Dibanding Klinik Lain</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8">
                <div class="flex flex-col items-center">
                    <div class="w-12 h-12 rounded-full bg-pink-50 flex items-center justify-center text-brand text-xl mb-4"><i class="fas fa-user-md"></i></div>
                    <h4 class="font-bold text-sm mb-1">Tenaga Medis Profesional</h4>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-12 h-12 rounded-full bg-pink-50 flex items-center justify-center text-brand text-xl mb-4"><i class="fas fa-microscope"></i></div>
                    <h4 class="font-bold text-sm mb-1">Teknologi Modern</h4>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-12 h-12 rounded-full bg-pink-50 flex items-center justify-center text-brand text-xl mb-4"><i class="fas fa-certificate"></i></div>
                    <h4 class="font-bold text-sm mb-1">Pelayanan Terbaik</h4>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-12 h-12 rounded-full bg-pink-50 flex items-center justify-center text-brand text-xl mb-4"><i class="fas fa-smile"></i></div>
                    <h4 class="font-bold text-sm mb-1">Treatment Wajah Holistik</h4>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-12 h-12 rounded-full bg-pink-50 flex items-center justify-center text-brand text-xl mb-4"><i class="fas fa-wallet"></i></div>
                    <h4 class="font-bold text-sm mb-1">Harga Terjangkau</h4>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-12 h-12 rounded-full bg-pink-50 flex items-center justify-center text-brand text-xl mb-4"><i class="fas fa-map-marker-alt"></i></div>
                    <h4 class="font-bold text-sm mb-1">15 Cabang di Kota Besar</h4>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row gap-12 items-start">
                <div class="w-full md:w-1/2">
                    <h2 class="text-3xl font-serif font-bold text-gray-900 mb-2">Alur Pendaftaran & Booking Mudah</h2>
                    <p class="text-gray-500 text-sm mb-8">Kamu tidak perlu antri lama lagi. Lakukan reservasi online dari rumah, datang sesuai jadwal, dan nikmati perawatan.</p>
                    
                    <div class="space-y-6 relative">
                         <div class="absolute left-3.5 top-0 bottom-0 w-0.5 bg-gray-200"></div>

                        <div class="relative flex items-center gap-4">
                            <div class="w-8 h-8 rounded-full bg-brand text-white flex items-center justify-center text-sm font-bold shrink-0 z-10">1</div>
                            <p class="text-sm text-gray-700">Kunjungi website/aplikasi Nuca dan pilih menu reservasi</p>
                        </div>
                        <div class="relative flex items-center gap-4">
                            <div class="w-8 h-8 rounded-full bg-brand text-white flex items-center justify-center text-sm font-bold shrink-0 z-10">2</div>
                            <p class="text-sm text-gray-700">Pilih jenis perawatan, dokter, dan jadwal</p>
                        </div>
                        <div class="relative flex items-center gap-4">
                            <div class="w-8 h-8 rounded-full bg-brand text-white flex items-center justify-center text-sm font-bold shrink-0 z-10">3</div>
                            <p class="text-sm text-gray-700">Tunggu konfirmasi via WhatsApp/Email</p>
                        </div>
                        <div class="relative flex items-center gap-4">
                            <div class="w-8 h-8 rounded-full bg-brand text-white flex items-center justify-center text-sm font-bold shrink-0 z-10">4</div>
                            <p class="text-sm text-gray-700">Datang 15 menit sebelum jadwal yang ditentukan</p>
                        </div>
                        <div class="relative flex items-center gap-4">
                            <div class="w-8 h-8 rounded-full bg-brand text-white flex items-center justify-center text-sm font-bold shrink-0 z-10">5</div>
                            <p class="text-sm text-gray-700">Verifikasi reservasi di meja resepsionis</p>
                        </div>
                    </div>
                    <button class="mt-8 px-6 py-3 bg-brand text-white rounded-full text-sm font-medium shadow-lg hover:bg-brand-dark transition">Mulai Reservasi Sekarang</button>
                </div>

                <div class="w-full md:w-1/2 bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                    <h3 class="font-serif font-bold text-xl mb-6 text-center">Alur Pemesanan Cepat</h3>
                    <div class="space-y-4">
                        <a href="{{ route('register') }}" class="flex items-center gap-4 p-3 bg-pink-50 rounded-lg hover:bg-pink-100 transition">
                            <div class="w-8 h-8 rounded-full bg-brand text-white flex items-center justify-center text-xs"><i class="fas fa-check"></i></div>
                            <span class="text-sm font-medium">1. Isi Data Diri / Daftar Akun</span>
                        </a>
                        <a href="{{ route('customer.bookings.create') }}" class="flex items-center gap-4 p-3 bg-white border border-gray-100 rounded-lg hover:bg-gray-50 transition">
                            <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-xs">2</div>
                            <span class="text-sm font-medium text-gray-500">2. Pilih Tanggal</span>
                        </a>
                         <div class="flex items-center gap-4 p-3 bg-white border border-gray-100 rounded-lg">
                            <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-xs">3</div>
                            <span class="text-sm font-medium text-gray-500">3. Pilih Dokter</span>
                        </div>
                         <div class="flex items-center gap-4 p-3 bg-white border border-gray-100 rounded-lg">
                            <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-xs">4</div>
                            <span class="text-sm font-medium text-gray-500">4. Konfirmasi & Datang</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="booking-check" class="py-16 bg-white">
        <div class="container mx-auto px-6">
             <div class="text-center mb-8">
                <h2 class="text-2xl font-serif font-bold text-gray-900">Status Pemesanan & Janji Temu Mendatang</h2>
                <p class="text-gray-500 text-sm mt-2">Cek status janji temu Anda dengan memasukkan kode booking.</p>
            </div>

            <!-- Error Message -->
            @if(session('booking_error'))
            <div class="max-w-4xl mx-auto mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm text-red-800">{{ session('booking_error') }}</p>
                </div>
            </div>
            @endif

            <!-- Booking Info Card -->
            @if(session('booking_info'))
            @php $booking = session('booking_info'); @endphp
            <div class="max-w-4xl mx-auto mb-6 bg-gradient-to-r from-pink-50 to-purple-50 border border-pink-200 rounded-xl shadow-lg p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $booking->treatment->name }}</h3>
                        <p class="text-sm text-gray-600">Kode: {{ $booking->booking_code }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold
                        @if($booking->status === 'completed') bg-green-100 text-green-800
                        @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                        @elseif($booking->status === 'deposit_confirmed') bg-blue-100 text-blue-800
                        @elseif($booking->status === 'waiting_deposit') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ strtoupper(str_replace('_', ' ', $booking->status)) }}
                    </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="bg-white rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Tanggal & Jam</p>
                        <p class="font-bold text-gray-900">{{ $booking->booking_date->format('d M Y') }}</p>
                        <p class="text-sm text-brand">{{ $booking->booking_time }} - {{ $booking->end_time }}</p>
                    </div>
                    <div class="bg-white rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Dokter</p>
                        <p class="font-bold text-gray-900">{{ $booking->doctor->name }}</p>
                        <p class="text-sm text-gray-600">{{ $booking->doctor->specialization }}</p>
                    </div>
                    <div class="bg-white rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Total Pembayaran</p>
                        <p class="font-bold text-gray-900">Rp {{ number_format($booking->final_price, 0, ',', '.') }}</p>
                        @if($booking->discount_amount > 0)
                        <p class="text-sm text-green-600">Hemat Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</p>
                        @endif
                    </div>
                </div>

                @if($booking->status === 'waiting_deposit' && $booking->deposit)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm font-bold text-yellow-800 mb-2">⚠️ Menunggu Upload Bukti Transfer</p>
                    <p class="text-xs text-yellow-700">Batas waktu: {{ $booking->deposit->deadline_at->format('d M Y H:i') }}</p>
                    <p class="text-xs text-yellow-700 mt-1">Nominal DP: Rp {{ number_format($booking->deposit->amount, 0, ',', '.') }}</p>
                    <a href="{{ route('customer.bookings.show', $booking->id) }}" class="inline-block mt-3 px-4 py-2 bg-yellow-500 text-white rounded-lg text-xs font-bold hover:bg-yellow-600">
                        Upload Bukti Transfer
                    </a>
                </div>
                @elseif($booking->status === 'deposit_confirmed')
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm font-bold text-blue-800">✓ Deposit Dikonfirmasi - Siap untuk Treatment</p>
                    <p class="text-xs text-blue-700 mt-1">Datang 15 menit sebelum jadwal. Bawa identitas diri.</p>
                </div>
                @elseif($booking->status === 'completed')
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-sm font-bold text-green-800">✓ Treatment Selesai</p>
                    <p class="text-xs text-green-700 mt-1">Terima kasih telah menggunakan layanan kami!</p>
                </div>
                @endif

                <div class="mt-4 flex gap-3">
                    @auth
                    <a href="{{ route('customer.bookings.show', $booking->id) }}" class="px-4 py-2 bg-brand text-white rounded-lg text-sm font-medium hover:bg-brand-dark">
                        Lihat Detail Lengkap
                    </a>
                    @else
                    <a href="{{ route('login') }}" class="px-4 py-2 bg-brand text-white rounded-lg text-sm font-medium hover:bg-brand-dark">
                        Login untuk Detail
                    </a>
                    @endauth
                    <a href="https://wa.me/{{ $clinicInfo['whatsapp'] ?? '' }}?text=Halo, saya ingin tanya tentang booking {{ $booking->booking_code }}" 
                       class="px-4 py-2 bg-green-500 text-white rounded-lg text-sm font-medium hover:bg-green-600">
                        <i class="fab fa-whatsapp mr-1"></i> Hubungi Klinik
                    </a>
                </div>
            </div>
            @endif
            
            <form action="{{ route('check-booking') }}" method="POST" class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                @csrf
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-brand uppercase mb-2">Kode Booking</label>
                        <input type="text" name="booking_code" placeholder="BK-75F1A92EEE" required
                               value="{{ old('booking_code') }}"
                               class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:border-brand">
                        <p class="text-xs text-gray-400 mt-1">*Cek WhatsApp anda untuk kode booking</p>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="px-6 py-3 bg-brand text-white rounded-lg font-medium hover:bg-brand-dark transition shadow-lg whitespace-nowrap">
                            <i class="fas fa-search mr-2"></i>Cek Status
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
             <h2 class="text-3xl font-serif font-bold text-gray-900 text-center mb-12">Apa Kata Klien Kami?</h2>
             <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="flex items-center gap-4 mb-4">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" class="w-12 h-12 rounded-full object-cover">
                        <div>
                            <h5 class="font-bold text-sm">Sarah P.</h5>
                            <div class="text-yellow-400 text-xs"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                        </div>
                    </div>
                    <p class="text-gray-500 text-sm italic">"Pelayanan sangat ramah. Tempatnya bersih dan nyaman. Hasil glowing facialnya langsung terlihat dalam sekali treatment!"</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="flex items-center gap-4 mb-4">
                        <img src="https://randomuser.me/api/portraits/women/68.jpg" class="w-12 h-12 rounded-full object-cover">
                        <div>
                            <h5 class="font-bold text-sm">Rina M.</h5>
                            <div class="text-yellow-400 text-xs"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                        </div>
                    </div>
                    <p class="text-gray-500 text-sm italic">"Dokternya sangat informatif. Tidak memaksakan produk yang tidak perlu. Jerawat saya sembuh total disini."</p>
                </div>
                 <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="flex items-center gap-4 mb-4">
                        <img src="https://randomuser.me/api/portraits/women/32.jpg" class="w-12 h-12 rounded-full object-cover">
                        <div>
                            <h5 class="font-bold text-sm">Tania K.</h5>
                            <div class="text-yellow-400 text-xs"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                        </div>
                    </div>
                    <p class="text-gray-500 text-sm italic">"Harga terjangkau untuk kualitas sebagus ini. Suka banget sama interior kliniknya yang instagramable."</p>
                </div>
             </div>
        </div>
    </section>

    <section id="articles" class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-serif font-bold text-gray-900 text-center mb-12">Artikel Kecantikan</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                {{-- Artikel dummy - Model Article belum ada --}}
                <div class="group cursor-pointer">
                    <div class="rounded-xl overflow-hidden mb-4 relative">
                        <img src="https://images.unsplash.com/photo-1596704017254-9b1b1848fb11?auto=format&fit=crop&q=80&w=400" class="w-full h-48 object-cover group-hover:scale-105 transition duration-500">
                        <span class="absolute top-3 left-3 bg-brand text-white text-[10px] px-2 py-1 rounded">Kulit</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2 group-hover:text-brand transition">Tips Wajah Glowing Alami Tanpa Makeup</h3>
                    <p class="text-xs text-brand font-bold">BACA SELENGKAPNYA <i class="fas fa-arrow-right ml-1"></i></p>
                </div>
                 <div class="group cursor-pointer">
                    <div class="rounded-xl overflow-hidden mb-4 relative">
                        <img src="https://images.unsplash.com/photo-1560750588-73207b1ef5b8?auto=format&fit=crop&q=80&w=400" class="w-full h-48 object-cover group-hover:scale-105 transition duration-500">
                        <span class="absolute top-3 left-3 bg-brand text-white text-[10px] px-2 py-1 rounded">Rambut</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2 group-hover:text-brand transition">Rambut Rontok? Ini Solusi Ampuhnya!</h3>
                    <p class="text-xs text-brand font-bold">BACA SELENGKAPNYA <i class="fas fa-arrow-right ml-1"></i></p>
                </div>
                 <div class="group cursor-pointer">
                    <div class="rounded-xl overflow-hidden mb-4 relative">
                        <img src="https://images.unsplash.com/photo-1616683693504-3ea7e9ad6fec?auto=format&fit=crop&q=80&w=400" class="w-full h-48 object-cover group-hover:scale-105 transition duration-500">
                        <span class="absolute top-3 left-3 bg-brand text-white text-[10px] px-2 py-1 rounded">Tips</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2 group-hover:text-brand transition">Pola Makan Sehat untuk Kulit Awet Muda</h3>
                    <p class="text-xs text-brand font-bold">BACA SELENGKAPNYA <i class="fas fa-arrow-right ml-1"></i></p>
                </div>
            </div>
        </div>
    </section>

    <section id="faq" class="py-16 bg-gray-50">
        <div class="container mx-auto px-6 flex flex-col md:flex-row gap-12">
            <div class="w-full md:w-1/3">
                <h2 class="text-3xl font-serif font-bold text-gray-900 mb-4">FAQ (Frequently Asked Questions)</h2>
                <p class="text-brand font-bold mb-2">{{ $clinicInfo['name'] ?? 'Nuca Beauty Skin' }} Terbaik</p>
                <p class="text-gray-500 text-sm mb-6">Pertanyaan yang sering diajukan oleh calon klien kami. Jika tidak menemukan jawaban, hubungi kami.</p>
                @if(!empty($clinicInfo['whatsapp']))
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $clinicInfo['whatsapp']) }}?text=Halo, saya ingin bertanya tentang perawatan kecantikan" target="_blank" class="inline-block px-6 py-3 bg-brand text-white rounded-full text-sm hover:bg-brand-dark transition"><i class="fab fa-whatsapp mr-2"></i> Hubungi Kami</a>
                @else
                <a href="{{ route('customer.bookings.create') }}" class="inline-block px-6 py-3 bg-brand text-white rounded-full text-sm hover:bg-brand-dark transition"><i class="fas fa-calendar mr-2"></i> Buat Reservasi</a>
                @endif
            </div>
            <div class="w-full md:w-2/3 space-y-4">
                <details class="bg-white rounded-lg shadow-sm group">
                    <summary class="list-none flex justify-between items-center cursor-pointer p-4 font-medium text-gray-800">
                        Apa itu Nuca Beauty Skin?
                        <span class="transition group-open:rotate-180"><i class="fas fa-chevron-down text-gray-400"></i></span>
                    </summary>
                    <div class="p-4 pt-0 text-gray-500 text-sm leading-relaxed border-t border-gray-100 mt-2">
                        Nuca Beauty Skin adalah klinik kecantikan modern yang menyediakan berbagai perawatan kulit dan wajah dengan teknologi terkini dan dokter profesional.
                    </div>
                </details>
                 <details class="bg-white rounded-lg shadow-sm group">
                    <summary class="list-none flex justify-between items-center cursor-pointer p-4 font-medium text-gray-800">
                        Mengapa memilih klinik kecantikan Nuca Beauty Skin?
                        <span class="transition group-open:rotate-180"><i class="fas fa-chevron-down text-gray-400"></i></span>
                    </summary>
                    <div class="p-4 pt-0 text-gray-500 text-sm leading-relaxed border-t border-gray-100 mt-2">
                        Kami mengutamakan kualitas, kebersihan, dan kenyamanan. Dengan harga yang transparan dan kompetitif.
                    </div>
                </details>
                <details class="bg-white rounded-lg shadow-sm group">
                    <summary class="list-none flex justify-between items-center cursor-pointer p-4 font-medium text-gray-800">
                        Apakah krim klinik bikin ketergantungan Nuca Beauty Skin?
                        <span class="transition group-open:rotate-180"><i class="fas fa-chevron-down text-gray-400"></i></span>
                    </summary>
                    <div class="p-4 pt-0 text-gray-500 text-sm leading-relaxed border-t border-gray-100 mt-2">
                        Tidak. Produk kami diformulasikan oleh dokter ahli dan aman digunakan jangka panjang maupun dihentikan.
                    </div>
                </details>
            </div>
        </div>
    </section>

    <footer class="bg-brand text-white pt-16 pb-8">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                         <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-brand font-serif font-bold">N</div>
                        <h3 class="text-xl font-serif font-bold">{{ $clinicInfo['name'] ?? 'Nuca Beauty Skin' }}</h3>
                    </div>
                    <p class="text-pink-100 text-sm mb-4">{{ $clinicInfo['address'] ?? 'Jl. Kesehatan Raya No. 123, Jakarta Selatan, 12345' }}</p>
                    <p class="text-pink-100 text-sm"><i class="fas fa-phone mr-2"></i> {{ $clinicInfo['phone'] ?? '+62 812 3456 7890' }}</p>
                    <p class="text-pink-100 text-sm"><i class="fab fa-whatsapp mr-2"></i> {{ $clinicInfo['whatsapp'] ?? $clinicInfo['phone'] ?? '+62 812 3456 7890' }}</p>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Menu Cepat</h4>
                    <ul class="space-y-2 text-sm text-pink-100">
                        <li><a href="#" class="hover:text-white">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-white">Perawatan</a></li>
                        <li><a href="#" class="hover:text-white">Dokter Kami</a></li>
                        <li><a href="#" class="hover:text-white">Lokasi Cabang</a></li>
                        <li><a href="#" class="hover:text-white">Karir</a></li>
                    </ul>
                </div>
                 <div>
                    <h4 class="font-bold mb-4">Ikuti Kami</h4>
                    <ul class="space-y-2 text-sm text-pink-100">
                        <li><a href="#" class="hover:text-white"><i class="fab fa-instagram mr-2"></i> Instagram</a></li>
                        <li><a href="#" class="hover:text-white"><i class="fab fa-facebook mr-2"></i> Facebook</a></li>
                        <li><a href="#" class="hover:text-white"><i class="fab fa-tiktok mr-2"></i> TikTok</a></li>
                        <li><a href="#" class="hover:text-white"><i class="fab fa-youtube mr-2"></i> YouTube</a></li>
                    </ul>
                </div>
                 <div>
                    <h4 class="font-bold mb-4">Jadwalkan Konsultasi</h4>
                    <p class="text-xs text-pink-100 mb-4">Dapatkan kulit sehat impianmu sekarang juga.</p>
                    @if(!empty($clinicInfo['whatsapp']))
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $clinicInfo['whatsapp']) }}" target="_blank" class="w-full inline-block text-center py-2 bg-white text-brand font-bold rounded-lg hover:bg-pink-50 transition">Chat WhatsApp</a>
                    @else
                    <a href="{{ route('customer.bookings.create') }}" class="w-full inline-block text-center py-2 bg-white text-brand font-bold rounded-lg hover:bg-pink-50 transition">Buat Reservasi</a>
                    @endif
                </div>
            </div>
            <div class="border-t border-pink-400 pt-8 text-center text-sm text-pink-100">
                &copy; {{ date('Y') }} Nuca Beauty Skin. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        const hamburgerIcon = document.getElementById('hamburgerIcon');
        const closeIcon = document.getElementById('closeIcon');

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
                hamburgerIcon.classList.toggle('hidden');
                closeIcon.classList.toggle('hidden');
            });
        }
    </script>

</body>
</html>