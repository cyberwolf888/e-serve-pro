{{-- dashboard.blade.php — placeholder dashboard (M0 gate: Metronic layout renders) --}}
{{-- NFR-08 / M0 scaffold --}}
@extends('layouts.app')

@section('title', 'Dashboard — ' . config('app.name'))

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-5 pb-7.5">
        <div class="flex flex-col justify-center gap-2">
            <h1 class="text-xl font-medium leading-none text-mono">Dashboard</h1>
            <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                E-SERVEPro — Platform Pembelajaran Bahasa Indonesia
            </div>
        </div>
    </div>

    <div class="kt-card">
        <div class="kt-card-body p-6">
            <p class="text-secondary-foreground text-sm">
                Selamat datang di <span class="font-semibold text-foreground">E-SERVEPro</span>.
                Silakan gunakan menu di samping untuk navigasi.
            </p>
        </div>
    </div>
@endsection
