{{-- auth/login.blade.php — FR-AUTH-01 / NFR-08 --}}
<!DOCTYPE html>
<html class="h-full" data-kt-theme="true" data-kt-theme-mode="light" dir="ltr" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
    <title>Masuk — {{ config('app.name') }}</title>
    <style>
        .branded-bg { background-image: url('/assets/media/images/2600x1600/login.jpg'); }
        .dark .branded-bg { background-image: url('/assets/media/images/2600x1600/login.jpg'); }
    </style>
</head>
<body class="antialiased flex h-full text-base text-foreground bg-background">

    <script>
        const defaultThemeMode = 'light';
        let themeMode;
        if (document.documentElement) {
            if (localStorage.getItem('kt-theme')) {
                themeMode = localStorage.getItem('kt-theme');
            } else if (document.documentElement.hasAttribute('data-kt-theme-mode')) {
                themeMode = document.documentElement.getAttribute('data-kt-theme-mode');
            } else {
                themeMode = defaultThemeMode;
            }
            if (themeMode === 'system') {
                themeMode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            document.documentElement.classList.add(themeMode);
        }
    </script>

    <div class="grid lg:grid-cols-2 grow">

        {{-- Form column --}}
        <div class="flex justify-center items-center p-8 lg:p-10 order-2 lg:order-1">
            <div class="kt-card max-w-[370px] w-full">
                <form method="POST" action="{{ route('auth.login') }}" class="kt-card-content flex flex-col gap-5 p-10">
                    @csrf

                    <div class="text-center mb-2.5">
                        <h3 class="text-lg font-medium text-mono leading-none mb-2.5">Masuk</h3>
                        <div class="flex items-center justify-center font-medium">
                            <span class="text-sm text-secondary-foreground me-1.5">Belum punya akun?</span>
                            <a class="text-sm link" href="{{ route('auth.register.show') }}">Daftar</a>
                        </div>
                    </div>

                    @if (session('status'))
                        <div class="kt-alert kt-alert-success">{{ session('status') }}</div>
                    @endif

                    {{-- Email --}}
                    <div class="flex flex-col gap-1">
                        <label class="kt-form-label font-normal text-mono" for="email">Email</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            autocomplete="email"
                            value="{{ old('email') }}"
                            placeholder="email@email.com"
                            class="kt-input @error('email') border-destructive @enderror"
                            required
                            autofocus
                        />
                        @error('email')
                            <p class="text-destructive text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center justify-between gap-1">
                            <label class="kt-form-label font-normal text-mono" for="password">Kata Sandi</label>
                            <a class="text-sm kt-link shrink-0" href="{{ route('auth.forgot.show') }}">Lupa kata sandi?</a>
                        </div>
                        <div class="kt-input" data-kt-toggle-password="true">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                placeholder="Masukkan kata sandi"
                                autocomplete="current-password"
                                required
                            />
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
                            <p class="text-destructive text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remember me --}}
                    <label class="kt-label">
                        <input class="kt-checkbox kt-checkbox-sm" name="remember" type="checkbox" value="1"/>
                        <span class="kt-checkbox-label">Ingat saya</span>
                    </label>

                    <button type="submit" class="kt-btn kt-btn-primary flex justify-center grow">
                        Masuk
                    </button>
                </form>
            </div>
        </div>

        {{-- Branded background column --}}
        <div class="lg:rounded-xl lg:border lg:border-border lg:m-5 order-1 lg:order-2 bg-center bg-cover bg-no-repeat branded-bg">
            <div class="flex flex-col p-8 lg:p-16 gap-4">
                <a href="{{ url('/') }}">
                    <img class="h-[64px] max-w-none dark:hidden" src="{{ asset('assets/media/logo-pro-bi-smart-black.png') }}" alt="{{ config('app.name') }}"/>
                    <img class="h-[64px] max-w-none hidden dark:block" src="{{ asset('assets/media/logo-pro-bi-smart-white.png') }}" alt="{{ config('app.name') }}"/>
                </a>
                <div class="flex flex-col gap-3">
                    <h3 class="text-2xl font-semibold text-mono">Portal Pembelajaran</h3>
                    <div class="text-base font-medium text-secondary-foreground">
                        Platform pembelajaran Bahasa Indonesia<br/>
                        berbasis <span class="text-mono font-semibold">Kurikulum Merdeka</span><br/>
                        untuk guru dan siswa.
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="{{ asset('assets/js/core.bundle.js') }}"></script>
    @vite('resources/js/app.js')
</body>
</html>
