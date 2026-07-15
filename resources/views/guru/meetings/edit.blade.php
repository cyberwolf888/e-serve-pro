{{-- guru/meetings/edit.blade.php — FR-GR-06 / NFR-08 / M4 --}}
@extends('layouts.app')
@section('breadcrumb')<x-breadcrumb :items="[['label' => 'Kelas Saya', 'url' => route('guru.classes.index')], ['label' => $class->name, 'url' => route('guru.classes.show', $class)], ['label' => 'Pertemuan', 'url' => route('guru.classes.meetings.index', $class)], ['label' => $meeting->title]]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5 py-6 xl:w-[38.75rem] mx-auto">
    <div class="flex items-center gap-3"><a href="{{ route('guru.classes.meetings.index', $class) }}" class="kt-btn kt-btn-ghost kt-btn-icon"><i class="ki-filled ki-arrow-left text-lg"></i></a><h1 class="text-xl font-semibold text-mono">{{ $meeting->title }}</h1></div>
    <div class="kt-card">
        <form method="POST" action="{{ route('guru.classes.meetings.update', [$class, $meeting]) }}">
            @csrf @method('PUT')
            <div class="kt-card-content grid gap-5 p-7.5">
                @include('guru.meetings._form')
            </div>
            <div class="kt-card-footer flex justify-end gap-2.5">
                <button class="kt-btn kt-btn-primary"><i class="ki-filled ki-check"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
