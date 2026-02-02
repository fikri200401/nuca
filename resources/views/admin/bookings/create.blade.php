@extends('layouts.admin')

@section('title', 'Booking Manual dari WhatsApp')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('admin.bookings.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
            ← Kembali ke Daftar Booking
        </a>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
    <div class="mb-4 rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Terjadi kesalahan:</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Booking Manual</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Input booking dari customer yang memesan via WhatsApp. 
                    Pastikan slot tersedia untuk menghindari double booking.
                </p>
            </div>
        </div>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="{{ route('admin.bookings.store') }}" method="POST">
                @csrf
                <div class="shadow sm:rounded-md sm:overflow-hidden">
                    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                        
                        <!-- Customer Selection -->
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700">
                                Customer <span class="text-red-500">*</span>
                            </label>
                            <select id="user_id" name="user_id" required 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('user_id') border-red-300 @enderror">
                                <option value="">Pilih Customer</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->whatsapp_number }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-sm text-gray-500">
                                Cari berdasarkan nama atau nomor WhatsApp. Jika belum terdaftar, daftarkan customer terlebih dahulu.
                            </p>
                            @error('user_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Treatment Selection -->
                        <div>
                            <label for="treatment_id" class="block text-sm font-medium text-gray-700">
                                Treatment <span class="text-red-500">*</span>
                            </label>
                            <select id="treatment_id" name="treatment_id" required 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('treatment_id') border-red-300 @enderror">
                                <option value="">Pilih Treatment</option>
                                @foreach($treatments as $treatment)
                                    <option value="{{ $treatment->id }}" 
                                            data-duration="{{ $treatment->duration }}" 
                                            data-price="{{ $treatment->price }}"
                                            {{ old('treatment_id') == $treatment->id ? 'selected' : '' }}>
                                        {{ $treatment->name }} ({{ $treatment->duration }} menit - Rp {{ number_format($treatment->price, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('treatment_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Booking Date -->
                        <div>
                            <label for="booking_date" class="block text-sm font-medium text-gray-700">
                                Tanggal Booking <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="booking_date" name="booking_date" required 
                                   min="{{ date('Y-m-d') }}"
                                   value="{{ old('booking_date') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('booking_date') border-red-300 @enderror">
                            @error('booking_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Booking Time -->
                        <div>
                            <label for="booking_time" class="block text-sm font-medium text-gray-700">
                                Jam Booking <span class="text-red-500">*</span>
                            </label>
                            <select id="booking_time" name="booking_time" required 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('booking_time') border-red-300 @enderror">
                                <option value="">Pilih jam terlebih dahulu tanggal dan treatment</option>
                            </select>
                            <p class="mt-2 text-sm text-gray-500" id="timeSlotInfo">
                                Pilih tanggal dan treatment terlebih dahulu untuk melihat jam yang tersedia
                            </p>
                            @error('booking_time')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Doctor Selection -->
                        <div>
                            <label for="doctor_id" class="block text-sm font-medium text-gray-700">
                                Dokter <span class="text-red-500">*</span>
                            </label>
                            <select id="doctor_id" name="doctor_id" required 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('doctor_id') border-red-300 @enderror">
                                <option value="">Pilih Dokter</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                        {{ $doctor->name }}{{ $doctor->specialization ? ' - ' . $doctor->specialization : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-sm text-gray-500">
                                Sistem akan otomatis cek ketersediaan dokter pada tanggal & jam yang dipilih
                            </p>
                            @error('doctor_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">
                                Catatan Admin (Opsional)
                            </label>
                            <textarea id="notes" name="notes" rows="3" 
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('notes') border-red-300 @enderror"
                                      placeholder="Contoh: Customer memesan via WhatsApp pukul 14:00">{{ old('notes') }}</textarea>
                            <p class="mt-2 text-sm text-gray-500">
                                Catatan ini hanya terlihat oleh admin, tidak terlihat oleh customer
                            </p>
                            @error('notes')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Info Box -->
                        <div class="rounded-md bg-blue-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h3 class="text-sm font-medium text-blue-800">Informasi Penting</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <ul class="list-disc pl-5 space-y-1">
                                            <li>Booking hari yang sama akan otomatis di-approve</li>
                                            <li>Booking minggu depan akan memerlukan DP minimal Rp 50.000</li>
                                            <li>Sistem akan otomatis cek ketersediaan slot</li>
                                            <li>Customer akan menerima notifikasi WhatsApp setelah booking dibuat</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <a href="{{ route('admin.bookings.index') }}" 
                           class="inline-flex justify-center py-2 px-4 mr-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Batal
                        </a>
                        <button type="submit" id="submitBtn"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <span id="btnText">Buat Booking</span>
                            <svg id="btnLoading" class="hidden animate-spin ml-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnLoading = document.getElementById('btnLoading');
    const bookingDateInput = document.getElementById('booking_date');
    const bookingTimeSelect = document.getElementById('booking_time');
    const treatmentSelect = document.getElementById('treatment_id');
    const doctorSelect = document.getElementById('doctor_id');
    const timeSlotInfo = document.getElementById('timeSlotInfo');
    
    form.addEventListener('submit', function(e) {
        // Show loading state
        submitBtn.disabled = true;
        btnText.textContent = 'Memproses...';
        btnLoading.classList.remove('hidden');
    });

    // Load time slots when date or treatment changes
    function loadTimeSlots() {
        const date = bookingDateInput.value;
        const treatmentId = treatmentSelect.value;
        const doctorId = doctorSelect.value;

        if (!date || !treatmentId) {
            bookingTimeSelect.innerHTML = '<option value="">Pilih tanggal dan treatment terlebih dahulu</option>';
            bookingTimeSelect.disabled = true;
            return;
        }

        bookingTimeSelect.disabled = true;
        timeSlotInfo.textContent = 'Memuat jam yang tersedia...';
        
        fetch('{{ route("admin.bookings.available-slots") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                booking_date: date,
                treatment_id: treatmentId,
                doctor_id: doctorId
            })
        })
        .then(response => response.json())
        .then(data => {
            bookingTimeSelect.innerHTML = '';
            
            if (data.slots && data.slots.length > 0) {
                let hasAvailable = false;
                data.slots.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot.time;
                    option.textContent = slot.time + (slot.isPast ? ' (Sudah Lewat)' : (slot.available ? '' : ' (Penuh)'));
                    option.disabled = !slot.available || slot.isPast;
                    bookingTimeSelect.appendChild(option);
                    if (slot.available && !slot.isPast) hasAvailable = true;
                });
                
                if (hasAvailable) {
                    timeSlotInfo.textContent = 'Pilih jam yang tersedia';
                    timeSlotInfo.className = 'mt-2 text-sm text-gray-500';
                } else {
                    timeSlotInfo.textContent = 'Tidak ada slot tersedia untuk tanggal ini';
                    timeSlotInfo.className = 'mt-2 text-sm text-red-600';
                }
                bookingTimeSelect.disabled = false;
            } else {
                bookingTimeSelect.innerHTML = '<option value="">Tidak ada slot tersedia</option>';
                timeSlotInfo.textContent = 'Tidak ada slot tersedia untuk tanggal ini';
                timeSlotInfo.className = 'mt-2 text-sm text-red-600';
            }
        })
        .catch(error => {
            console.error('Error loading slots:', error);
            bookingTimeSelect.innerHTML = '<option value="">Gagal memuat slot</option>';
            timeSlotInfo.textContent = 'Gagal memuat slot, coba lagi';
            timeSlotInfo.className = 'mt-2 text-sm text-red-600';
        });
    }

    bookingDateInput.addEventListener('change', loadTimeSlots);
    treatmentSelect.addEventListener('change', loadTimeSlots);
    doctorSelect.addEventListener('change', loadTimeSlots);
});
</script>
@endsection
