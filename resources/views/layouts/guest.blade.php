{{-- layouts/guest.blade.php — unauthenticated layout (login, register, password reset) --}}
{{-- NFR-08 / M0 scaffold --}}
<!DOCTYPE html>
<html class="h-full" data-kt-theme="true" data-kt-theme-mode="light" dir="ltr" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
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

    <div class="flex items-center justify-center grow bg-center bg-no-repeat bg-background w-full">
        <div class="w-full max-w-md px-6 py-8">

            <div class="flex justify-center mb-6">
                <a href="{{ url('/') }}">
                    <img class="dark:hidden max-h-[64px]" src="{{ asset('assets/media/logo-pro-bi-smart-black.png') }}" alt="{{ config('app.name') }}"/>
                    <img class="hidden dark:block max-h-[64px]" src="{{ asset('assets/media/logo-pro-bi-smart-white.png') }}" alt="{{ config('app.name') }}"/>
                </a>
            </div>

            @yield('content')

        </div>
    </div>

    <script src="{{ asset('assets/js/core.bundle.js') }}"></script>
    @vite('resources/js/app.js')
    @stack('scripts')

</body>
</html>
