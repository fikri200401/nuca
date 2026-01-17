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
        <div class="sm:hidden">
            <select class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                <option>Pending</option>
                <option>Approved</option>
                <option>Rejected</option>
                <option>Expired</option>
            </select>
        </div>
        <div class="hidden sm:block">
            <nav class="flex space-x-4" aria-label="Tabs">
                <a href="{{ route('admin.deposits.index', ['status' => 'pending']) }}" 
                   class="{{ (!request('status') || request('status') == 'pending') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:text-gray-700' }} rounded-md px-3 py-2 text-sm font-medium">
                    Pending
                </a>
                <a href="{{ route('admin.deposits.index', ['status' => 'approved']) }}" 
                   class="{{ request('status') == 'approved' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:text-gray-700' }} rounded-md px-3 py-2 text-sm font-medium">
                    Approved
                </a>
                <a href="{{ route('admin.deposits.index', ['status' => 'rejected']) }}" 
                   class="{{ request('status') == 'rejected' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:text-gray-700' }} rounded-md px-3 py-2 text-sm font-medium">
                    Rejected
                </a>
                <a href="{{ route('admin.deposits.index', ['status' => 'expired']) }}" 
                   class="{{ request('status') == 'expired' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:text-gray-700' }} rounded-md px-3 py-2 text-sm font-medium">
                    Expired
                </a>
            </nav>
        </div>
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
                                            'approved' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            'expired' => 'bg-gray-100 text-gray-800',
                                        ];
                                    @endphp
                                    <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 {{ $statusColors[$deposit->status] }}">
                                        {{ ucfirst($deposit->status) }}
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
