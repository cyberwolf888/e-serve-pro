{{-- siswa/dashboard.blade.php — FR-SW-02 / FR-SW-06 / NFR-08 --}}
@extends('layouts.app')

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => 'Dashboard']]" />
@endsection

@section('content')
<div class="py-6">
    <h1 class="text-2xl font-semibold text-foreground mb-2">Dashboard Siswa</h1>
    <p class="text-secondary-foreground text-sm">Selamat datang, {{ auth()->user()->name }}.</p>
    <div class="kt-card mt-5"><div class="kt-card-header"><h3 class="kt-card-title text-sm">Nilai Terbaru</h3></div><div class="kt-card-content">@forelse($grades as $grade)<p class="text-sm">{{ $grade->schoolClass->name }}: <strong>{{ $grade->final_score }}</strong></p>@empty<p class="text-sm text-secondary-foreground">Belum ada nilai akhir.</p>@endforelse</div></div>
    <a href="{{ route('siswa.grades.index') }}" class="kt-btn kt-btn-outline kt-btn-primary mt-4">Lihat Semua Nilai</a>
</div>
@endsection
