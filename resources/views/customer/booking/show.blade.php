@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="container mx-auto px-4 max-w-5xl">
        {{-- Back Button --}}
        <div class="mb-6">
            <a href="{{ route('customer.bookings.index') }}" class="inline-flex items-center text-pink-600 hover:text-pink-700 font-semibold transition group">
                <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Riwayat Booking
            </a>
        </div>

        {{-- Alert Messages --}}
        @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-xl shadow-sm animate-fade-in">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-xl shadow-sm animate-fade-in">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-red-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1">
                    @foreach($errors->all() as $error)
                    <p class="text-sm font-medium text-red-800">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Header --}}
        <div class="bg-white rounded-2xl shadow-lg border border-pink-100 p-6 mb-6">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent mb-2">{{ $booking->treatment->name }}</h1>
                    <p class="text-gray-600 font-mono text-sm bg-pink-50 border border-pink-200 px-3 py-1 rounded-full inline-block">{{ $booking->booking_code }}</p>
                </div>
                <div>
                    @if($booking->status === 'auto_approved')
                        <span class="px-4 py-2 bg-green-100 text-green-700 text-sm font-semibold rounded-full">Auto Approved</span>
                    @elseif($booking->status === 'waiting_deposit')
                        <span class="px-4 py-2 bg-yellow-100 text-yellow-700 text-sm font-semibold rounded-full">Menunggu Deposit</span>
                    @elseif($booking->status === 'deposit_confirmed')
                        <span class="px-4 py-2 bg-blue-100 text-blue-700 text-sm font-semibold rounded-full">Deposit Terkonfirmasi</span>
                    @elseif($booking->status === 'deposit_rejected')
                        <span class="px-4 py-2 bg-red-100 text-red-700 text-sm font-semibold rounded-full">Deposit Ditolak</span>
                    @elseif($booking->status === 'completed')
                        <span class="px-4 py-2 bg-purple-100 text-purple-700 text-sm font-semibold rounded-full">Selesai</span>
                    @elseif($booking->status === 'cancelled')
                        <span class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-full">Dibatalkan</span>
                    @elseif($booking->status === 'expired')
                        <span class="px-4 py-2 bg-orange-100 text-orange-700 text-sm font-semibold rounded-full">Kadaluarsa</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Booking Details --}}
            <div class="bg-white rounded-2xl shadow-lg border border-pink-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-pink-500 to-purple-600 flex items-center justify-center mr-3 shadow-lg">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    Detail Booking
                </h2>
                
                <div class="space-y-5">
                    <div class="pb-5 border-b border-pink-100">
                        <label class="text-xs uppercase tracking-wide text-gray-500 font-semibold mb-2 block">Tanggal & Waktu</label>
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="font-bold text-gray-900 text-lg">{{ $booking->booking_date->format('d F Y') }}</p>
                        </div>
                        <p class="text-gray-600 ml-7">{{ $booking->booking_time }} - {{ $booking->end_time }} WIB</p>
                    </div>

                    <div class="pb-5 border-b border-pink-100">
                        <label class="text-xs uppercase tracking-wide text-gray-500 font-semibold mb-2 block">Dokter</label>
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <p class="font-bold text-gray-900 text-lg">{{ $booking->doctor->name }}</p>
                        </div>
                        <p class="text-sm text-gray-600 ml-7">{{ $booking->doctor->specialization ?? 'Dokter Kecantikan' }}</p>
                    </div>

                    <div class="pb-5 border-b border-pink-100">
                        <label class="text-xs uppercase tracking-wide text-gray-500 font-semibold mb-2 block">Treatment</label>
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="font-bold text-gray-900">{{ $booking->treatment->name }}</p>
                        </div>
                        <p class="text-sm text-gray-600 ml-7">Durasi: {{ $booking->treatment->duration_minutes }} menit</p>
                    </div>

                    @if($booking->customer_notes)
                    <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                        <label class="text-xs uppercase tracking-wide text-blue-700 font-semibold mb-2 block">Catatan Anda</label>
                        <p class="text-gray-800">{{ $booking->customer_notes }}</p>
                    </div>
                    @endif

                    @if($booking->admin_notes)
                    <div class="bg-orange-50 rounded-xl p-4 border border-orange-100">
                        <label class="text-xs uppercase tracking-wide text-orange-700 font-semibold mb-2 block">Catatan Admin</label>
                        <p class="text-gray-800">{{ $booking->admin_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Payment Details --}}
            <div class="bg-white rounded-2xl shadow-lg border border-pink-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-green-500 to-emerald-500 flex items-center justify-center mr-3 shadow-lg">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    Detail Pembayaran
                </h2>
                
                <div class="space-y-4">
                    <div class="flex justify-between text-gray-700">
                        <span>Harga Treatment</span>
                        <span class="font-semibold">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                    </div>

                    @if($booking->discount_amount > 0)
                    <div class="flex justify-between text-green-600">
                        <span>Diskon</span>
                        <span class="font-semibold">- Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif

                    <div class="border-t-2 border-pink-200 pt-4 flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-900">Total</span>
                        <span class="text-3xl font-bold bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent">
                            Rp {{ number_format($booking->final_price, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- Deposit Info --}}
                @if($booking->deposit)
                <div class="mt-6 pt-6 border-t-2 border-pink-200">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Informasi Deposit
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Jumlah Deposit</span>
                            <span class="font-bold text-gray-900">Rp {{ number_format($booking->deposit->amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Status</span>
                            <span class="font-semibold">
                                @if($booking->deposit->status === 'pending')
                                    <span class="text-yellow-600 bg-yellow-50 px-2 py-1 rounded">Menunggu Upload</span>
                                @elseif($booking->deposit->status === 'submitted')
                                    <span class="text-blue-600 bg-blue-50 px-2 py-1 rounded">Menunggu Verifikasi</span>
                                @elseif($booking->deposit->status === 'approved')
                                    <span class="text-green-600 bg-green-50 px-2 py-1 rounded">Disetujui</span>
                                @elseif($booking->deposit->status === 'rejected')
                                    <span class="text-red-600 bg-red-50 px-2 py-1 rounded">Ditolak</span>
                                @endif
                            </span>
                        </div>

                        @if($booking->deposit->proof_of_payment)
                        <div class="mt-4">
                            <label class="text-xs uppercase tracking-wide text-gray-500 font-semibold mb-2 block">Bukti Transfer</label>
                            <a href="{{ asset('storage/' . $booking->deposit->proof_of_payment) }}" target="_blank" class="block">
                                <img src="{{ asset('storage/' . $booking->deposit->proof_of_payment) }}" alt="Bukti Transfer" class="w-full rounded-xl border-2 border-pink-200 shadow-lg hover:shadow-xl transition cursor-pointer hover:border-pink-400">
                            </a>
                            <p class="text-xs text-gray-500 mt-2 text-center">Klik gambar untuk memperbesar</p>
                        </div>
                        @endif

                        @if($booking->deposit->status === 'rejected' && $booking->deposit->rejection_reason)
                        <div class="mt-4">
                            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-xl">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-red-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <div class="flex-1">
                                        <p class="text-xs uppercase tracking-wide text-red-700 font-semibold mb-1">Alasan Penolakan</p>
                                        <p class="text-sm text-red-800 font-medium">{{ $booking->deposit->rejection_reason }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($booking->deposit->status === 'pending' || $booking->deposit->status === 'rejected')
                        <button onclick="showUploadModal()" class="w-full mt-4 px-5 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:from-green-600 hover:to-emerald-700 transition-all duration-200 transform hover:-translate-y-0.5">
                            {{ $booking->deposit->status === 'rejected' ? 'Upload Ulang Bukti Transfer' : 'Upload Bukti Transfer' }}
                        </button>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Before After Photos --}}
        @if($booking->beforeAfterPhotos && $booking->beforeAfterPhotos->count() > 0)
        <div class="bg-white rounded-2xl shadow-lg border border-pink-100 p-6 mt-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center mr-3 shadow-lg">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                Foto Before & After
            </h2>
            
            <div class="grid grid-cols-1 gap-6">
                @foreach($booking->beforeAfterPhotos as $photo)
                <div class="border-2 border-pink-200 rounded-2xl p-5 hover:border-pink-300 transition shadow-md">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">Before</p>
                            <img src="{{ asset('storage/' . $photo->before_photo) }}" alt="Before" class="w-full rounded-xl shadow-lg">
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">After</p>
                            <img src="{{ asset('storage/' . $photo->after_photo) }}" alt="After" class="w-full rounded-xl shadow-lg">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Feedback Section --}}
        @if($booking->status === 'completed')
            @if($booking->feedback)
            <div class="bg-white rounded-2xl shadow-lg border border-pink-100 p-6 mt-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center mr-3 shadow-lg">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    Review Anda
                </h2>
                
                <div class="flex items-center gap-2 mb-4">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $booking->feedback->rating)
                            <svg class="w-7 h-7 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @else
                            <svg class="w-7 h-7 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endif
                    @endfor
                    <span class="ml-2 text-gray-600 font-semibold">{{ $booking->feedback->rating }}/5</span>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-4 border border-pink-100">
                    <p class="text-gray-800 leading-relaxed">{{ $booking->feedback->comment }}</p>
                </div>
                <p class="text-sm text-gray-500 mt-3">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Ditulis pada {{ $booking->feedback->created_at->format('d F Y, H:i') }}
                </p>
            </div>
            @else
            <div class="bg-gradient-to-br from-pink-50 to-purple-50 rounded-2xl border-2 border-pink-200 p-8 mt-6 text-center shadow-lg">
                <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Berikan Review Anda</h3>
                <p class="text-gray-600 mb-6">Bagaimana pengalaman Anda dengan treatment ini?</p>
                <a href="{{ route('customer.feedback.create', $booking->id) }}" class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:from-pink-600 hover:to-purple-700 transition-all duration-200 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    Tulis Review Sekarang
                </a>
            </div>
            @endif
        @endif
    </div>
