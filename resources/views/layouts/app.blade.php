{{-- layouts/app.blade.php — authenticated master layout, Layout 1 (sidebar-fixed + header-fixed) --}}
{{-- NFR-08 / M0 scaffold --}}
<!DOCTYPE html>
<html class="h-full" data-kt-theme="true" data-kt-theme-mode="light" dir="ltr" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
</head>
<body class="antialiased flex h-full text-base text-foreground bg-background demo1 kt-sidebar-fixed kt-header-fixed">

    {{-- Theme mode detection (localStorage → classList) --}}
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

    {{-- Main --}}
    <div class="flex grow">

        {{-- Sidebar --}}
        @include('layouts.partials.sidebar')

        {{-- Wrapper: header + content + footer --}}
        <div class="kt-wrapper flex grow flex-col">

            {{-- Header --}}
            @include('layouts.partials.header')

            {{-- Content --}}
            <main class="grow pt-5" id="content" role="main">
                {{-- Reparent target for breadcrumb on mobile --}}
                <div class="kt-container-fixed" id="contentContainer"></div>

                <div class="kt-container-fixed">
                    @yield('content')
                </div>
            </main>

            {{-- Footer --}}
            <footer class="kt-footer">
                <div class="kt-container-fixed">
                    <div class="flex flex-col md:flex-row justify-center md:justify-between items-center gap-3 py-5">
                        <div class="flex gap-2 font-normal text-sm text-secondary-foreground">
                            <span>{{ date('Y') }}©</span>
                            <a class="text-primary font-medium hover:text-primary-active" href="#">{{ config('app.name') }}</a>
                        </div>
                    </div>
                </div>
            </footer>

        </div>
        {{-- End Wrapper --}}

    </div>
    {{-- End Main --}}

    {{-- Scripts --}}
    <script src="{{ asset('assets/js/core.bundle.js') }}"></script>
    @vite('resources/js/app.js')
    @stack('scripts')

</body>
</html>
