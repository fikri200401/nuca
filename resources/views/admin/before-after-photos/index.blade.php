@extends('layouts.admin')

@section('title', 'Before-After Photos')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Before-After Photos</h1>
            <p class="mt-2 text-sm text-gray-700">Dokumentasi hasil treatment (upload dari detail booking)</p>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($photos as $photo)
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $photo->booking->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $photo->booking->booking_code }}</p>
                    </div>
                    <span class="inline-flex rounded-full bg-purple-100 px-2 text-xs font-semibold leading-5 text-purple-800">
                        {{ $photo->booking->treatment->name }}
                    </span>
                </div>

                <!-- Photos -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- Before Photo -->
                    <div>
                        <p class="text-xs font-medium text-gray-700 mb-2">Before</p>
                        @if($photo->before_photo)
                            <img src="{{ Storage::url($photo->before_photo) }}" alt="Before" class="w-full h-32 object-cover rounded-lg border border-gray-200">
                        @else
                            <div class="w-full h-32 bg-gray-100 rounded-lg flex items-center justify-center">
                                <span class="text-xs text-gray-400">No photo</span>
                            </div>
                        @endif
                    </div>

                    <!-- After Photo -->
                    <div>
                        <p class="text-xs font-medium text-gray-700 mb-2">After</p>
                        @if($photo->after_photo)
                            <img src="{{ Storage::url($photo->after_photo) }}" alt="After" class="w-full h-32 object-cover rounded-lg border border-gray-200">
                        @else
                            <div class="w-full h-32 bg-gray-100 rounded-lg flex items-center justify-center">
                                <span class="text-xs text-gray-400">No photo</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Notes -->
                @if($photo->notes)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-xs text-gray-500">{{ $photo->notes }}</p>
                </div>
                @endif

                <!-- Info -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-xs text-gray-500">
                        <span class="font-medium">Uploaded:</span> {{ $photo->created_at->format('d/m/Y H:i') }}<br>
                        <span class="font-medium">Booking:</span> {{ $photo->booking->booking_date->format('d/m/Y') }} - {{ $photo->booking->booking_time }}
                    </p>
                </div>

                <!-- Actions -->
                <div class="mt-4 flex justify-end space-x-3">
                    <a href="{{ route('admin.bookings.show', $photo->booking_id) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                        Detail Booking
                    </a>
                    <form action="{{ route('admin.before-after.destroy', $photo->booking_id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus foto ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 hover:text-red-900">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 rounded-lg bg-white shadow p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="mt-4 text-gray-500">Belum ada foto before-after</p>
            <p class="mt-2 text-sm text-gray-400">Upload foto dari detail booking setelah treatment selesai</p>
        </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $photos->links() }}
    </div>
</div>
@endsection