</div>

{{-- Upload Deposit Modal --}}
<div id="uploadDepositModal" class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 p-4 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-full">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all border border-pink-100">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent">Upload Bukti Deposit</h3>
            <button onclick="closeUploadModal()" class="text-gray-400 hover:text-pink-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('customer.bookings.upload-deposit', $booking->id) }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Bukti Transfer</label>
                <div class="relative border-2 border-dashed border-pink-300 rounded-xl p-6 text-center hover:border-pink-400 transition bg-pink-50">
                    <input type="file" name="proof_of_payment" accept="image/*" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="previewImage(this)">
                    <div id="uploadPlaceholder">
                        <svg class="w-12 h-12 text-pink-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-sm text-pink-700 font-medium">Klik untuk upload gambar</p>
                        <p class="text-xs text-pink-500 mt-1">JPG, PNG (Max: 2MB)</p>
                    </div>
                    <div id="imagePreview" class="hidden">
                        <img src="" alt="Preview" class="max-h-48 mx-auto rounded-xl shadow-lg">
                        <p class="text-xs text-gray-500 mt-2">Klik lagi untuk mengganti</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeUploadModal()" class="flex-1 px-5 py-3 border-2 border-pink-300 text-gray-700 font-semibold rounded-xl hover:bg-pink-50 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-5 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:from-pink-600 hover:to-purple-700 transition-all duration-200 transform hover:-translate-y-0.5">
                    Upload
                </button>
            </div>
        </form>
    </div>
    </div>
</div>

@push('scripts')
<script>
function showUploadModal() {
    const modal = document.getElementById('uploadDepositModal');
    modal.classList.remove('hidden');
    modal.style.display = 'block';
}

function closeUploadModal() {
    const modal = document.getElementById('uploadDepositModal');
    modal.classList.add('hidden');
    modal.style.display = 'none';
    document.getElementById('uploadPlaceholder').classList.remove('hidden');
    document.getElementById('imagePreview').classList.add('hidden');
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').querySelector('img').src = e.target.result;
            document.getElementById('uploadPlaceholder').classList.add('hidden');
            document.getElementById('imagePreview').classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Close modal when clicking outside
document.getElementById('uploadDepositModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeUploadModal();
    }
});
</script>
@endpush
@endsection
