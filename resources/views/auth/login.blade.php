{{-- auth/login.blade.php — FR-AUTH-01 / NFR-08 --}}
@extends('layouts.guest')

@section('content')
<div class="card shadow-sm">
    <div class="card-body p-8">

        <div class="text-center mb-8">
            <h1 class="text-2xl font-semibold text-foreground">Masuk ke Akun</h1>
            <p class="text-sm text-secondary-foreground mt-1">Masukkan email dan kata sandi Anda</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success mb-4">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('auth.login') }}" class="space-y-5">
            @csrf

            {{-- Email --}}
            <div>
                <label class="form-label" for="email">Email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    autocomplete="email"
                    value="{{ old('email') }}"
                    class="input @error('email') border-destructive @enderror"
                    required
                    autofocus
                />
                @error('email')
                    <p class="text-destructive text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label class="form-label mb-0" for="password">Kata Sandi</label>
                    <a href="{{ route('auth.forgot.show') }}" class="text-xs text-primary hover:underline">Lupa kata sandi?</a>
                </div>
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    class="input"
                    required
                />
                @error('password')
                    <p class="text-destructive text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember --}}
            <div class="flex items-center gap-2">
                <input id="remember" name="remember" type="checkbox" class="checkbox" value="1"/>
                <label for="remember" class="text-sm text-secondary-foreground">Ingat saya</label>
            </div>

            <button type="submit" class="btn btn-primary w-full">Masuk</button>
        </form>

        <p class="text-center text-sm text-secondary-foreground mt-6">
            Belum punya akun?
            <a href="{{ route('auth.register.show') }}" class="text-primary hover:underline font-medium">Daftar sekarang</a>
        </p>

    </div>
</div>
@endsection
