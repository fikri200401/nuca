@extends('layouts.admin')

@section('title', 'Edit Treatment')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('admin.treatments.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
            ← Kembali ke Daftar Treatment
        </a>
    </div>

    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Edit Treatment</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Update informasi treatment yang ada.
                </p>
            </div>
        </div>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="{{ route('admin.treatments.update', $treatment) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="shadow sm:rounded-md sm:overflow-hidden">
                    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                        
                        <!-- Treatment Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Nama Treatment <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" required 
                                   value="{{ old('name', $treatment->name) }}"
                                   placeholder="Contoh: Chemical Peeling"
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
                            <textarea id="description" name="description" rows="4" 
                                      placeholder="Jelaskan manfaat dan proses treatment..."
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('description') border-red-300 @enderror">{{ old('description', $treatment->description) }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Duration -->
                        <div>
                            <label for="duration_minutes" class="block text-sm font-medium text-gray-700">
                                Durasi (menit) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="duration_minutes" name="duration_minutes" required 
                                   min="15" step="15"
                                   value="{{ old('duration_minutes', $treatment->duration_minutes) }}"
                                   placeholder="60"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('duration_minutes') border-red-300 @enderror">
                            <p class="mt-2 text-sm text-gray-500">
                                Durasi treatment dalam menit (minimal 15 menit, kelipatan 15)
                            </p>
                            @error('duration_minutes')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Price -->
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700">
                                Harga (Rp) <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input type="number" id="price" name="price" required 
                                       min="0" step="1000"
                                       value="{{ old('price', $treatment->price) }}"
                                       placeholder="250000"
                                       class="pl-12 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('price') border-red-300 @enderror">
                            </div>
                            @error('price')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Is Popular -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="is_popular" name="is_popular" type="checkbox" value="1"
                                       {{ old('is_popular', $treatment->is_popular) ? 'checked' : '' }}
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_popular" class="font-medium text-gray-700">Treatment Populer</label>
                                <p class="text-gray-500">Tandai jika ini adalah treatment yang paling diminati customer</p>
                            </div>
                        </div>

                        <!-- Warning if has bookings -->
                        @if($treatment->bookings()->exists())
                        <div class="rounded-md bg-yellow-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Perhatian!</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        Treatment ini sudah memiliki booking. Perubahan harga atau durasi tidak akan mempengaruhi booking yang sudah ada.
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>

                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <a href="{{ route('admin.treatments.index') }}" 
                           class="inline-flex justify-center py-2 px-4 mr-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Batal
                        </a>
                        <button type="submit" 
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Update Treatment
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
