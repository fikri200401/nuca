@extends('layouts.admin')

@section('title', 'Deposit Verification')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Deposit Verification</h1>
            <p class="mt-2 text-sm text-gray-700">Verifikasi bukti pembayaran DP dari customer</p>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="mt-6">
        <div class="hidden sm:block">
            <nav class="flex space-x-1 border-b border-gray-200" aria-label="Tabs">
                @php
                    $tabs = [
                        'pending'   => ['label' => 'Pending',   'color' => 'yellow'],
                        'submitted' => ['label' => 'Submitted', 'color' => 'blue'],
                        'approved'  => ['label' => 'Approved',  'color' => 'green'],
                        'rejected'  => ['label' => 'Rejected',  'color' => 'red'],
                        'expired'   => ['label' => 'Expired',   'color' => 'gray'],
                    ];
                    $activeStatus = request('status', 'pending');
                    $submittedCount = \App\Models\Deposit::where('status', 'submitted')->count();
                @endphp
                @foreach($tabs as $key => $tab)
                @php $isActive = $activeStatus === $key; @endphp
                <a href="{{ route('admin.deposits.index', array_merge(request()->except(['status','page']), ['status' => $key])) }}"
                   class="relative px-4 py-3 text-sm font-medium border-b-2 transition-colors
                          {{ $isActive
                              ? 'border-indigo-600 text-indigo-600'
                              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    {{ $tab['label'] }}
                    @if($key === 'submitted' && $submittedCount > 0)
                        <span class="ml-1.5 inline-flex items-center justify-center rounded-full bg-blue-500 px-1.5 py-0.5 text-xs font-bold text-white leading-none">
                            {{ $submittedCount }}
                        </span>
                    @endif
                </a>
                @endforeach
            </nav>
        </div>
    </div>

    <!-- Search + Sort bar -->
    <div class="mt-4 bg-white shadow sm:rounded-lg p-4">
        <form method="GET" action="{{ route('admin.deposits.index') }}">
            {{-- Preserve active status tab --}}
            <input type="hidden" name="status" value="{{ request('status', 'pending') }}">

            <div class="flex flex-col sm:flex-row gap-3">
                {{-- Search --}}
                <div class="flex-1 relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari kode booking, nama, WhatsApp..."
                           class="block w-full rounded-lg border-gray-300 pl-10 pr-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                {{-- Sort --}}
                <div class="sm:w-44">
                    <select name="sort" class="block w-full rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Deadline Terbaru</option>
                        <option value="oldest" {{ request('sort') == 'oldest'           ? 'selected' : '' }}>Deadline Terlama</option>
                    </select>
                </div>

                {{-- Apply --}}
                <button type="submit"
                        class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                    Filter
                </button>

                {{-- Reset search/sort only --}}
                @if(request('search') || (request('sort') && request('sort') !== 'newest'))
                <a href="{{ route('admin.deposits.index', ['status' => request('status', 'pending')]) }}"
                   class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-600 shadow-sm hover:bg-gray-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </a>
                @endif
            </div>
        </form>
    </div>

    <div class="mt-8 flex flex-col">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Booking</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Customer</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Jumlah DP</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Deadline</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                <th class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($deposits as $deposit)
                            <tr>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                    <div class="font-medium text-gray-900">{{ $deposit->booking->booking_code }}</div>
                                    <div class="text-xs text-gray-500">{{ $deposit->booking->treatment->name }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <div class="font-medium text-gray-900">{{ $deposit->booking->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $deposit->booking->user->whatsapp_number }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    Rp {{ number_format($deposit->amount, 0, ',', '.') }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $deposit->deadline_at->format('d/m/Y H:i') }}
                                    @if($deposit->status == 'pending' && $deposit->deadline_at->isPast())
                                        <span class="block text-xs text-red-600">Melewati deadline</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'submitted' => 'bg-blue-100 text-blue-800',
                                            'approved' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            'expired' => 'bg-gray-100 text-gray-800',
                                        ];
                                    @endphp
                                    <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 {{ $statusColors[$deposit->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst(str_replace('_', ' ', $deposit->status)) }}
                                    </span>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <a href="{{ route('admin.deposits.show', $deposit->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-3 py-8 text-center text-sm text-gray-500">
                                    Tidak ada deposit dengan status ini
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $deposits->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
