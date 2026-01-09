@extends('layouts.admin')

@section('title', 'Feedbacks Management')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Feedbacks Management</h1>
            <p class="mt-2 text-sm text-gray-700">Kelola rating & review dari customer</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="mt-6 bg-white shadow sm:rounded-lg p-4">
        <form method="GET" action="{{ route('admin.feedbacks.index') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <label for="rating" class="block text-sm font-medium text-gray-700">Rating</label>
                <select name="rating" id="rating" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Semua Rating</option>
                    <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ (5)</option>
                    <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ (4)</option>
                    <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>⭐⭐⭐ (3)</option>
                    <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>⭐⭐ (2)</option>
                    <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>⭐ (1)</option>
                </select>
            </div>

            <div>
                <label for="treatment_id" class="block text-sm font-medium text-gray-700">Treatment</label>
                <select name="treatment_id" id="treatment_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Semua Treatment</option>
                    <!-- Will be populated from controller -->
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($feedbacks as $feedback)
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-lg font-medium text-gray-600">{{ substr($feedback->user->name, 0, 1) }}</span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ $feedback->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $feedback->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div>
                        @if($feedback->is_visible)
                            <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">Visible</span>
                        @else
                            <span class="inline-flex rounded-full bg-gray-100 px-2 text-xs font-semibold leading-5 text-gray-800">Hidden</span>
                        @endif
                    </div>
                </div>

                <div class="mt-4">
                    <div class="flex items-center">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $feedback->rating)
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @else
                                <svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endif
                        @endfor
                    </div>
                </div>

                <div class="mt-3">
                    <p class="text-sm text-gray-700">{{ $feedback->comment }}</p>
                </div>

                <div class="mt-4 border-t border-gray-200 pt-4">
                    <p class="text-xs text-gray-500">
                        <span class="font-medium">Treatment:</span> {{ $feedback->treatment->name }}<br>
                        <span class="font-medium">Dokter:</span> {{ $feedback->doctor->name }}<br>
                        <span class="font-medium">Booking:</span> {{ $feedback->booking->booking_code }}
                    </p>
                </div>

                <div class="mt-4 flex justify-end space-x-3">
                    <form action="{{ route('admin.feedbacks.toggle-visibility', $feedback->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-yellow-600 hover:text-yellow-900">
                            {{ $feedback->is_visible ? 'Hide' : 'Show' }}
                        </button>
                    </form>
                    <form action="{{ route('admin.feedbacks.destroy', $feedback->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus feedback ini?')">
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
            <p class="text-gray-500">Belum ada feedback dari customer</p>
        </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $feedbacks->links() }}
    </div>
</div>
@endsection
