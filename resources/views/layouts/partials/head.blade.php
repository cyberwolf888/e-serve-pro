{{-- NFR-08 — shared Metronic document head --}}
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport"/>
<meta content="@yield('description')" name="description"/>
<title>@yield('title', config('app.name'))</title>

{{-- Favicon --}}
<link href="{{ asset('assets/media/favicon/apple-touch-icon.png') }}" rel="apple-touch-icon" sizes="180x180"/>
<link href="{{ asset('assets/media/favicon/favicon-32x32.png') }}" rel="icon" sizes="32x32" type="image/png"/>
<link href="{{ asset('assets/media/favicon/favicon-16x16.png') }}" rel="icon" sizes="16x16" type="image/png"/>
<link href="{{ asset('assets/media/favicon/favicon.ico') }}" rel="shortcut icon"/>

{{-- Google Fonts: Inter (same as Metronic starter kit) --}}
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>

{{-- KeenIcons --}}
<link href="{{ asset('assets/vendors/keenicons/styles.bundle.css') }}" rel="stylesheet"/>

{{-- Vite: Metronic source CSS, KTUI, components, and Tailwind utilities --}}
@vite('resources/css/app.css')

@stack('styles')
