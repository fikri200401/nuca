@extends('layouts.admin')

@section('title', 'Penggunaan Voucher - ' . $voucher->code)

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('admin.vouchers.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
            ← Kembali ke Daftar Voucher
        </a>
    </div>

    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
            Riwayat Penggunaan Voucher
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            Kode: <span class="font-semibold">{{ $voucher->code }}</span> - {{ $voucher->name }}
        </p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3 mb-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Total Penggunaan</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    {{ $voucher->usages()->count() }}
                    @if($voucher->max_usage)
                        <span class="text-lg text-gray-500">/ {{ $voucher->max_usage }}</span>
                    @endif
                </dd>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Tipe Diskon</dt>
                <dd class="mt-1 text-3xl font-semibold text-indigo-600">
                    @if($voucher->type === 'nominal')
                        Rp {{ number_format($voucher->value, 0, ',', '.') }}
                    @else
                        {{ $voucher->value }}%
                    @endif
                </dd>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Periode</dt>
                <dd class="mt-1 text-sm font-semibold text-gray-900">
                    {{ \Carbon\Carbon::parse($voucher->valid_from)->format('d/m/Y') }}
                    <br>
                    {{ \Carbon\Carbon::parse($voucher->valid_until)->format('d/m/Y') }}
                </dd>
            </div>
        </div>
    </div>

    <!-- Usage List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Daftar Penggunaan
            </h3>
        </div>
        <ul role="list" class="divide-y divide-gray-200">
            @forelse($usages as $usage)
            <li class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <div>
                                <p class="text-sm font-medium text-indigo-600">
                                    {{ $usage->user->name }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ $usage->user->whatsapp_number }}
                                </p>
                                @if($usage->booking)
                                <p class="text-xs text-gray-400 mt-1">
                                    Booking: {{ $usage->booking->booking_number }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="ml-4 flex-shrink-0 text-right">
                        <p class="text-sm font-semibold text-gray-900">
                            Rp {{ number_format($usage->discount_amount, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $usage->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
            </li>
            @empty
            <li class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada penggunaan</h3>
                <p class="mt-1 text-sm text-gray-500">Voucher ini belum pernah digunakan.</p>
            </li>
            @endforelse
        </ul>

        @if($usages->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $usages->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
