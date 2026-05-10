@extends('layouts.app')
@php $pageTitle = 'Login' @endphp

@section('content')
<div class="min-h-screen flex items-center justify-center py-20" style="background-color: var(--bg-color);">
    <div class="w-full max-w-md">

        {{-- Logo --}}
        <div class="text-center mb-10">
            <a href="{{ route('home') }}" class="inline-flex flex-col items-center leading-none">
                <span class="logo-text text-3xl">{{ setting('site_name_short', 'ZAIN') }}</span>
                <span class="logo-subtext">{{ setting('site_name_sub', 'HANGER') }}</span>
            </a>
        </div>

        <div class="glass rounded-2xl p-10 shadow-lg">
            <h1 class="text-2xl font-bold text-center mb-2" style="color: var(--primary)">Masuk ke Akun</h1>
            <p class="text-sm text-center mb-8" style="color: var(--text-muted)">
                Login untuk mulai berbelanja dan lacak pesanan Anda
            </p>

            @if(session('error'))
                <div class="alert-error mb-6">{{ session('error') }}</div>
            @endif

            {{-- Google SSO --}}
            <a href="{{ route('auth.google') }}"
               class="flex items-center justify-center gap-3 w-full py-4 px-6 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 transition-all text-gray-700 font-semibold text-sm shadow-sm hover:shadow-md mb-6">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Lanjutkan dengan Google
            </a>

            <div class="relative mb-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="bg-white px-3" style="color: var(--text-muted)">atau login dengan email</span>
                </div>
            </div>

            {{-- Email Login --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold mb-2" style="color: var(--text-muted)">EMAIL</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="filter-select"
                           placeholder="email@example.com">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold mb-2" style="color: var(--text-muted)">PASSWORD</label>
                    <input type="password" name="password" required
                           class="filter-select"
                           placeholder="••••••••">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm" style="color: var(--text-muted)">
                        <input type="checkbox" name="remember" class="rounded">
                        Ingat saya
                    </label>
                    <a href="#" class="text-sm hover:underline" style="color: var(--primary)">
                        Lupa password?
                    </a>
                </div>
                <button type="submit" class="btn btn-primary w-full justify-center">
                    Masuk
                </button>
            </form>

            <p class="text-center text-sm mt-6" style="color: var(--text-muted)">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-semibold hover:underline" style="color: var(--primary)">Daftar sekarang</a>
            </p>
        </div>

        <p class="text-center text-xs mt-6" style="color: var(--text-muted)">
            Dengan login, Anda menyetujui <a href="#" class="underline">Syarat & Ketentuan</a> kami.
        </p>
    </div>
</div>
@endsection
