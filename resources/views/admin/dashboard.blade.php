@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="p-6">
    <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500">Booking Hari Ini</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_bookings_today'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500">DP Pending</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_deposits'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500">DP Expired</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['expired_deposits'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500">Total Member</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_members'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500">Voucher Aktif</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_vouchers'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <a href="{{ route('admin.bookings.index') }}" class="block bg-white rounded-lg shadow hover:shadow-lg transition p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Kelola Booking</h3>
                <p class="text-sm text-gray-600">Lihat dan kelola semua booking</p>
            </a>
            <a href="{{ route('admin.treatments.index') }}" class="block bg-white rounded-lg shadow hover:shadow-lg transition p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Kelola Treatment</h3>
                <p class="text-sm text-gray-600">Tambah dan edit treatment</p>
            </a>
            <a href="{{ route('admin.doctors.index') }}" class="block bg-white rounded-lg shadow hover:shadow-lg transition p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Kelola Dokter</h3>
                <p class="text-sm text-gray-600">Atur jadwal dokter</p>
            </a>
            <a href="{{ route('admin.deposits.index') }}" class="block bg-white rounded-lg shadow hover:shadow-lg transition p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Verifikasi DP</h3>
                <p class="text-sm text-gray-600">Approve/reject deposit</p>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Upcoming Bookings -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Booking Mendatang</h2>
                </div>
                <div class="p-6">
                    @if($upcomingBookings->count() > 0)
                    <div class="space-y-4">
                        @foreach($upcomingBookings as $booking)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $booking->user->name }}</p>
                                <p class="text-sm text-gray-600">{{ $booking->treatment->name }}</p>
                                <p class="text-sm text-gray-500">Dr. {{ $booking->doctor->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</p>
                                <p class="text-sm text-gray-600">{{ $booking->booking_time }}</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($booking->status == 'auto_approved') bg-green-100 text-green-800
                                    @elseif($booking->status == 'waiting_deposit') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-500 text-center py-8">Tidak ada booking mendatang</p>
                    @endif
                </div>
            </div>

            <!-- Pending Deposits -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Deposit Pending</h2>
                </div>
                <div class="p-6">
                    @if($pendingDeposits->count() > 0)
                    <div class="space-y-4">
                        @foreach($pendingDeposits as $deposit)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $deposit->booking->user->name }}</p>
                                <p class="text-sm text-gray-600">{{ $deposit->booking->treatment->name }}</p>
                                <p class="text-sm text-gray-500">Rp {{ number_format($deposit->amount, 0, ',', '.') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-red-600">
                                    Deadline: {{ \Carbon\Carbon::parse($deposit->deadline_at)->format('d M H:i') }}
                                </p>
                                <a href="{{ route('admin.deposits.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                                    Verifikasi →
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-500 text-center py-8">Tidak ada deposit pending</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
