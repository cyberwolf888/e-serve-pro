{{-- profile/show.blade.php — FR-AUTH-01 / FR-AUTH-05 / FR-AUTH-06 / BR-05 / NFR-08 --}}
@extends('layouts.app')

@section('title', 'Profil Saya - '.config('app.name'))

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Profil Saya'],
    ]" />
@endsection

@section('content')
    <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
        <div class="flex flex-col justify-center gap-2">
            <h1 class="text-xl font-medium leading-none text-mono">Profil Saya</h1>
            <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                {{ $user->email }}
                <span class="kt-badge kt-badge-outline kt-badge-info kt-badge-sm ms-1">{{ $roleLabel }}</span>
                @if (! $user->is_active)
                    <span class="kt-badge kt-badge-outline kt-badge-danger kt-badge-sm">Nonaktif</span>
                @endif
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="kt-alert kt-alert-success flex items-center gap-2 mb-5" data-auto-dismiss>
            <i class="ki-filled ki-check-circle text-success"></i>
            {{ session('status') }}
        </div>
    @endif

    <div class="grid gap-5 lg:gap-7.5 xl:w-[38.75rem] mx-auto">
        <div class="kt-card pb-2.5">
            <div class="kt-card-header">
                <h3 class="kt-card-title">Informasi Akun</h3>
            </div>
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')
                <div class="kt-card-content grid gap-5">
                    @if (! $user->is_active)
                        <div class="px-4 py-3 rounded-lg bg-danger/10 text-danger border border-danger/20 text-sm">
                            Akun Anda dinonaktifkan. Pengeditan profil tidak diizinkan.
                        </div>
                    @endif

                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label for="name" class="kt-form-label max-w-56">Nama Lengkap</label>
                        <div class="grow">
                            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}"
                                   class="kt-input w-full @error('name') border-destructive @enderror"
                                   {{ ! $user->is_active ? 'disabled' : '' }} required>
                            @error('name')
                                <p class="text-destructive text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label for="email" class="kt-form-label max-w-56">Email</label>
                        <div class="grow">
                            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
                                   class="kt-input w-full @error('email') border-destructive @enderror"
                                   {{ ! $user->is_active ? 'disabled' : '' }} required>
                            @error('email')
                                <p class="text-destructive text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="border-t border-border my-2"></div>

                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label for="current_password" class="kt-form-label max-w-56">Kata Sandi Saat Ini</label>
                        <div class="grow">
                            <div class="kt-input @error('current_password') border-destructive @enderror" data-kt-toggle-password="true">
                                <input id="current_password" name="current_password" type="password"
                                       {{ ! $user->is_active ? 'disabled' : '' }}>
                                <button class="kt-btn kt-btn-sm kt-btn-ghost kt-btn-icon bg-transparent! -me-1.5" data-kt-toggle-password-trigger="true" type="button">
                                    <span class="kt-toggle-password-active:hidden">
                                        <i class="ki-filled ki-eye text-muted-foreground"></i>
                                    </span>
                                    <span class="hidden kt-toggle-password-active:block">
                                        <i class="ki-filled ki-eye-slash text-muted-foreground"></i>
                                    </span>
                                </button>
                            </div>
                            <p class="text-xs text-secondary-foreground mt-1">Wajib diisi jika mengubah kata sandi.</p>
                            @error('current_password')
                                <p class="text-destructive text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label for="password" class="kt-form-label max-w-56">Kata Sandi Baru</label>
                        <div class="grow">
                            <div class="kt-input @error('password') border-destructive @enderror" data-kt-toggle-password="true">
                                <input id="password" name="password" type="password"
                                       {{ ! $user->is_active ? 'disabled' : '' }}>
                                <button class="kt-btn kt-btn-sm kt-btn-ghost kt-btn-icon bg-transparent! -me-1.5" data-kt-toggle-password-trigger="true" type="button">
                                    <span class="kt-toggle-password-active:hidden">
                                        <i class="ki-filled ki-eye text-muted-foreground"></i>
                                    </span>
                                    <span class="hidden kt-toggle-password-active:block">
                                        <i class="ki-filled ki-eye-slash text-muted-foreground"></i>
                                    </span>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-destructive text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label for="password_confirmation" class="kt-form-label max-w-56">Konfirmasi Kata Sandi Baru</label>
                        <div class="grow">
                            <div class="kt-input" data-kt-toggle-password="true">
                                <input id="password_confirmation" name="password_confirmation" type="password"
                                       {{ ! $user->is_active ? 'disabled' : '' }}>
                                <button class="kt-btn kt-btn-sm kt-btn-ghost kt-btn-icon bg-transparent! -me-1.5" data-kt-toggle-password-trigger="true" type="button">
                                    <span class="kt-toggle-password-active:hidden">
                                        <i class="ki-filled ki-eye text-muted-foreground"></i>
                                    </span>
                                    <span class="hidden kt-toggle-password-active:block">
                                        <i class="ki-filled ki-eye-slash text-muted-foreground"></i>
                                    </span>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <p class="text-destructive text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-2.5">
                        <a href="{{ url()->previous() }}" class="kt-btn kt-btn-outline">
                            <i class="ki-filled ki-arrow-left"></i>Kembali
                        </a>
                        @if ($user->is_active)
                            <button type="submit" class="kt-btn kt-btn-primary">
                                <i class="ki-filled ki-check"></i>Simpan Perubahan
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="kt-card pb-2.5">
            <div class="kt-card-header">
                <h3 class="kt-card-title">Detail Akun</h3>
            </div>
            <div class="kt-card-content grid gap-4 text-sm">
                <div class="flex justify-between">
                    <span class="text-secondary-foreground">Bergabung Sejak</span>
                    <span class="font-medium">{{ $user->created_at?->translatedFormat('d F Y') ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-secondary-foreground">Status</span>
                    <span class="font-medium {{ $user->is_active ? 'text-success' : 'text-danger' }}">
                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-secondary-foreground">Peran</span>
                    <span class="font-medium">{{ $roleLabel }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    // Auto-dismiss flash alerts after 5 seconds
    document.querySelectorAll('[data-auto-dismiss]').forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 300ms ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
}());
</script>
@endpush
