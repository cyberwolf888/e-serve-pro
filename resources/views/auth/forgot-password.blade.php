{{-- auth/forgot-password.blade.php — FR-AUTH-04 / BR-02 / NFR-08 --}}
@extends('layouts.guest')

@section('content')
<div class="card shadow-sm">
    <div class="card-body p-8">

        <div class="text-center mb-8">
            <h1 class="text-2xl font-semibold text-foreground">Lupa Kata Sandi</h1>
            <p class="text-sm text-secondary-foreground mt-1">
                Masukkan email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
            </p>
        </div>

        @if (session('status'))
            <div class="alert alert-success mb-4">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('auth.forgot.email') }}" class="space-y-5">
            @csrf

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

            <button type="submit" class="btn btn-primary w-full">Kirim Tautan Reset</button>
        </form>

        <p class="text-center text-sm text-secondary-foreground mt-6">
            <a href="{{ route('auth.login.show') }}" class="text-primary hover:underline">Kembali ke halaman masuk</a>
        </p>

    </div>
</div>
@endsection
