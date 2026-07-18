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
            @if($demoUsers->isNotEmpty())
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-xs text-center mb-3">
                    <strong class="text-pink-600">Demo Login:</strong>
                    <span class="text-gray-400"> Klik untuk mengisi otomatis</span>
                </p>

                @php
                    $roleConfig = [
                        'customer'  => ['label' => 'Customer',  'bg' => 'bg-pink-50',   'border' => 'border-pink-100',   'hover' => 'hover:bg-pink-100 hover:border-pink-300',   'text' => 'text-pink-700',   'htext' => 'group-hover:text-pink-800'],
                        'frontdesk' => ['label' => 'Frontdesk', 'bg' => 'bg-blue-50',   'border' => 'border-blue-100',   'hover' => 'hover:bg-blue-100 hover:border-blue-300',   'text' => 'text-blue-700',   'htext' => 'group-hover:text-blue-800'],
                        'doctor'    => ['label' => 'Dokter',    'bg' => 'bg-green-50',  'border' => 'border-green-100',  'hover' => 'hover:bg-green-100 hover:border-green-300',  'text' => 'text-green-700',  'htext' => 'group-hover:text-green-800'],
                        'admin'     => ['label' => 'Admin',     'bg' => 'bg-purple-50', 'border' => 'border-purple-100', 'hover' => 'hover:bg-purple-100 hover:border-purple-300','text' => 'text-purple-700', 'htext' => 'group-hover:text-purple-800'],
                        'owner'     => ['label' => 'Owner',     'bg' => 'bg-orange-50', 'border' => 'border-orange-100', 'hover' => 'hover:bg-orange-100 hover:border-orange-300','text' => 'text-orange-700', 'htext' => 'group-hover:text-orange-800'],
                    ];
                    $cols = $demoUsers->count() <= 2 ? 'grid-cols-2' : ($demoUsers->count() === 3 ? 'grid-cols-3' : 'grid-cols-2 sm:grid-cols-' . min($demoUsers->count(), 4));
                @endphp

                <div class="grid {{ $cols }} gap-2 text-xs">
                    @foreach($demoUsers as $demo)
                    @php $cfg = $roleConfig[$demo->role] ?? ['label' => ucfirst($demo->role), 'bg' => 'bg-gray-50', 'border' => 'border-gray-100', 'hover' => 'hover:bg-gray-100', 'text' => 'text-gray-700', 'htext' => 'group-hover:text-gray-800']; @endphp
                    <button type="button"
                            onclick="fillDemo('{{ $demo->whatsapp_number }}', 'password')"
                            class="{{ $cfg['bg'] }} {{ $cfg['border'] }} {{ $cfg['hover'] }} border rounded-lg p-3 text-left hover:shadow-md transition-all duration-150 cursor-pointer group">
                        <p class="font-semibold {{ $cfg['text'] }} {{ $cfg['htext'] }} mb-1.5 flex items-center justify-between">
                            {{ $cfg['label'] }}
                            <svg class="w-3 h-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </p>
                        <p class="text-gray-500 truncate">{{ $demo->name }}</p>
                        <p class="text-gray-500 mt-0.5">WA: <code class="bg-white px-1 py-0.5 rounded text-gray-700">{{ $demo->whatsapp_number }}</code></p>
                        <p class="text-gray-400 mt-0.5">Pass: <code class="bg-white px-1 py-0.5 rounded text-gray-600">password</code></p>
                    </button>
                    @endforeach
                </div>
            </div>
            @endif
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

@push('scripts')
<script>
function fillDemo(identifier, password) {
    document.getElementById('identifier').value = identifier;
    document.getElementById('password').value = password;
    document.getElementById('identifier').focus();
}
</script>
@endpush
@endsection
