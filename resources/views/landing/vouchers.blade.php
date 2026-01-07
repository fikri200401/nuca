@extends('layouts.base')

@section('title', 'Promo & Voucher')

@section('content')
<div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Promo & Voucher 🎉</h1>
        <p class="text-xl text-indigo-100">Dapatkan diskon menarik untuk treatment favorit Anda</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    @if($vouchers->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($vouchers as $voucher)
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
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-gray-600">Berlaku:</span>
                        <span class="font-semibold">{{ $voucher->valid_from->format('d/m/Y') }} - {{ $voucher->valid_until->format('d/m/Y') }}</span>
                    </div>
                    @if($voucher->max_usage)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Sisa Kuota:</span>
                        <span class="font-semibold">{{ $voucher->max_usage - $voucher->usage_count }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <p class="text-gray-500 text-lg">Belum ada voucher aktif saat ini.</p>
        </div>
    @endif
</div>
@endsection
