@extends('layouts.base')

@section('title', 'Login')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Login ke Akun Anda
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Gunakan WhatsApp, Username, atau Member Number
            </p>
        </div>

        @if(session('status'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
            {{ session('status') }}
        </div>
        @endif

        <form class="mt-8 space-y-6 bg-white p-8 rounded-lg shadow-lg" method="POST" action="{{ route('login.submit') }}">
            @csrf

            <div>
                <label for="identifier" class="block text-sm font-medium text-gray-700">
                    WhatsApp / Username / Member Number
                </label>
                <input id="identifier" 
                       name="identifier" 
                       type="text" 
                       required 
                       value="{{ old('identifier') }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('identifier') border-red-500 @enderror"
                       placeholder="081234567890 / username / MBR-001">
                @error('identifier')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">
                    Password
                </label>
                <input id="password" 
                       name="password" 
                       type="password" 
                       required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('password') border-red-500 @enderror">
                @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" 
                           name="remember" 
                           type="checkbox" 
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-900">
                        Ingat Saya
                    </label>
                </div>

                <div class="text-sm">
                    <a href="{{ route('forgot-password') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Lupa Password?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                    Login
                </button>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Daftar Sekarang
                    </a>
                </p>
            </div>

            <div class="mt-6 border-t pt-6">
                <p class="text-xs text-gray-500 text-center">
                    <strong>Demo Login:</strong><br>
                    Customer: <code class="bg-gray-100 px-2 py-1 rounded">customer / password</code><br>
                    Admin: <code class="bg-gray-100 px-2 py-1 rounded">admin / password</code>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
