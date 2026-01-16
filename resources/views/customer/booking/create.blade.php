@extends('layouts.app')

@section('content')
<div class="py-8">
    <!-- Main Content -->
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Title -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent mb-2">Buat Booking Baru</h1>
            <p class="text-gray-600">Pilih treatment dan jadwal yang Anda inginkan</p>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <h3 class="font-bold text-blue-900 mb-1">Kebijakan Booking</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
<<<<<<< HEAD
=======
                        <li>• Klinik:<strong> Open-close</strong> 08:00 pm-08:00 am</li>
>>>>>>> 37f6b61 (upload project)
                        <li>• Booking <strong>kurang dari 7 hari</strong>: Langsung dikonfirmasi (Auto Approved)</li>
                        <li>• Booking <strong>7 hari atau lebih</strong>: Perlu bayar deposit Rp 50.000 dalam 24 jam</li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="bookingApp" class="bg-white rounded-2xl shadow-xl p-8 border border-pink-100">
            <!-- Alert Messages -->
            <div v-if="errorMessage" class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center gap-2" role="alert">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span v-text="errorMessage"></span>
            </div>
            <div v-if="successMessage" class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2" role="alert">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span v-text="successMessage"></span>
            </div>

            <form @submit.prevent="submitBooking" class="space-y-6">
                <!-- Treatment Selection -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Treatment *</label>
                    <select v-model="formData.treatment_id" @change="onTreatmentChange" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition" required>
                        <option value="">-- Pilih Treatment --</option>
                        @foreach($treatments as $treatment)
                        <option value="{{ $treatment->id }}" data-duration="{{ $treatment->duration_minutes }}" data-price="{{ $treatment->price }}">
                            {{ $treatment->name }} - Rp {{ number_format($treatment->price, 0, ',', '.') }} ({{ $treatment->duration_minutes }} menit)
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Selection -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Booking *</label>
                    <input type="date" v-model="formData.booking_date" @change="onDateChange" :min="minDate" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition" required>
                </div>

                <!-- Time Slot Selection -->
                <div v-if="availableSlots.length > 0">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Waktu *</label>
                    <div class="grid grid-cols-3 gap-3">
                        <button type="button" v-for="slot in availableSlots" :key="slot" @click="selectTimeSlot(slot)" class="px-4 py-3 border-2 rounded-xl text-sm font-semibold transition shadow-sm" :class="formData.booking_time === slot ? 'bg-gradient-to-r from-pink-500 to-purple-600 text-white border-pink-500 shadow-lg' : 'bg-white text-gray-700 border-gray-300 hover:border-pink-400 hover:shadow-md'">
                            @{{ slot }}
                        </button>
                    </div>
                </div>
                <div v-else-if="formData.booking_date && formData.treatment_id">
                    <p class="text-sm text-gray-500">Memuat slot waktu tersedia...</p>
                </div>

                <!-- Doctor Selection -->
                <div v-if="availableDoctors.length > 0">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Dokter *</label>
                    <div class="space-y-3">
                        <label v-for="doctor in availableDoctors" :key="doctor.id" class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition shadow-sm" :class="formData.doctor_id === doctor.id ? 'border-pink-500 bg-pink-50 shadow-md' : 'border-gray-300 hover:border-pink-400 hover:shadow-md'">
                            <input type="radio" :value="doctor.id" v-model="formData.doctor_id" class="text-pink-600 focus:ring-pink-500">
                            <div class="ml-3">
                                <p class="font-bold text-gray-900">@{{ doctor.name }}</p>
                                <p class="text-sm text-gray-600">@{{ doctor.specialization }}</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Voucher Code -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kode Voucher (Opsional)</label>
                    <div class="flex gap-2">
                        <input type="text" v-model="formData.voucher_code" @input="voucherMessage = ''; voucherDiscount = 0;" placeholder="MASUKKAN KODE VOUCHER" class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 uppercase font-medium transition">
                        <button type="button" @click="checkVoucher" :disabled="!formData.voucher_code || !formData.treatment_id || loading" class="px-8 py-3 bg-gradient-to-r from-purple-500 to-pink-600 text-white font-bold rounded-xl hover:from-purple-600 hover:to-pink-700 disabled:opacity-50 disabled:cursor-not-allowed transition shadow-lg">
                            Cek
                        </button>
                    </div>
                    <p v-if="voucherMessage" :class="voucherValid ? 'text-green-600' : 'text-red-600'" class="mt-2 text-sm font-medium">
                        @{{ voucherMessage }}
                    </p>
                    <div v-if="voucherValid && voucherData" class="mt-3 p-4 bg-green-50 border border-green-200 rounded-xl">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-green-900">@{{ voucherData.name }}</p>
                                <p class="text-xs text-green-700">Diskon: @{{ voucherData.formatted_value }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                    <textarea v-model="formData.notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="Tambahkan catatan atau keluhan khusus..."></textarea>
                </div>

                <!-- Price Summary -->
                <div v-if="formData.treatment_id" class="bg-gray-50 rounded-lg p-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Harga Treatment</span>
                        <span class="font-medium">Rp @{{ treatmentPrice.toLocaleString('id-ID') }}</span>
                    </div>
                    @if(Auth::user()->is_member)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Diskon Member ({{ Auth::user()->member_discount }}%)</span>
                        <span class="font-medium text-green-600">- Rp @{{ memberDiscount.toLocaleString('id-ID') }}</span>
                    </div>
                    @endif
                    <div v-if="voucherDiscount > 0" class="flex justify-between text-sm">
                        <span class="text-gray-600">Diskon Voucher</span>
                        <span class="font-medium text-green-600">- Rp @{{ voucherDiscount.toLocaleString('id-ID') }}</span>
                    </div>
                    <div class="border-t pt-2 flex justify-between">
                        <span class="font-semibold text-gray-900">Total Bayar</span>
                        <span class="font-bold bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent text-lg">Rp @{{ finalPrice.toLocaleString('id-ID') }}</span>
                    </div>
                    <div v-if="needsDeposit" class="text-sm text-amber-600 bg-amber-50 p-3 rounded-xl border border-amber-200">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <strong>Perlu DP:</strong> Booking lebih dari 7 hari memerlukan deposit Rp 50.000 yang harus dibayar dalam 24 jam.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" :disabled="!canSubmit || loading" class="w-full py-3 px-4 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:from-pink-600 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                    <span v-if="loading" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memproses...
                    </span>
                    <span v-else class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Buat Booking
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.js"></script>
<script>
console.log('Script loaded!');
console.log('Vue:', typeof Vue);

const { createApp } = Vue;

const app = createApp({
    data() {
        console.log('Vue app created!');
        return {
            formData: {
                treatment_id: '',
                booking_date: '',
                booking_time: '',
                doctor_id: '',
                voucher_code: '',
                notes: ''
            },
            availableSlots: [],
            availableDoctors: [],
            treatmentPrice: 0,
            treatmentDuration: 0,
            voucherDiscount: 0,
            voucherValid: false,
            voucherMessage: '',
            voucherData: null,
            loading: false,
            errorMessage: '',
            successMessage: ''
        }
    },
    computed: {
        minDate() {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            return tomorrow.toISOString().split('T')[0];
        },
        memberDiscount() {
            @if(Auth::user()->is_member)
                return this.treatmentPrice * {{ Auth::user()->member_discount }} / 100;
            @else
                return 0;
            @endif
        },
        finalPrice() {
            return Math.max(0, this.treatmentPrice - this.memberDiscount - this.voucherDiscount);
        },
        needsDeposit() {
            if (!this.formData.booking_date) return false;
            const bookingDate = new Date(this.formData.booking_date);
            const today = new Date();
            const diffDays = Math.ceil((bookingDate - today) / (1000 * 60 * 60 * 24));
            return diffDays >= 7;
        },
        canSubmit() {
            return !!(this.formData.treatment_id && 
                   this.formData.booking_date && 
                   this.formData.booking_time && 
                   this.formData.doctor_id);
        }
    },
    methods: {
        onTreatmentChange(e) {
            console.log('Treatment changed!', e.target.value);
            const option = e.target.options[e.target.selectedIndex];
            this.treatmentPrice = parseFloat(option.dataset.price);
            this.treatmentDuration = parseInt(option.dataset.duration);
            this.availableSlots = [];
            this.availableDoctors = [];
            this.formData.booking_time = '';
            this.formData.doctor_id = '';
            console.log('Treatment details:', {
                price: this.treatmentPrice,
                duration: this.treatmentDuration
            });
            if (this.formData.booking_date) {
                console.log('Date already selected, loading slots...');
                this.loadAvailableSlots();
            }
        },
        onDateChange() {
            console.log('Date changed!', this.formData.booking_date);
            this.loadAvailableSlots();
        },
        async loadAvailableSlots() {
            if (!this.formData.treatment_id || !this.formData.booking_date) return;
            
            this.loading = true;
            this.errorMessage = '';
            console.log('Loading slots for:', {
                treatment_id: this.formData.treatment_id,
                date: this.formData.booking_date
            });
            
            try {
                const response = await fetch('{{ route("customer.bookings.available-slots") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        treatment_id: this.formData.treatment_id,
                        date: this.formData.booking_date
                    })
                });
                const data = await response.json();
                console.log('Slots response:', data);
                
                if (data.success) {
                    this.availableSlots = data.slots;
                    console.log('Available slots:', this.availableSlots);
                    if (this.availableSlots.length === 0) {
                        this.errorMessage = 'Tidak ada slot waktu tersedia untuk tanggal ini';
                    }
                } else {
                    this.errorMessage = data.message || 'Gagal memuat slot waktu';
                }
            } catch (error) {
                console.error('Error loading slots:', error);
                this.errorMessage = 'Gagal memuat slot waktu: ' + error.message;
            } finally {
                this.loading = false;
            }
        },
        async selectTimeSlot(slot) {
            this.formData.booking_time = slot;
            await this.loadAvailableDoctors();
        },
        async loadAvailableDoctors() {
            if (!this.formData.booking_date || !this.formData.booking_time) return;
            
            this.loading = true;
            try {
                const response = await fetch('{{ route("customer.bookings.available-doctors") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        treatment_id: this.formData.treatment_id,
                        date: this.formData.booking_date,
                        time: this.formData.booking_time
                    })
                });
                const data = await response.json();
                if (data.success) {
                    this.availableDoctors = data.doctors;
                }
            } catch (error) {
                this.errorMessage = 'Gagal memuat dokter tersedia';
            } finally {
                this.loading = false;
            }
        },
        async checkVoucher() {
            if (!this.formData.voucher_code || !this.formData.treatment_id) {
                this.voucherMessage = 'Pilih treatment terlebih dahulu';
                this.voucherValid = false;
                return;
            }

            this.loading = true;
            this.voucherMessage = '';
            this.voucherValid = false;
            this.voucherData = null;
            
            try {
                const response = await fetch('{{ route("customer.bookings.check-voucher") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        voucher_code: this.formData.voucher_code.toUpperCase(),
                        treatment_id: this.formData.treatment_id
                    })
                });
                
                const data = await response.json();
                
                if (data.valid) {
                    this.voucherValid = true;
                    this.voucherMessage = data.message;
                    this.voucherData = data.voucher;
                    this.voucherDiscount = data.price_breakdown.voucher_discount;
                } else {
                    this.voucherValid = false;
                    this.voucherMessage = data.message;
                    this.voucherDiscount = 0;
                    this.voucherData = null;
                }
            } catch (error) {
                console.error('Voucher check error:', error);
                this.voucherMessage = 'Gagal memeriksa voucher';
                this.voucherValid = false;
                this.voucherDiscount = 0;
            } finally {
                this.loading = false;
            }
        },
        async submitBooking() {
            if (!this.canSubmit || this.loading) return;
            
            this.loading = true;
            this.errorMessage = '';
            try {
                const response = await fetch('{{ route("customer.bookings.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.formData)
                });
                const data = await response.json();
                if (data.success) {
                    this.successMessage = 'Booking berhasil dibuat! Redirecting...';
                    setTimeout(() => {
                        window.location.href = '{{ route("customer.dashboard") }}';
                    }, 1000);
                } else {
                    this.errorMessage = data.message || 'Gagal membuat booking';
                }
            } catch (error) {
                this.errorMessage = 'Terjadi kesalahan: ' + error.message;
                console.error('Submit error:', error);
            } finally {
                this.loading = false;
            }
        }
    }
});

console.log('Attempting to mount Vue app...');
app.mount('#bookingApp');
console.log('Vue app mounted!');
</script>
@endsection
