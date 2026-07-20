{{-- layouts/public.blade.php — public marketing layout (landing page) --}}
{{-- FR-PUB-01 --}}
<!DOCTYPE html>
<html class="h-full scroll-smooth" data-kt-theme="true" data-kt-theme-mode="light" dir="ltr" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
</head>
<body class="antialiased flex flex-col h-full text-base text-foreground bg-background">

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

    {{-- Topbar — sticky on scroll (FR-PUB-01) --}}
    <div data-kt-sticky-wrapper>
        <header
            class="flex items-center justify-between px-6 lg:px-16 py-4 border-b border-border bg-background"
            data-kt-sticky="true"
            data-kt-sticky-name="header"
            data-kt-sticky-offset="10"
            data-kt-sticky-class="fixed top-0 inset-x-0 z-50 shadow-md"
        >
            <a href="{{ url('/') }}">
                <img class="dark:hidden max-h-[40px]" src="{{ asset('assets/media/logo-pro-bi-smart-black.png') }}" alt="{{ config('app.name') }}"/>
                <img class="hidden dark:block max-h-[40px]" src="{{ asset('assets/media/logo-pro-bi-smart-white.png') }}" alt="{{ config('app.name') }}"/>
            </a>
            <nav class="hidden lg:flex items-center gap-8 text-sm font-medium text-secondary-foreground">
                <a href="#" class="text-primary font-semibold underline underline-offset-8">Beranda</a>
                <a href="#fitur" class="hover:text-primary">Fitur</a>
                <a href="#tentang" class="hover:text-primary">Tentang</a>
                <a href="#kontak" class="hover:text-primary">Kontak</a>
            </nav>
            <a href="{{ route('auth.login.show') }}" class="kt-btn kt-btn-outline rounded-full">
                <span>Masuk</span>
                <i class="ki-outline ki-arrow-right"></i>
            </a>
        </header>
    </div>

    <main class="grow">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer id="kontak" class="border-t border-border px-6 lg:px-16 py-8">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-4 text-sm text-secondary-foreground">
            <div class="flex items-center gap-2.5">
                <img class="dark:hidden max-h-[28px]" src="{{ asset('assets/media/logo-pro-bi-smart-black.png') }}" alt="{{ config('app.name') }}"/>
                <img class="hidden dark:block max-h-[28px]" src="{{ asset('assets/media/logo-pro-bi-smart-white.png') }}" alt="{{ config('app.name') }}"/>
            </div>
            {{-- ASSUMPTION: placeholder institution/contact info, no real value supplied yet --}}
            <div>Program Riset Kurikulum Merdeka &middot; kontak@probismart.id</div>
            <div>&copy; {{ now()->year }} {{ config('app.name') }}. Seluruh hak cipta dilindungi.</div>
        </div>
    </footer>

    <script src="{{ asset('assets/js/core.bundle.js') }}"></script>
    @vite('resources/js/app.js')
    @stack('scripts')

</body>
</html>
