@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="container mx-auto px-4 max-w-6xl">
        {{-- Header --}}
        <div class="mb-8 bg-white rounded-2xl shadow-lg border border-pink-100 p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent mb-1">Riwayat Booking</h1>
                    <p class="text-gray-600">Lihat semua riwayat booking treatment Anda</p>
                </div>
            </div>
        </div>

        {{-- Bookings List --}}
        @forelse($bookings as $booking)
        <div class="bg-white rounded-2xl shadow-lg border border-pink-100 mb-4 overflow-hidden hover:shadow-xl hover:border-pink-200 transition-all duration-200">
            <div class="p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    {{-- Booking Info --}}
                    <div class="flex-1">
                        <div class="flex items-start gap-4">
                            {{-- Icon --}}
                            <div class="flex-shrink-0">
                                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-pink-500 to-purple-600 flex items-center justify-center shadow-lg">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                            </div>

                            {{-- Details --}}
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-xl font-bold text-gray-900">{{ $booking->treatment->name }}</h3>
                                    @if($booking->status === 'auto_approved')
                                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Auto Approved</span>
                                    @elseif($booking->status === 'waiting_deposit')
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">Menunggu Deposit</span>
                                    @elseif($booking->status === 'deposit_confirmed')
                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">Deposit Terkonfirmasi</span>
                                    @elseif($booking->status === 'deposit_rejected')
                                        <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">Deposit Ditolak</span>
                                    @elseif($booking->status === 'completed')
                                        <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full">Selesai</span>
                                    @elseif($booking->status === 'cancelled')
                                        <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full">Dibatalkan</span>
                                    @elseif($booking->status === 'expired')
                                        <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs font-semibold rounded-full">Kadaluarsa</span>
                                    @endif
                                </div>

                                <div class="space-y-2 text-sm text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="font-medium">{{ $booking->booking_date->format('d F Y') }}</span>
                                        <span class="text-gray-400">•</span>
                                        <span>{{ $booking->booking_time }} - {{ $booking->end_time }} WIB</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>{{ $booking->doctor->name }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                        <span class="font-mono text-xs bg-pink-50 border border-pink-200 px-2 py-1 rounded-lg font-semibold text-pink-700">{{ $booking->booking_code }}</span>
                                    </div>
                                </div>

                                @if($booking->customer_notes)
                                <div class="mt-3 pt-3 border-t border-pink-100">
                                    <p class="text-sm text-gray-600"><span class="font-semibold text-gray-700">Catatan:</span> {{ $booking->customer_notes }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Price & Actions --}}
                    <div class="flex flex-col items-end gap-3 md:ml-4">
                        <div class="text-right">
                            @if($booking->discount_amount > 0)
                                <div class="text-sm text-gray-400 line-through">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</div>
                            @endif
                            <div class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent">
                                Rp {{ number_format($booking->final_price, 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('customer.bookings.show', $booking->id) }}" class="px-5 py-2.5 bg-gradient-to-r from-pink-500 to-purple-600 text-white text-sm font-semibold rounded-xl shadow-lg hover:shadow-xl hover:from-pink-600 hover:to-purple-700 transition-all duration-200 transform hover:-translate-y-0.5">
                                Lihat Detail
                            </a>
                            
                            {{-- Upload Deposit Button (only if waiting_deposit) --}}
                            @if($booking->status === 'waiting_deposit')
                            <button onclick="showUploadModal({{ $booking->id }})" class="px-5 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-sm font-semibold rounded-xl shadow-lg hover:shadow-xl hover:from-green-600 hover:to-emerald-700 transition-all duration-200 transform hover:-translate-y-0.5">
                                Upload Deposit
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        {{-- Empty State --}}
        <div class="bg-white rounded-2xl shadow-lg border border-pink-100 p-12 text-center">
            <div class="w-24 h-24 bg-gradient-to-br from-pink-100 to-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                <svg class="w-12 h-12 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Belum Ada Booking</h3>
            <p class="text-gray-600 mb-6">Anda belum memiliki riwayat booking treatment apapun.</p>
            <a href="{{ route('customer.bookings.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:from-pink-600 hover:to-purple-700 transition-all duration-200 transform hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Booking Baru
            </a>
        </div>
        @endforelse

        {{-- Pagination --}}
        @if($bookings->hasPages())
        <div class="mt-8">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Upload Deposit Modal --}}
<div id="uploadDepositModal" class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all border border-pink-100">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent">Upload Bukti Deposit</h3>
            <button onclick="closeUploadModal()" class="text-gray-400 hover:text-pink-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="uploadDepositForm" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Bukti Transfer</label>
                <div class="relative border-2 border-dashed border-pink-300 rounded-xl p-6 text-center hover:border-pink-400 transition bg-pink-50">
                    <input type="file" name="deposit_proof" accept="image/*" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="previewImage(this)">
                    <div id="uploadPlaceholder">
                        <svg class="w-12 h-12 text-pink-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-sm text-pink-700 font-medium">Klik untuk upload gambar</p>
                        <p class="text-xs text-pink-500 mt-1">JPG, PNG (Max: 2MB)</p>
                    </div>
                    <div id="imagePreview" class="hidden">
                        <img src="" alt="Preview" class="max-h-48 mx-auto rounded-lg shadow-lg">
                        <p class="text-xs text-gray-500 mt-2">Klik lagi untuk mengganti</p>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan (Opsional)</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-3 border border-pink-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition" placeholder="Contoh: Transfer dari BCA a.n. John Doe"></textarea>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeUploadModal()" class="flex-1 px-5 py-3 border-2 border-pink-300 text-gray-700 font-semibold rounded-xl hover:bg-pink-50 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-5 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:from-pink-600 hover:to-purple-700 transition-all duration-200 transform hover:-translate-y-0.5">
                    Upload
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let currentBookingId = null;

function showUploadModal(bookingId) {
    currentBookingId = bookingId;
    document.getElementById('uploadDepositForm').action = `/customer/bookings/${bookingId}/upload-deposit`;
    document.getElementById('uploadDepositModal').classList.remove('hidden');
}

function closeUploadModal() {
    currentBookingId = null;
    document.getElementById('uploadDepositModal').classList.add('hidden');
    document.getElementById('uploadPlaceholder').classList.remove('hidden');
    document.getElementById('imagePreview').classList.add('hidden');
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').querySelector('img').src = e.target.result;
            document.getElementById('uploadPlaceholder').classList.add('hidden');
            document.getElementById('imagePreview').classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Close modal when clicking outside
document.getElementById('uploadDepositModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeUploadModal();
    }
});
</script>
@endpush
@endsection
