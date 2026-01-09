@extends('layouts.admin')

@section('title', 'Buat Voucher')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('admin.vouchers.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
            ← Kembali ke Daftar Voucher
        </a>
    </div>

    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Buat Voucher Baru</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Buat voucher diskon untuk customer.
                </p>
            </div>
        </div>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="{{ route('admin.vouchers.store') }}" method="POST">
                @csrf
                <div class="shadow sm:rounded-md sm:overflow-hidden">
                    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                        
                        <!-- Voucher Code -->
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700">
                                Kode Voucher <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="code" name="code" required 
                                   value="{{ old('code') }}"
                                   placeholder="WELCOME2024"
                                   style="text-transform: uppercase;"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('code') border-red-300 @enderror">
                            <p class="mt-1 text-sm text-gray-500">Kode unik untuk voucher (akan otomatis uppercase)</p>
                            @error('code')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Voucher Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Nama Voucher <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" required 
                                   value="{{ old('name') }}"
                                   placeholder="Voucher Selamat Datang"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('name') border-red-300 @enderror">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                Deskripsi
                            </label>
                            <textarea id="description" name="description" rows="3" 
                                      placeholder="Syarat dan ketentuan voucher..."
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">
                                Tipe Diskon <span class="text-red-500">*</span>
                            </label>
                            <select id="type" name="type" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('type') border-red-300 @enderror">
                                <option value="">Pilih Tipe</option>
                                <option value="nominal" {{ old('type') == 'nominal' ? 'selected' : '' }}>Nominal (Rp)</option>
                                <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                            </select>
                            @error('type')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Value -->
                        <div>
                            <label for="value" class="block text-sm font-medium text-gray-700">
                                Nilai Diskon <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="value" name="value" required 
                                   min="0" step="0.01"
                                   value="{{ old('value') }}"
                                   placeholder="50000"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('value') border-red-300 @enderror">
                            <p class="mt-1 text-sm text-gray-500">Untuk nominal: dalam Rupiah. Untuk persentase: 0-100</p>
                            @error('value')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Min Transaction -->
                        <div>
                            <label for="min_transaction" class="block text-sm font-medium text-gray-700">
                                Minimal Transaksi (Rp) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="min_transaction" name="min_transaction" required 
                                   min="0" step="1000"
                                   value="{{ old('min_transaction', 0) }}"
                                   placeholder="100000"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('min_transaction') border-red-300 @enderror">
                            <p class="mt-1 text-sm text-gray-500">Minimal 0 jika tidak ada minimal transaksi</p>
                            @error('min_transaction')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Valid From -->
                        <div>
                            <label for="valid_from" class="block text-sm font-medium text-gray-700">
                                Berlaku Dari <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="valid_from" name="valid_from" required 
                                   value="{{ old('valid_from', date('Y-m-d')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('valid_from') border-red-300 @enderror">
                            @error('valid_from')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Valid Until -->
                        <div>
                            <label for="valid_until" class="block text-sm font-medium text-gray-700">
                                Berlaku Sampai <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="valid_until" name="valid_until" required 
                                   value="{{ old('valid_until') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('valid_until') border-red-300 @enderror">
                            @error('valid_until')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Max Usage -->
                        <div>
                            <label for="max_usage" class="block text-sm font-medium text-gray-700">
                                Maksimal Penggunaan
                            </label>
                            <input type="number" id="max_usage" name="max_usage" 
                                   min="1"
                                   value="{{ old('max_usage') }}"
                                   placeholder="100"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('max_usage') border-red-300 @enderror">
                            <p class="mt-1 text-sm text-gray-500">Kosongkan untuk unlimited</p>
                            @error('max_usage')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Is Single Use -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="is_single_use" name="is_single_use" type="checkbox" value="1"
                                       {{ old('is_single_use') ? 'checked' : '' }}
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_single_use" class="font-medium text-gray-700">Sekali Pakai Per User</label>
                                <p class="text-gray-500">Setiap customer hanya bisa menggunakan voucher ini 1 kali</p>
                            </div>
                        </div>

                        <!-- Show on Landing -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="show_on_landing" name="show_on_landing" type="checkbox" value="1"
                                       {{ old('show_on_landing') ? 'checked' : '' }}
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="show_on_landing" class="font-medium text-gray-700">Tampilkan di Landing Page</label>
                                <p class="text-gray-500">Voucher akan muncul di halaman utama untuk menarik customer baru</p>
                            </div>
                        </div>

                    </div>

                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <a href="{{ route('admin.vouchers.index') }}" 
                           class="inline-flex justify-center py-2 px-4 mr-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Batal
                        </a>
                        <button type="submit" 
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Buat Voucher
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Auto uppercase code
document.getElementById('code').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});
</script>
@endsection
