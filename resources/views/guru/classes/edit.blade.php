{{-- guru/classes/edit.blade.php — FR-GR-02 / FR-GR-03 / NFR-08 / M3 --}}
@extends('layouts.app')
@section('breadcrumb')<x-breadcrumb :items="[['label' => 'Kelas Saya', 'url' => route('guru.classes.index')], ['label' => $class->name]]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5 py-6 xl:w-[38.75rem] mx-auto">
    <div class="flex items-center gap-3"><a href="{{ route('guru.classes.index') }}" class="kt-btn kt-btn-ghost kt-btn-icon"><i class="ki-filled ki-arrow-left text-lg"></i></a><h1 class="text-xl font-semibold text-mono">{{ $class->name }}</h1></div>
    @include('guru.classes._tabs', ['class' => $class])
    @if(session('success'))<div class="kt-alert kt-alert-success">{{ session('success') }}</div>@endif
    <div class="kt-card"><form method="POST" action="{{ route('guru.classes.update', $class) }}">@csrf @method('PUT')<div class="kt-card-content grid gap-5 p-7.5">@include('guru.classes._form')</div><div class="kt-card-footer flex justify-end gap-2.5"><button class="kt-btn kt-btn-primary"><i class="ki-filled ki-check"></i>Simpan</button></div></form></div>
    <div class="kt-card"><form method="POST" action="{{ route('guru.classes.students.store', $class) }}">@csrf<div class="kt-card-content grid gap-4 p-7.5"><h2 class="text-base font-medium">Tambah Siswa</h2><input name="email" type="email" class="kt-input @error('email') border-destructive @enderror" placeholder="email siswa" required />@error('email')<p class="text-destructive text-xs">{{ $message }}</p>@enderror<button class="kt-btn kt-btn-outline kt-btn-primary self-start"><i class="ki-filled ki-plus"></i>Tambah</button></div></form></div>
    <button type="button" class="kt-btn kt-btn-destructive"
            data-kt-modal-toggle="#confirm_status_modal"
            data-action="{{ route('guru.classes.destroy', $class) }}"
            data-method="DELETE"
            data-message="Nonaktifkan kelas {{ $class->name }}? Kelas menjadi hanya-baca."
            data-label="Nonaktifkan"
            data-variant="destructive">
        <i class="ki-filled ki-minus-circle"></i>Nonaktifkan Kelas
    </button>
</div>
<x-confirm-status-modal />
@endsection
