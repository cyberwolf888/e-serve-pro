{{-- resources/views/errors/500.blade.php — NFR-08 Metronic error page --}}
@extends(auth()->check() ? 'layouts.app' : 'layouts.guest')

@section('title', 'Terjadi Kesalahan Server')

@section('content')
    <div class="flex min-h-[calc(100vh-12rem)] flex-col items-center justify-center text-center">
        <div class="mb-16">
            <img class="dark:hidden max-h-[160px]" src="{{ asset('assets/media/illustrations/20.svg') }}" alt=""/>
            <img class="hidden dark:block max-h-[160px]" src="{{ asset('assets/media/illustrations/20-dark.svg') }}" alt=""/>
        </div>
        <span class="kt-badge kt-badge-primary kt-badge-outline mb-3">500 Error</span>
        <h1 class="text-2xl font-semibold text-mono mb-2">Terjadi kesalahan pada server</h1>
        <p class="text-base text-secondary-foreground mb-10">
            Silakan coba lagi nanti atau hubungi administrator untuk bantuan.
        </p>
        <a class="kt-btn kt-btn-primary" href="{{ url('/') }}">Kembali ke beranda</a>
    </div>
@endsection
