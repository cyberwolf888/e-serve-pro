{{-- siswa/classes/show.blade.php — FR-SW-04 / NFR-08 / M3 --}}
@extends('layouts.app')
@section('breadcrumb')<x-breadcrumb :items="[['label' => 'Kelas Saya', 'url' => route('siswa.classes.index')], ['label' => $class->name]]" />@endsection
@section('content')
<div class="kt-card">
    <div class="kt-card-content grid gap-3 p-7.5">
        <h1 class="text-xl font-medium text-mono">{{ $class->name }}</h1>
        <p class="text-secondary-foreground">Guru: {{ $class->guru->name }}</p>
        @if ($class->description)
            <p>{{ $class->description }}</p>
        @endif
        @if (! $class->is_active)
            <div class="kt-alert kt-alert-warning">Kelas nonaktif. Konten hanya dapat dibaca.</div>
        @endif
    </div>
</div>
@endsection
