@extends('layouts.base')

@section('title', 'Buat Booking Baru')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('customer.dashboard') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <h1 class="text-xl font-bold text-gray-900">Buat Booking Baru</h1>
                </div>
                <div class="flex items-center">
                    <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div id="bookingApp" class="bg-white rounded-lg shadow-lg p-6">
            <!-- Alert Messages -->
            <div v-if="errorMessage" class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded" role="alert">
                <span v-text="errorMessage"></span>
            </div>
            <div v-if="successMessage" class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded" role="alert">
                <span v-text="successMessage"></span>
            </div>

            <form @submit.prevent="submitBooking" class="space-y-6">
                <!-- Treatment Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Treatment *</label>
                    <select v-model="formData.treatment_id" @change="onTreatmentChange" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Booking *</label>
                    <input type="date" v-model="formData.booking_date" @change="onDateChange" :min="minDate" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>

                <!-- Time Slot Selection -->
                <div v-if="availableSlots.length > 0">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Waktu *</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" v-for="slot in availableSlots" :key="slot" @click="selectTimeSlot(slot)" class="px-4 py-2 border rounded-lg text-sm font-medium transition" :class="formData.booking_time === slot ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-300 hover:border-indigo-500'">
                            @{{ slot }}
                        </button>
                    </div>
                </div>
                <div v-else-if="formData.booking_date && formData.treatment_id">
                    <p class="text-sm text-gray-500">Memuat slot waktu tersedia...</p>
                </div>

                <!-- Doctor Selection -->
                <div v-if="availableDoctors.length > 0">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Dokter *</label>
                    <div class="space-y-2">
                        <label v-for="doctor in availableDoctors" :key="doctor.id" class="flex items-center p-4 border rounded-lg cursor-pointer transition" :class="formData.doctor_id === doctor.id ? 'border-indigo-600 bg-indigo-50' : 'border-gray-300 hover:border-indigo-500'">
                            <input type="radio" :value="doctor.id" v-model="formData.doctor_id" class="text-indigo-600 focus:ring-indigo-500">
                            <div class="ml-3">
                                <p class="font-medium text-gray-900">@{{ doctor.name }}</p>
                                <p class="text-sm text-gray-600">@{{ doctor.specialization }}</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Voucher Code -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode Voucher (Opsional)</label>
                    <div class="flex gap-2">
                        <input type="text" v-model="formData.voucher_code" placeholder="Masukkan kode voucher" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <button type="button" @click="checkVoucher" :disabled="!formData.voucher_code || loading" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 disabled:opacity-50">
                            Cek
                        </button>
                    </div>
                    <p v-if="voucherDiscount > 0" class="mt-2 text-sm text-green-600">Diskon voucher: Rp @{{ voucherDiscount.toLocaleString('id-ID') }}</p>
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
                        <span class="font-bold text-indigo-600 text-lg">Rp @{{ finalPrice.toLocaleString('id-ID') }}</span>
                    </div>
                    <div v-if="needsDeposit" class="text-sm text-amber-600 bg-amber-50 p-2 rounded">
                        <strong>Perlu DP:</strong> Booking lebih dari 7 hari memerlukan deposit Rp 50.000 yang harus dibayar dalam 24 jam.
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" :disabled="!canSubmit || loading" class="w-full py-3 px-4 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition">
                    <span v-if="loading">Memproses...</span>
                    <span v-else>Buat Booking</span>
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
            return diffDays > 7;
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
            // Placeholder - implement voucher validation
            this.errorMessage = 'Fitur voucher akan segera tersedia';
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
