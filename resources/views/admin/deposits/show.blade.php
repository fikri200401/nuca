@extends('layouts.admin')

@section('title', 'Detail Deposit - ' . $deposit->booking->booking_number)

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('admin.deposits.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
            ← Kembali ke Daftar Deposit
        </a>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-lg shadow-sm">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-lg shadow-sm">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-red-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="flex-1">
                @foreach($errors->all() as $error)
                <p class="text-sm font-medium text-red-800">{{ $error }}</p>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Header -->
    <div class="mb-6">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Detail Deposit
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Booking #{{ $deposit->booking->booking_number }}
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                @if(in_array($deposit->status, ['pending', 'submitted']))
                    <button type="button" 
                            onclick="showApproveModal()"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all hover:shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Approve Deposit
                    </button>
                    <button type="button" 
                            onclick="showRejectModal()"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all hover:shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Reject Deposit
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Deposit Information -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Informasi Deposit
                    </h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                @if($deposit->status === 'pending')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending - Menunggu Upload
                                    </span>
                                @elseif($deposit->status === 'submitted')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Submitted - Menunggu Verifikasi
                                    </span>
                                @elseif($deposit->status === 'approved')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Approved - Disetujui
                                    </span>
                                @elseif($deposit->status === 'rejected')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Rejected - Ditolak
                                    </span>
                                @elseif($deposit->status === 'expired')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Expired - Kedaluwarsa
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ ucfirst(str_replace('_', ' ', $deposit->status)) }}
                                    </span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Jumlah DP</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">
                                Rp {{ number_format($deposit->amount, 0, ',', '.') }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Deadline</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $deposit->deadline_at->format('d/m/Y H:i') }}
                                @if($deposit->isExpired())
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Expired
                                    </span>
                                @endif
                            </dd>
                        </div>

                        @if($deposit->proof_of_payment)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 mb-2">Bukti Transfer</dt>
                            <dd class="mt-1">
                                <a href="{{ asset('storage/' . $deposit->proof_of_payment) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $deposit->proof_of_payment) }}" 
                                         alt="Bukti Transfer" 
                                         class="max-w-full h-auto rounded-lg border-2 border-gray-300 shadow-lg hover:shadow-xl transition cursor-pointer hover:border-indigo-500">
                                </a>
                                <p class="text-xs text-gray-500 mt-2 text-center">Klik gambar untuk memperbesar</p>
                            </dd>
                        </div>
                        @endif

                        @if($deposit->verified_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Diverifikasi Pada</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $deposit->verified_at->format('d/m/Y H:i') }}
                            </dd>
                        </div>
                        @endif

                        @if($deposit->verifier)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Diverifikasi Oleh</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $deposit->verifier->name }}
                            </dd>
                        </div>
                        @endif

                        @if($deposit->status === 'rejected' && $deposit->rejection_reason)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 mb-2">Alasan Penolakan</dt>
                            <dd class="mt-1">
                                <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-red-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        <p class="text-sm text-red-800 font-medium">{{ $deposit->rejection_reason }}</p>
                                    </div>
                                </div>
                            </dd>
                        </div>
                        @endif

                        @if($deposit->notes)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Catatan Customer</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $deposit->notes }}
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

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
                            <dt class="text-sm font-medium text-gray-500">No. Booking</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900">
                                {{ $deposit->booking->booking_number }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal & Waktu</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($deposit->booking->booking_date)->format('d/m/Y') }} 
                                {{ $deposit->booking->booking_time }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Treatment</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $deposit->booking->treatment->name }}
                                <span class="text-gray-500">({{ $deposit->booking->treatment->duration }} menit)</span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dokter</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $deposit->booking->doctor->name }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Harga Treatment</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900">
                                Rp {{ number_format($deposit->booking->treatment->price, 0, ',', '.') }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status Booking</dt>
                            <dd class="mt-1">
                                @if($deposit->booking->status === 'pending')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @elseif($deposit->booking->status === 'waiting_dp')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                        Waiting DP
                                    </span>
                                @elseif($deposit->booking->status === 'deposit_confirmed')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        DP Confirmed
                                    </span>
                                @elseif($deposit->booking->status === 'confirmed')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Confirmed
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ ucfirst($deposit->booking->status) }}
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

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
                                {{ $deposit->booking->user->name }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">WhatsApp</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="https://wa.me/{{ $deposit->booking->user->whatsapp_number }}" 
                                   target="_blank"
                                   class="text-indigo-600 hover:text-indigo-900">
                                    {{ $deposit->booking->user->whatsapp_number }}
                                </a>
                            </dd>
                        </div>

                        @if($deposit->booking->user->is_member)
                        <div>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                Member
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Payment Proof Preview -->
            @if($deposit->payment_proof)
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Bukti Transfer
                    </h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <img src="{{ Storage::url($deposit->payment_proof) }}" 
                         alt="Bukti Transfer" 
                         class="w-full rounded-lg border border-gray-200">
                    <a href="{{ Storage::url($deposit->payment_proof) }}" 
                       target="_blank"
                       class="mt-3 block text-center text-sm text-indigo-600 hover:text-indigo-900">
                        Lihat Ukuran Penuh →
                    </a>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-gray-900">Reject Deposit</h3>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.deposits.reject', $deposit) }}">
            @csrf
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Alasan Penolakan *</label>
                <textarea name="rejection_reason" rows="4" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Jelaskan alasan penolakan deposit ini..."></textarea>
                <p class="text-xs text-gray-500 mt-1">Alasan akan dikirim ke customer via WhatsApp</p>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()" class="flex-1 px-5 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-5 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold rounded-lg hover:from-red-700 hover:to-red-800 transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    Reject Deposit
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Approve Confirmation Modal -->
<div id="approveModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
        <div class="p-6">
            <!-- Icon -->
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            
            <!-- Content -->
            <div class="text-center mb-6">
                <h3 class="text-xl font-bold text-gray-900 mb-2">Approve Deposit</h3>
                <p class="text-sm text-gray-600 mb-1">Apakah Anda yakin ingin menyetujui deposit ini?</p>
                <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-sm text-green-800 font-semibold">Nominal: Rp {{ number_format($deposit->amount, 0, ',', '.') }}</p>
                    <p class="text-xs text-green-700 mt-1">Booking: {{ $deposit->booking->booking_number }}</p>
                </div>
                <p class="text-xs text-gray-500 mt-3">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Customer akan mendapat notifikasi WhatsApp
                </p>
            </div>

            <!-- Actions -->
            <form action="{{ route('admin.deposits.approve', $deposit) }}" method="POST">
                @csrf
                <div class="flex gap-3">
                    <button type="button" 
                            onclick="closeApproveModal()" 
                            class="flex-1 px-5 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-all">
                        Batal
                    </button>
                    <button type="submit" 
                            class="flex-1 px-5 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold rounded-lg hover:from-green-700 hover:to-green-800 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Ya, Approve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showApproveModal() {
    document.getElementById('approveModal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
}

function showRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

// Close modals when clicking outside
document.getElementById('approveModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeApproveModal();
    }
});

document.getElementById('rejectModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeApproveModal();
        closeRejectModal();
    }
});
</script>
@endsection

