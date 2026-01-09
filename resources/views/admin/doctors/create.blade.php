@extends('layouts.admin')

@section('title', 'Tambah Dokter')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('admin.doctors.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
            ← Kembali ke Daftar Dokter
        </a>
    </div>

    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Tambah Dokter Baru</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Tambahkan data dokter yang akan menangani treatment di klinik.
                </p>
            </div>
        </div>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="{{ route('admin.doctors.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="shadow sm:rounded-md sm:overflow-hidden">
                    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                        
                        <!-- Doctor Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Nama Dokter <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" required 
                                   value="{{ old('name') }}"
                                   placeholder="Contoh: Dr. Amanda Putri"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('name') border-red-300 @enderror">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Specialization -->
                        <div>
                            <label for="specialization" class="block text-sm font-medium text-gray-700">
                                Spesialisasi
                            </label>
                            <input type="text" id="specialization" name="specialization" 
                                   value="{{ old('specialization') }}"
                                   placeholder="Contoh: Aesthetic Medicine"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('specialization') border-red-300 @enderror">
                            @error('specialization')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">
                                No. Telepon
                            </label>
                            <input type="text" id="phone" name="phone" 
                                   value="{{ old('phone') }}"
                                   placeholder="081234567890"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('phone') border-red-300 @enderror">
                            @error('phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                Email
                            </label>
                            <input type="email" id="email" name="email" 
                                   value="{{ old('email') }}"
                                   placeholder="dokter@example.com"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('email') border-red-300 @enderror">
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bio -->
                        <div>
                            <label for="bio" class="block text-sm font-medium text-gray-700">
                                Biografi / Keterangan
                            </label>
                            <textarea id="bio" name="bio" rows="4" 
                                      placeholder="Pengalaman, riwayat pendidikan, keahlian khusus, dll..."
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('bio') border-red-300 @enderror">{{ old('bio') }}</textarea>
                            @error('bio')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Photo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Foto Profil
                            </label>
                            <div class="mt-1 flex items-center space-x-5">
                                <div class="flex-shrink-0">
                                    <img id="preview" 
                                         class="h-32 w-32 rounded-full object-cover border-2 border-gray-300" 
                                         src="https://ui-avatars.com/api/?name=Dokter&size=128&background=6366f1&color=ffffff" 
                                         alt="Preview">
                                </div>
                                <div class="flex-grow">
                                    <input type="file" id="photo" name="photo" accept="image/*"
                                           class="block w-full text-sm text-gray-500
                                                  file:mr-4 file:py-2 file:px-4
                                                  file:rounded-md file:border-0
                                                  file:text-sm file:font-semibold
                                                  file:bg-indigo-50 file:text-indigo-700
                                                  hover:file:bg-indigo-100
                                                  @error('photo') border-red-300 @enderror">
                                    <p class="mt-2 text-sm text-gray-500">
                                        JPG, PNG atau JPEG (Max. 2MB)
                                    </p>
                                </div>
                            </div>
                            @error('photo')
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
                                            <li>Dokter baru akan otomatis berstatus "Aktif"</li>
                                            <li>Jadwal praktik dokter dapat diatur setelah data dokter tersimpan</li>
                                            <li>Pastikan nomor telepon dan email valid untuk komunikasi</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <a href="{{ route('admin.doctors.index') }}" 
                           class="inline-flex justify-center py-2 px-4 mr-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Batal
                        </a>
                        <button type="submit" 
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Simpan Dokter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Preview Script -->
<script>
document.getElementById('photo').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
        }
        reader.readAsDataURL(e.target.files[0]);
    }
});
</script>
@endsection
