@extends('layouts.app')

@section('content')
<div class="py-8">
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Member Badge (if applicable) -->
        @if(Auth::user()->is_member)
        <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border-2 border-yellow-200 rounded-xl shadow-md p-4 mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-amber-900">Status Member Aktif</p>
                    <p class="text-lg font-bold text-amber-700">Diskon {{ Auth::user()->member_discount }}% untuk semua treatment</p>
                </div>
            </div>
            <div class="hidden sm:flex items-center gap-2 px-4 py-2 bg-white rounded-lg border border-yellow-300 shadow-sm">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm font-bold text-yellow-700">Hemat Lebih Banyak!</span>
            </div>
        </div>
        @endif

        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-pink-500 via-purple-500 to-pink-600 rounded-2xl shadow-xl p-8 text-white mb-8">
            <h2 class="text-3xl font-bold mb-2">Selamat Datang, {{ Auth::user()->name }}!</h2>
            <p class="text-pink-100">Kelola booking treatment Anda dengan mudah</p>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 border border-pink-100 hover:shadow-xl transition">
                <div class="flex items-center">
                    <div class="p-3 bg-gradient-to-br from-pink-100 to-purple-100 rounded-xl">
                        <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600 font-medium">Total Booking</p>
                        <p class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent">{{ $totalBookings ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border border-green-100 hover:shadow-xl transition">
                <div class="flex items-center">
                    <div class="p-3 bg-gradient-to-br from-green-100 to-emerald-100 rounded-xl">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600 font-medium">Selesai</p>
                        <p class="text-2xl font-bold text-green-600">{{ $completedBookings ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border border-yellow-100 hover:shadow-xl transition">
                <div class="flex items-center">
                    <div class="p-3 bg-gradient-to-br from-yellow-100 to-amber-100 rounded-xl">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600 font-medium">Menunggu</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $pendingBookings ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <a href="{{ route('customer.bookings.create') }}" class="bg-white rounded-xl shadow-lg hover:shadow-xl transition p-6 flex items-center border border-pink-100 group">
                <div class="p-4 bg-gradient-to-br from-pink-100 to-purple-100 rounded-xl group-hover:from-pink-200 group-hover:to-purple-200 transition">
                    <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-pink-600 transition">Booking Baru</h3>
                    <p class="text-sm text-gray-600">Buat booking treatment baru</p>
                </div>
            </a>

            <a href="{{ route('customer.bookings.index') }}" class="bg-white rounded-xl shadow-lg hover:shadow-xl transition p-6 flex items-center border border-purple-100 group">
                <div class="p-4 bg-gradient-to-br from-purple-100 to-pink-100 rounded-xl group-hover:from-purple-200 group-hover:to-pink-200 transition">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-purple-600 transition">Riwayat Booking</h3>
                    <p class="text-sm text-gray-600">Lihat semua booking Anda</p>
                </div>
            </a>
        </div>

        <!-- Recent Bookings -->
        <div class="bg-white rounded-xl shadow-lg border border-pink-100">
            <div class="px-6 py-4 border-b border-pink-100 bg-gradient-to-r from-pink-50 to-purple-50">
                <h3 class="text-lg font-bold text-gray-900">Booking Terakhir</h3>
            </div>
            <div class="p-6">
                @if(isset($recentBookings) && $recentBookings->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentBookings as $booking)
                        <div class="flex items-center justify-between p-4 border-2 border-gray-100 rounded-xl hover:border-pink-200 hover:shadow-md transition">
                            <div>
                                <p class="font-bold text-gray-900">{{ $booking->treatment->name }}</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $booking->booking_date->format('d M Y') }} - {{ $booking->booking_time }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ $booking->doctor->name }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 text-xs font-bold rounded-full
                                    @if($booking->status == 'completed') bg-green-100 text-green-800 border border-green-200
                                    @elseif($booking->status == 'deposit_confirmed') bg-blue-100 text-blue-800 border border-blue-200
                                    @elseif($booking->status == 'waiting_deposit') bg-yellow-100 text-yellow-800 border border-yellow-200
                                    @elseif($booking->status == 'deposit_rejected') bg-red-100 text-red-800 border border-red-200
                                    @elseif($booking->status == 'auto_approved') bg-purple-100 text-purple-800 border border-purple-200
                                    @elseif($booking->status == 'expired') bg-gray-100 text-gray-800 border border-gray-200
                                    @else bg-pink-100 text-pink-800 border border-pink-200
                                    @endif">
                                    {{ strtoupper(str_replace('_', ' ', $booking->status)) }}
                                </span>
                                <p class="text-sm font-bold text-gray-900 mt-2">Rp {{ number_format($booking->final_price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-24 h-24 mx-auto mb-4 bg-gradient-to-br from-pink-100 to-purple-100 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <p class="text-gray-600 mb-4">Belum ada booking</p>
                        <a href="{{ route('customer.bookings.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-xl hover:from-pink-600 hover:to-purple-700 transition shadow-lg font-medium">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Buat Booking Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
