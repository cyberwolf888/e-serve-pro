{{-- auth/register.blade.php — FR-AUTH-02 / NFR-08 --}}
@extends('layouts.guest')

@section('content')
<div class="card shadow-sm">
    <div class="card-body p-8">

        <div class="text-center mb-8">
            <h1 class="text-2xl font-semibold text-foreground">Daftar Akun Siswa</h1>
            <p class="text-sm text-secondary-foreground mt-1">Buat akun untuk bergabung dengan kelas</p>
        </div>

        <form method="POST" action="{{ route('auth.register') }}" class="space-y-5">
            @csrf

            {{-- Name --}}
            <div>
                <label class="form-label" for="name">Nama Lengkap</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    autocomplete="name"
                    value="{{ old('name') }}"
                    class="input @error('name') border-destructive @enderror"
                    required
                    autofocus
                />
                @error('name')
                    <p class="text-destructive text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

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
                />
                @error('email')
                    <p class="text-destructive text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label class="form-label" for="password">Kata Sandi</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    class="input @error('password') border-destructive @enderror"
                    required
                />
                @error('password')
                    <p class="text-destructive text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password confirm --}}
            <div>
                <label class="form-label" for="password_confirmation">Konfirmasi Kata Sandi</label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    class="input"
                    required
                />
            </div>

            <button type="submit" class="btn btn-primary w-full">Daftar</button>
        </form>

        <p class="text-center text-sm text-secondary-foreground mt-6">
            Sudah punya akun?
            <a href="{{ route('auth.login.show') }}" class="text-primary hover:underline font-medium">Masuk</a>
        </p>

    </div>
</div>
@endsection
