@extends('layouts.admin')

@section('title', 'Doctors Management')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Doctors Management</h1>
            <p class="mt-2 text-sm text-gray-700">Kelola data dokter dan jadwal praktik</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <a href="{{ route('admin.doctors.create') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                + Tambah Dokter
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
                                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Nama Dokter</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Spesialisasi</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">No. Telepon</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Total Booking</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                <th class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($doctors as $doctor)
                            <tr>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                    {{ $doctor->name }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $doctor->specialization ?? '-' }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $doctor->phone ?? '-' }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $doctor->bookings_count ?? 0 }} booking</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    @if($doctor->is_active)
                                        <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">Aktif</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-red-100 px-2 text-xs font-semibold leading-5 text-red-800">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <a href="{{ route('admin.doctors.schedules', $doctor->id) }}" class="text-green-600 hover:text-green-900 mr-3">Jadwal</a>
                                    <a href="{{ route('admin.doctors.edit', $doctor->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <form action="{{ route('admin.doctors.toggle-status', $doctor->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                            {{ $doctor->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.doctors.destroy', $doctor->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus dokter ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-3 py-8 text-center text-sm text-gray-500">
                                    Belum ada dokter. <a href="{{ route('admin.doctors.create') }}" class="text-indigo-600">Tambah dokter pertama</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $doctors->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
