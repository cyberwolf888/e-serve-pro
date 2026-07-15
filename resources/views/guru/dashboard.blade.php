{{-- guru/dashboard.blade.php — FR-GR-01 / NFR-08 --}}
@extends('layouts.app')

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => 'Dashboard']]" />
@endsection

@section('content')
<div class="py-6">
    <h1 class="text-2xl font-semibold text-foreground mb-2">Dashboard Guru</h1>
    <p class="text-secondary-foreground text-sm">Selamat datang, {{ auth()->user()->name }}.</p>
</div>
@endsection
