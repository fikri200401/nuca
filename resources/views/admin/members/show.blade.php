@extends('layouts.admin')

@section('title', 'Detail Member - ' . $member->name)

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('admin.members.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
            ← Kembali ke Daftar Member
        </a>
    </div>

    <!-- Header -->
    <div class="mb-6">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    {{ $member->name }}
                </h2>
                <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                    @if($member->is_member)
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                            Member - {{ $member->member_number }}
                        </span>
                    </div>
                    @endif
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                        </svg>
                        {{ $member->whatsapp_number }}
                    </div>
                </div>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                @if(!$member->is_member)
                <form action="{{ route('admin.members.activate', $member) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            onclick="return confirm('Aktifkan member untuk user ini?')"
                            class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700">
                        Aktifkan Member
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Total Booking</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['total_bookings'] }}</dd>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Selesai</dt>
                <dd class="mt-1 text-3xl font-semibold text-green-600">{{ $stats['completed_bookings'] }}</dd>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Dibatalkan</dt>
                <dd class="mt-1 text-3xl font-semibold text-red-600">{{ $stats['cancelled_bookings'] }}</dd>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">No Show</dt>
                <dd class="mt-1 text-3xl font-semibold text-orange-600">{{ $stats['no_show_count'] }}</dd>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Member Information -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Informasi Member
                    </h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $member->name }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">WhatsApp</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="https://wa.me/{{ $member->whatsapp_number }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $member->whatsapp_number }}
                                </a>
                            </dd>
                        </div>

                        @if($member->member_number)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nomor Member</dt>
                            <dd class="mt-1 text-sm font-semibold text-purple-600">{{ $member->member_number }}</dd>
                        </div>
                        @endif

                        @if($member->birth_date)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal Lahir</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($member->birth_date)->format('d/m/Y') }}</dd>
                        </div>
                        @endif

                        @if($member->gender)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Jenis Kelamin</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $member->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</dd>
                        </div>
                        @endif

                        @if($member->is_member)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Diskon Member</dt>
                            <dd class="mt-1 text-sm font-semibold text-green-600">{{ $member->member_discount }}%</dd>
                        </div>
                        @endif

                        @if($member->address)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $member->address }}</dd>
                        </div>
                        @endif

                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Bergabung Sejak</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $member->created_at->format('d/m/Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Riwayat Booking Terbaru
                    </h3>
                </div>
                <ul role="list" class="divide-y divide-gray-200">
                    @forelse($member->bookings()->latest()->take(5)->get() as $booking)
                    <li class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-indigo-600">{{ $booking->booking_number }}</p>
                                <p class="text-sm text-gray-500">{{ $booking->treatment->name }}</p>
                                <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }} {{ $booking->booking_time }}</p>
                            </div>
                            <div>
                                @if($booking->status === 'completed')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Selesai
                                    </span>
                                @elseif($booking->status === 'cancelled')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Batal
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="px-6 py-12 text-center text-sm text-gray-500">
                        Belum ada booking
                    </li>
                    @endforelse
                </ul>
            </div>

        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- No Show Notes -->
            @if($member->noShowNotes()->count() > 0)
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Catatan No Show
                    </h3>
                </div>
                <ul role="list" class="divide-y divide-gray-200">
                    @foreach($member->noShowNotes as $note)
                    <li class="px-6 py-4">
                        <p class="text-xs text-gray-500">{{ $note->created_at->format('d/m/Y') }}</p>
                        <p class="text-sm text-gray-900 mt-1">{{ $note->notes }}</p>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Feedbacks -->
            @if($member->feedbacks()->count() > 0)
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Feedback Terbaru
                    </h3>
                </div>
                <ul role="list" class="divide-y divide-gray-200">
                    @foreach($member->feedbacks()->latest()->take(3)->get() as $feedback)
                    <li class="px-6 py-4">
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $feedback->rating)
                                    <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @else
                                    <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endif
                            @endfor
                        </div>
                        <p class="text-sm text-gray-900 mt-2">{{ $feedback->comments }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $feedback->created_at->format('d/m/Y') }}</p>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
