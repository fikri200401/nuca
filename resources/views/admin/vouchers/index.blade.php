@extends('layouts.admin')

@section('title', 'Vouchers Management')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Vouchers Management</h1>
            <p class="mt-2 text-sm text-gray-700">Kelola voucher & promo bulanan</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <a href="{{ route('admin.vouchers.create') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                + Buat Voucher
            </a>
        </div>
    </div>

    <div class="mt-8 flex flex-col">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Kode Voucher</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Nama</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Tipe</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Nilai</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Periode</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Penggunaan</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                <th class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($vouchers as $voucher)
                            <tr>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                    {{ $voucher->code }}
                                    @if($voucher->show_on_landing)
                                        <span class="ml-2 inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800">Landing</span>
                                    @endif
                                </td>
                                <td class="px-3 py-4 text-sm text-gray-500">
                                    <div class="font-medium text-gray-900">{{ $voucher->name }}</div>
                                    @if($voucher->min_transaction > 0)
                                        <div class="text-xs text-gray-500">Min. Transaksi: Rp {{ number_format($voucher->min_transaction, 0, ',', '.') }}</div>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <span class="inline-flex rounded-full px-2 text-xs font-semibold {{ $voucher->type == 'percentage' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $voucher->type == 'percentage' ? 'Persentase' : 'Nominal' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    @if($voucher->type == 'percentage')
                                        {{ $voucher->value }}%
                                    @else
                                        Rp {{ number_format($voucher->value, 0, ',', '.') }}
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <div>{{ $voucher->valid_from->format('d/m/Y') }}</div>
                                    <div class="text-xs">s/d {{ $voucher->valid_until->format('d/m/Y') }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $voucher->usage_count }} / {{ $voucher->max_usage ?? '∞' }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    @if($voucher->is_active)
                                        <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">Aktif</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-red-100 px-2 text-xs font-semibold leading-5 text-red-800">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <a href="{{ route('admin.vouchers.usage', $voucher->id) }}" class="text-purple-600 hover:text-purple-900 mr-3">Usage</a>
                                    <a href="{{ route('admin.vouchers.edit', $voucher->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <form action="{{ route('admin.vouchers.toggle-status', $voucher->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                            {{ $voucher->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.vouchers.destroy', $voucher->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus voucher ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-3 py-8 text-center text-sm text-gray-500">
                                    Belum ada voucher. <a href="{{ route('admin.vouchers.create') }}" class="text-indigo-600">Buat voucher pertama</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $vouchers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
