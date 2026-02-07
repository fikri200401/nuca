@extends('layouts.base')

@section('title', 'Treatment Kami')

@section('content')
<div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Treatment Kami</h1>
        <p class="text-xl text-indigo-100">Pilih treatment sesuai kebutuhan kecantikan Anda</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    @if($treatments->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($treatments as $treatment)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <div class="h-48 overflow-hidden">
                    @if($treatment->image)
                        <img src="{{ asset('storage/' . $treatment->image) }}" alt="{{ $treatment->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-full flex items-center justify-center">
                            <svg class="w-24 h-24 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $treatment->name }}</h3>
                    <p class="text-gray-600 text-sm mb-4">{{ $treatment->description }}</p>
                    
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-sm text-gray-500">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $treatment->duration_minutes }} menit
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $treatment->feedbacks_count }} review
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="text-2xl font-bold text-indigo-600">
                            {{ $treatment->formatted_price }}
                        </div>
                        <a href="{{ route('treatments.detail', $treatment->id) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition-all">
                            Detail
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <p class="text-gray-500 text-lg">Belum ada treatment tersedia.</p>
        </div>
    @endif
</div>
@endsection
