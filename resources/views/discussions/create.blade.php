{{-- discussions/create.blade.php — FR-GR-14 / FR-SW-07 / NFR-08 / M7.8 --}}
@extends('layouts.app')
@php
    $indexLabel = $routePrefix === 'admin' ? 'Kelas' : 'Kelas Saya';
    $materialsRoute = $routePrefix === 'siswa'
        ? route('siswa.classes.show', $class)
        : route($routePrefix.'.classes.materials.index', $class);
@endphp
@section('breadcrumb')<x-breadcrumb :items="[['label' => $indexLabel, 'url' => route($routePrefix.'.classes.index')], ['label' => $class->name, 'url' => route($routePrefix.'.classes.show', $class)], ['label' => 'Materi', 'url' => $materialsRoute], ['label' => $material->title, 'url' => route($routePrefix.'.classes.materials.discussions.index', [$class, $material])], ['label' => 'Buat Topik']]" />@endsection
@section('content')
<div class="mx-auto grid gap-5 py-6 lg:gap-7.5 xl:w-[46rem]">
    <div class="flex items-center gap-3">
        <a href="{{ route($routePrefix.'.classes.materials.discussions.index', [$class, $material]) }}" class="kt-btn kt-btn-ghost kt-btn-icon"><i class="ki-filled ki-arrow-left text-lg"></i></a>
        <div>
            <h1 class="text-xl font-semibold text-mono">Buat Topik Diskusi</h1>
            <p class="mt-1 text-sm text-secondary-foreground">{{ $material->title }} · {{ $class->name }}</p>
        </div>
    </div>
    <div class="kt-card">
        <form method="POST" action="{{ route($routePrefix.'.classes.materials.discussions.store', [$class, $material]) }}">
            @csrf
            <div class="kt-card-content grid gap-5 p-7.5">
                <div>
                    <label class="kt-form-label mb-2" for="title">Judul Topik</label>
                    <input id="title" name="title" class="kt-input @error('title') border-destructive @enderror" value="{{ old('title') }}" maxlength="255" required autofocus />
                    @error('title')<p class="mt-1 text-xs text-destructive">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="kt-form-label mb-2" for="body">Isi Diskusi</label>
                    <textarea id="body" name="body" rows="9" class="kt-textarea @error('body') border-destructive @enderror" maxlength="10000" required>{{ old('body') }}</textarea>
                    @error('body')<p class="mt-1 text-xs text-destructive">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="kt-card-footer flex justify-end gap-2.5">
                <a href="{{ route($routePrefix.'.classes.materials.discussions.index', [$class, $material]) }}" class="kt-btn kt-btn-outline"><i class="ki-filled ki-cross"></i>Batal</a>
                <button class="kt-btn kt-btn-primary"><i class="ki-filled ki-check"></i>Terbitkan Topik</button>
            </div>
        </form>
    </div>
</div>
@endsection
