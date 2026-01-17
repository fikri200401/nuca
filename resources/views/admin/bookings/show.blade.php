@extends('layouts.admin')

@section('title', 'Detail Booking - ' . $booking->booking_number)

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('admin.bookings.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
            ← Kembali ke Daftar Booking
        </a>
    </div>

    <!-- Header -->
    <div class="mb-6">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    {{ $booking->booking_number }}
                </h2>
                <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        @if($booking->status === 'pending')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                        @elseif($booking->status === 'waiting_dp')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                Menunggu DP
                            </span>
                        @elseif($booking->status === 'deposit_confirmed')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                DP Dikonfirmasi
                            </span>
                        @elseif($booking->status === 'confirmed')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Confirmed
                            </span>
                        @elseif($booking->status === 'completed')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Selesai
                            </span>
                        @elseif($booking->status === 'cancelled')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Dibatalkan
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ ucfirst($booking->status) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap gap-2 md:mt-0 md:ml-4">
<<<<<<< HEAD
                @if($booking->status === 'confirmed')
=======
                @if(in_array($booking->status, ['confirmed', 'deposit_confirmed', 'auto_approved']))
>>>>>>> 37f6b61 (upload project)
                    <form action="{{ route('admin.bookings.complete', $booking) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('Tandai booking ini sebagai selesai?')"
                                class="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                            ✓ Selesai
                        </button>
                    </form>
<<<<<<< HEAD
                @endif
                @if(in_array($booking->status, ['pending', 'waiting_dp', 'deposit_confirmed', 'confirmed']))
=======
                    <!-- Testing: Mark as No-Show -->
                    <button type="button" 
                            onclick="showNoShowModal()"
                            class="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700">
                        🚫 No-Show (Test)
                    </button>
                @endif
                @if(in_array($booking->status, ['pending', 'waiting_deposit', 'deposit_confirmed', 'confirmed', 'auto_approved']))
>>>>>>> 37f6b61 (upload project)
                    <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('Yakin batalkan booking ini?')"
                                class="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                            ✗ Batalkan
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Booking Information -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Informasi Booking
                    </h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900">
                                {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Waktu</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900">
                                {{ $booking->booking_time }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Treatment</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $booking->treatment->name }}
                                <span class="text-gray-500">({{ $booking->treatment->duration }} menit)</span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dokter</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $booking->doctor->name }}
                                @if($booking->doctor->specialization)
                                    <span class="text-gray-500">- {{ $booking->doctor->specialization }}</span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Harga</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">
                                Rp {{ number_format($booking->treatment->price, 0, ',', '.') }}
                            </dd>
                        </div>

                        @if($booking->is_manual_entry)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Sumber</dt>
                            <dd class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                    Manual Entry (WhatsApp)
                                </span>
                            </dd>
                        </div>
                        @endif

                        @if($booking->notes)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Catatan</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $booking->notes }}
                            </dd>
                        </div>
                        @endif

                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Dibuat pada</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $booking->created_at->format('d/m/Y H:i') }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Deposit Information -->
            @if($booking->deposit)
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Informasi Deposit
                    </h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status DP</dt>
                            <dd class="mt-1">
                                @if($booking->deposit->status === 'pending')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @elseif($booking->deposit->status === 'approved')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Approved
                                    </span>
                                @elseif($booking->deposit->status === 'rejected')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Rejected
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ ucfirst($booking->deposit->status) }}
                                    </span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Jumlah DP</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900">
                                Rp {{ number_format($booking->deposit->amount, 0, ',', '.') }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Deadline</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $booking->deposit->deadline_at->format('d/m/Y H:i') }}
                            </dd>
                        </div>

                        @if($booking->deposit->verified_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Diverifikasi</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $booking->deposit->verified_at->format('d/m/Y H:i') }}
                            </dd>
                        </div>
                        @endif

                        <div class="sm:col-span-2">
                            <a href="{{ route('admin.deposits.show', $booking->deposit) }}" 
                               class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                Lihat Detail Deposit →
                            </a>
                        </div>
                    </dl>
                </div>
            </div>
            @endif

            <!-- Feedback -->
            @if($booking->feedback)
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Feedback Customer
                    </h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Rating</dt>
                            <dd class="mt-1 flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $booking->feedback->rating)
                                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endif
                                @endfor
                                <span class="ml-2 text-sm text-gray-500">({{ $booking->feedback->rating }}/5)</span>
                            </dd>
                        </div>
                        @if($booking->feedback->comments)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Komentar</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $booking->feedback->comments }}
                            </dd>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Before-After Photos -->
            @if($booking->beforeAfterPhotos)
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Foto Before-After
                    </h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-2">Before</p>
                            <img src="{{ Storage::url($booking->beforeAfterPhotos->before_photo) }}" 
                                 alt="Before" 
                                 class="w-full rounded-lg border border-gray-200">
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-2">After</p>
                            <img src="{{ Storage::url($booking->beforeAfterPhotos->after_photo) }}" 
                                 alt="After" 
                                 class="w-full rounded-lg border border-gray-200">
                        </div>
                        @if($booking->beforeAfterPhotos->notes)
                        <div class="col-span-2">
                            <p class="text-sm text-gray-600">{{ $booking->beforeAfterPhotos->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Customer Info -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Customer
                    </h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nama</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900">
                                {{ $booking->user->name }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">WhatsApp</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="https://wa.me/{{ $booking->user->whatsapp_number }}" 
                                   target="_blank"
                                   class="text-indigo-600 hover:text-indigo-900">
                                    {{ $booking->user->whatsapp_number }}
                                </a>
                            </dd>
                        </div>

                        @if($booking->user->is_member)
                        <div>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                Member
                            </span>
                            @if($booking->user->member_number)
                            <p class="text-xs text-gray-500 mt-1">{{ $booking->user->member_number }}</p>
                            @endif
                        </div>
                        @endif

                        <div class="pt-4 border-t border-gray-200">
                            <a href="{{ route('admin.members.show', $booking->user) }}" 
                               class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                Lihat Profil Customer →
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Quick Actions
                    </h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-3">
                        @if($booking->status === 'completed' && !$booking->beforeAfterPhotos)
                        <button type="button"
                                onclick="document.getElementById('uploadPhotoModal').classList.remove('hidden')"
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                            📸 Upload Foto Before-After
                        </button>
                        @endif

                        @if(in_array($booking->status, ['pending', 'waiting_deposit', 'deposit_confirmed']))
                        <button type="button"
                                onclick="alert('Fitur reschedule akan segera ditambahkan')"
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            📅 Reschedule
                        </button>
                        @endif

                        <button type="button"
                                onclick="window.print()"
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            🖨️ Print
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Upload Photo Modal -->
<div id="uploadPhotoModal" class="hidden fixed z-10 inset-0 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.before-after.upload', $booking) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-purple-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Upload Foto Before-After
                            </h3>
                            <div class="mt-4 space-y-4">
                                <!-- Before Photo -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Foto Before</label>
                                    <input type="file" name="before_photo" accept="image/*" required
                                           class="mt-1 block w-full text-sm text-gray-500
                                                  file:mr-4 file:py-2 file:px-4
                                                  file:rounded-md file:border-0
                                                  file:text-sm file:font-semibold
                                                  file:bg-purple-50 file:text-purple-700
                                                  hover:file:bg-purple-100">
                                </div>

                                <!-- After Photo -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Foto After</label>
                                    <input type="file" name="after_photo" accept="image/*" required
                                           class="mt-1 block w-full text-sm text-gray-500
                                                  file:mr-4 file:py-2 file:px-4
                                                  file:rounded-md file:border-0
                                                  file:text-sm file:font-semibold
                                                  file:bg-purple-50 file:text-purple-700
                                                  hover:file:bg-purple-100">
                                </div>

                                <!-- Notes -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                                    <textarea name="notes" rows="3"
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                              placeholder="Contoh: Hasil facial treatment setelah 3x sesi..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Upload
                    </button>
                    <button type="button"
                            onclick="document.getElementById('uploadPhotoModal').classList.add('hidden')"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
<<<<<<< HEAD
</div>
@endsection
=======

    <!-- No-Show Modal -->
    <div id="noShowModal" class="fixed z-50 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="hideNoShowModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative z-50">
                <form action="{{ route('admin.bookings.no-show', $booking) }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Tandai sebagai No-Show (Testing)
                                </h3>
                                <div class="mt-4">
                                    <p class="text-sm text-gray-500 mb-4">
                                        Customer tidak datang pada jadwal yang ditentukan. <strong>DP akan dihanguskan.</strong>
                                    </p>
                                    <div>
                                        <label for="reason" class="block text-sm font-medium text-gray-700">Alasan (Opsional)</label>
                                        <textarea name="reason" id="reason" rows="3"
                                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500"
                                                  placeholder="Contoh: Customer tidak datang dan tidak memberikan konfirmasi..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Konfirmasi No-Show
                        </button>
                        <button type="button"
                                onclick="hideNoShowModal()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showNoShowModal() {
    console.log('showNoShowModal called');
    const modal = document.getElementById('noShowModal');
    if (modal) {
        modal.classList.remove('hidden');
        console.log('Modal shown');
    } else {
        console.error('Modal not found!');
    }
}

function hideNoShowModal() {
    console.log('hideNoShowModal called');
    const modal = document.getElementById('noShowModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}
</script>
@endpush
>>>>>>> 37f6b61 (upload project)
