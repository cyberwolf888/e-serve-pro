{{-- resources/views/errors/404.blade.php — NFR-08 Metronic error page --}}
@extends(auth()->check() ? 'layouts.app' : 'layouts.guest')

@section('title', 'Halaman Tidak Ditemukan')

@section('content')
    <div class="flex min-h-[calc(100vh-12rem)] flex-col items-center justify-center text-center">
        <div class="mb-10">
            <img class="dark:hidden max-h-[160px]" src="{{ asset('assets/media/illustrations/19.svg') }}" alt=""/>
            <img class="hidden dark:block max-h-[160px]" src="{{ asset('assets/media/illustrations/19-dark.svg') }}" alt=""/>
        </div>
        <span class="kt-badge kt-badge-primary kt-badge-outline mb-3">404 Error</span>
        <h1 class="text-2xl font-semibold text-mono mb-2">Halaman tidak ditemukan</h1>
        <p class="text-base text-secondary-foreground mb-10">
            Halaman yang Anda cari tidak tersedia. Periksa alamat atau
            <a class="text-primary font-medium hover:text-primary-active" href="{{ url('/') }}">kembali ke beranda</a>.
        </p>
    </div>
@endsection
