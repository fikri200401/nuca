@extends('layouts.base')

@section('title', 'Login')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-pink-50 via-purple-50 to-pink-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 mb-6">
                <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full flex items-center justify-center text-white font-serif font-bold text-xl shadow-lg">N</div>
                <span class="text-2xl font-serif font-bold text-gray-900">Nuca Beauty Skin</span>
            </a>
            <h2 class="mt-4 text-3xl font-serif font-bold text-gray-900">
                Login ke Akun Anda
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Gunakan WhatsApp, Username, atau Member Number
            </p>
        </div>

        @if(session('status'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('status') }}
        </div>
        @endif

        <form class="bg-white p-8 rounded-2xl shadow-xl border border-pink-100" method="POST" action="{{ route('login.submit') }}">
            @csrf

            <div class="space-y-5">
                <!-- Identifier Input -->
                <div>
                    <label for="identifier" class="block text-sm font-medium text-gray-700 mb-1">
                        WhatsApp / Username / Member Number
                    </label>
                    <input id="identifier" 
                           name="identifier" 
                           type="text" 
                           required 
                           value="{{ old('identifier') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition @error('identifier') border-red-500 @enderror"
                           placeholder="081234567890 / username / MBR-001">
                    @error('identifier')
                    <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password
                    </label>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition @error('password') border-red-500 @enderror">
                    @error('password')
                    <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Remember & Forgot -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" 
                               name="remember" 
                               type="checkbox" 
                               class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Ingat Saya
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="{{ route('forgot-password') }}" class="font-medium text-pink-600 hover:text-pink-700 transition">
                            Lupa Password?
                        </a>
                    </div>
                </div>

                <!-- Login Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-sm font-medium text-white bg-gradient-to-r from-pink-500 to-purple-600 hover:from-pink-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Login
                    </button>
                </div>
            </div>

            <!-- Register Link -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="font-semibold text-pink-600 hover:text-pink-700 transition">
                        Daftar Sekarang
                    </a>
                </p>
            </div>

            <!-- Demo Credentials -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-500 text-center mb-3">
                    <strong class="text-pink-600">Demo Login:</strong>
                </p>
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="bg-pink-50 border border-pink-100 rounded-lg p-3">
                        <p class="font-semibold text-pink-700 mb-1">Customer</p>
                        <p class="text-gray-600">WA: <code class="bg-white px-1.5 py-0.5 rounded">081234567892</code></p>
                        <p class="text-gray-600">Pass: <code class="bg-white px-1.5 py-0.5 rounded">password</code></p>
                    </div>
                    <div class="bg-purple-50 border border-purple-100 rounded-lg p-3">
                        <p class="font-semibold text-purple-700 mb-1">Admin</p>
                        <p class="text-gray-600">WA: <code class="bg-white px-1.5 py-0.5 rounded">081234567890</code></p>
                        <p class="text-gray-600">Pass: <code class="bg-white px-1.5 py-0.5 rounded">password</code></p>
                    </div>
                </div>
            </div>
        </form>

        <!-- Back to Home -->
        <div class="mt-6 text-center">
            <a href="{{ url('/') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-pink-600 transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
