{{-- NFR-08 — authenticated Metronic Demo 2 layout --}}
<!DOCTYPE html>
<html class="h-full" data-kt-theme="true" data-kt-theme-mode="light" dir="ltr" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
</head>
<body class="antialiased flex h-full text-base text-foreground bg-background [--header-height:100px] data-[kt-sticky-header=on]:[--header-height:60px]">

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

    <div class="flex grow flex-col in-data-[kt-sticky-header=on]:pt-(--header-height)">
        @include('layouts.partials.header')
        @include('layouts.partials.navbar')

        @hasSection('breadcrumb')
            <div class="mb-5 lg:mb-10">
                <div class="kt-container-fixed flex items-center justify-between flex-wrap gap-2.5">
                    <div class="flex items-center flex-wrap gap-3 lg:gap-5">
                        @yield('breadcrumb')
                    </div>
                </div>
            </div>
        @endif

        <main class="grow" id="content" role="main">
            <div class="kt-container-fixed">
                @yield('content')
            </div>
        </main>

        <footer class="footer">
            <div class="kt-container-fixed">
                <div class="flex flex-col md:flex-row justify-center md:justify-between items-center gap-3 py-5">
                    <div class="flex order-2 md:order-1 gap-2 font-normal text-sm text-secondary-foreground">
                        <span>{{ date('Y') }}©</span>
                        <a class="text-primary font-medium hover:text-primary-active" href="{{ url('/') }}">{{ config('app.name') }}</a>
                    </div>
                    <nav class="flex order-1 md:order-2 gap-4 font-normal text-sm text-secondary-foreground" aria-label="Tautan footer">
                        <a class="hover:text-primary" href="{{ route('profile.show') }}">Profil Saya</a>
                    </nav>
                </div>
            </div>
        </footer>
    </div>

    <script src="{{ asset('assets/js/core.bundle.js') }}"></script>
    @vite('resources/js/app.js')
    @stack('scripts')

</body>
</html>
