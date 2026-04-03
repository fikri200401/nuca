@extends('layouts.admin')

@section('title', 'Bookings Management')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Bookings Management</h1>
            <p class="mt-2 text-sm text-gray-700">Kelola semua booking treatment dari customer</p>
        </div>
        @canDo('bookings', 'add')
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <a href="{{ route('admin.bookings.create') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                + Booking Manual (dari WhatsApp)
            </a>
        </div>
        @endCanDo
    </div>

    <!-- Filter -->
    <div class="mt-6 bg-white shadow sm:rounded-lg p-5">
        <form method="GET" action="{{ route('admin.bookings.index') }}">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">

                {{-- Search --}}
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Cari</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Kode booking, nama, WhatsApp..."
                               class="block w-full rounded-lg border-gray-300 pl-10 pr-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Status</label>
                    <select name="status" class="block w-full rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua Status</option>
                        <option value="auto_approved"      {{ request('status') == 'auto_approved'      ? 'selected' : '' }}>Auto Approved</option>
                        <option value="waiting_deposit"    {{ request('status') == 'waiting_deposit'    ? 'selected' : '' }}>Menunggu DP</option>
                        <option value="deposit_confirmed"  {{ request('status') == 'deposit_confirmed'  ? 'selected' : '' }}>DP Terkonfirmasi</option>
                        <option value="deposit_rejected"   {{ request('status') == 'deposit_rejected'   ? 'selected' : '' }}>DP Ditolak</option>
                        <option value="completed"          {{ request('status') == 'completed'          ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled"          {{ request('status') == 'cancelled'          ? 'selected' : '' }}>Dibatalkan</option>
                        <option value="expired"            {{ request('status') == 'expired'            ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>

                {{-- Sort --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Urutkan</label>
                    <select name="sort" class="block w-full rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort') == 'oldest'           ? 'selected' : '' }}>Terlama</option>
                    </select>
                </div>

                {{-- Buttons --}}
                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="flex-1 inline-flex justify-center items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                        Filter
                    </button>
                    @if(request()->hasAny(['search','status','sort','date_from','date_to']))
                    <a href="{{ route('admin.bookings.index') }}"
                       class="inline-flex justify-center items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-600 shadow-sm hover:bg-gray-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                    @endif
                </div>

            </div>

            {{-- Date range (row 2) --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mt-4 border-t border-gray-100 pt-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="block w-full rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="block w-full rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
        </form>

        {{-- Active filter badges --}}
        @if(request()->hasAny(['search','status','sort','date_from','date_to']))
        <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-gray-100">
            <span class="text-xs text-gray-400 self-center">Filter aktif:</span>
            @if(request('search'))
                <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 border border-indigo-200 px-2.5 py-0.5 text-xs font-medium text-indigo-700">
                    Cari: "{{ request('search') }}"
                </span>
            @endif
            @if(request('status'))
                <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 border border-indigo-200 px-2.5 py-0.5 text-xs font-medium text-indigo-700">
                    Status: {{ request('status') }}
                </span>
            @endif
            @if(request('date_from') || request('date_to'))
                <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 border border-indigo-200 px-2.5 py-0.5 text-xs font-medium text-indigo-700">
                    Tanggal: {{ request('date_from') ?: '...' }} – {{ request('date_to') ?: '...' }}
                </span>
            @endif
            @if(request('sort') == 'oldest')
                <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 border border-indigo-200 px-2.5 py-0.5 text-xs font-medium text-indigo-700">
                    Urut: Terlama
                </span>
            @endif
        </div>
        @endif
    </div>

    <!-- Table -->
    <div class="mt-8 flex flex-col">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Kode Booking</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Customer</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Treatment</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Dokter</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Tanggal & Jam</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Harga</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($bookings as $booking)
                            <tr>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                    {{ $booking->booking_code }}
                                    @if($booking->is_manual_entry)
                                        <span class="ml-2 inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">WhatsApp</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <div class="font-medium text-gray-900">{{ $booking->user->name }}</div>
                                    <div class="text-gray-500">{{ $booking->user->whatsapp_number }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $booking->treatment->name }}
                                    <div class="text-xs text-gray-400">{{ $booking->treatment->duration }} menit</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $booking->doctor->name }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <div>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $booking->booking_time }} - {{ $booking->end_time }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    @php
                                        $statusColors = [
                                            'auto_approved' => 'bg-green-100 text-green-800',
                                            'waiting_deposit' => 'bg-yellow-100 text-yellow-800',
                                            'deposit_confirmed' => 'bg-blue-100 text-blue-800',
                                            'deposit_rejected' => 'bg-red-100 text-red-800',
                                            'expired' => 'bg-gray-100 text-gray-800',
                                            'completed' => 'bg-purple-100 text-purple-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                        $statusLabels = [
                                            'auto_approved' => 'Approved',
                                            'waiting_deposit' => 'Menunggu DP',
                                            'deposit_confirmed' => 'DP OK',
                                            'deposit_rejected' => 'DP Ditolak',
                                            'expired' => 'Expired',
                                            'completed' => 'Selesai',
                                            'cancelled' => 'Dibatalkan',
                                        ];
                                    @endphp
                                    <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 {{ $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusLabels[$booking->status] ?? $booking->status }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    Rp {{ number_format($booking->final_price, 0, ',', '.') }}
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <a href="{{ route('admin.bookings.show', $booking->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-3 py-8 text-center text-sm text-gray-500">
                                    Tidak ada data booking
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $bookings->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
