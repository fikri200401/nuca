@extends('layouts.admin')

@section('title', 'Members Management')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Members Management</h1>
            <p class="mt-2 text-sm text-gray-700">Kelola member dan diskon khusus</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="mt-6 bg-white shadow sm:rounded-lg p-4">
        <form method="GET" action="{{ route('admin.members.index') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <label for="is_member" class="block text-sm font-medium text-gray-700">Status Member</label>
                <select name="is_member" id="is_member" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Semua Customer</option>
                    <option value="1" {{ request('is_member') == '1' ? 'selected' : '' }}>Member Aktif</option>
                    <option value="0" {{ request('is_member') == '0' ? 'selected' : '' }}>Bukan Member</option>
                </select>
            </div>

            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Cari</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nama / WhatsApp / Member Number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                    Filter
                </button>
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
                                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Customer</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">WhatsApp</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Member Number</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Total Booking</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Diskon</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                <th class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($members as $member)
                            <tr>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                    <div class="font-medium text-gray-900">{{ $member->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $member->email ?? '-' }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $member->whatsapp_number }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $member->member_number ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $member->bookings_count ?? 0 }} booking
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $member->member_discount }}%
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    @if($member->is_member)
                                        <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">Member</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-gray-100 px-2 text-xs font-semibold leading-5 text-gray-800">Customer</span>
                                    @endif
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <a href="{{ route('admin.members.show', $member->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</a>
                                    @if($member->is_member)
                                        <form action="{{ route('admin.members.deactivate', $member->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-900">Nonaktifkan</button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.members.activate', $member->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900">Aktifkan Member</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-3 py-8 text-center text-sm text-gray-500">
                                    Tidak ada data customer
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $members->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
