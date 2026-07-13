{{-- auth/reset-password.blade.php — FR-AUTH-04 / NFR-08 --}}
@extends('layouts.guest')

@section('content')
<div class="card shadow-sm">
    <div class="card-body p-8">

        <div class="text-center mb-8">
            <h1 class="text-2xl font-semibold text-foreground">Atur Ulang Kata Sandi</h1>
        </div>

        <form method="POST" action="{{ route('auth.reset') }}" class="space-y-5">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}"/>

            {{-- Email --}}
            <div>
                <label class="form-label" for="email">Email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    autocomplete="email"
                    value="{{ old('email', request('email')) }}"
                    class="input @error('email') border-destructive @enderror"
                    required
                    autofocus
                />
                @error('email')
                    <p class="text-destructive text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- New password --}}
            <div>
                <label class="form-label" for="password">Kata Sandi Baru</label>
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

            {{-- Confirm --}}
            <div>
                <label class="form-label" for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    class="input"
                    required
                />
            </div>

            <button type="submit" class="btn btn-primary w-full">Simpan Kata Sandi</button>
        </form>

    </div>
</div>
@endsection
