{{-- auth/forgot-password.blade.php — FR-AUTH-04 / BR-02 / NFR-08 --}}
<!DOCTYPE html>
<html class="h-full" data-kt-theme="true" data-kt-theme-mode="light" dir="ltr" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
    <title>Lupa Kata Sandi — {{ config('app.name') }}</title>
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
        <div class="flex justify-center items-center p-8 lg:p-10 order-2 lg:order-1">
            <div class="kt-card max-w-[370px] w-full">
                <form method="POST" action="{{ route('auth.forgot.email') }}" class="kt-card-content flex flex-col gap-5 p-10">
                    @csrf

                    <div class="text-center mb-2.5">
                        <h3 class="text-lg font-medium text-mono leading-none mb-2.5">Lupa Kata Sandi</h3>
                        <span class="text-sm text-secondary-foreground">
                            Masukkan email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
                        </span>
                    </div>

                    @if (session('status'))
                        <div class="kt-alert kt-alert-success">{{ session('status') }}</div>
                    @endif

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

                    <button type="submit" class="kt-btn kt-btn-primary flex justify-center grow">
                        Kirim Tautan Reset
                    </button>

                    <p class="text-center text-sm text-secondary-foreground">
                        <a href="{{ route('auth.login.show') }}" class="kt-link kt-link-underlined text-sm">Kembali ke halaman masuk</a>
                    </p>
                </form>
            </div>
        </div>

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
