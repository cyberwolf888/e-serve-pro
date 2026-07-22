{{-- guru/meetings/edit.blade.php — FR-GR-06 / FR-SA-03 / NFR-08 / M4 / ADMIN_CLASS_ACCESS_PLAN --}}
@extends('layouts.app')
@php($indexLabel = $routePrefix === 'admin' ? 'Kelas' : 'Kelas Saya')
@section('breadcrumb')<x-breadcrumb :items="[['label' => $indexLabel, 'url' => route($routePrefix.'.classes.index')], ['label' => $class->name, 'url' => route($routePrefix.'.classes.show', $class)], ['label' => 'Pertemuan', 'url' => route($routePrefix.'.classes.meetings.index', $class)], ['label' => $meeting->title]]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5 py-6 xl:w-[38.75rem] mx-auto">
    <div class="flex items-center gap-3"><a href="{{ route($routePrefix.'.classes.meetings.index', $class) }}" class="kt-btn kt-btn-ghost kt-btn-icon"><i class="ki-filled ki-arrow-left text-lg"></i></a><h1 class="text-xl font-semibold text-mono">{{ $meeting->title }}</h1></div>
    <div class="kt-card">
        <form method="POST" action="{{ route($routePrefix.'.classes.meetings.update', [$class, $meeting]) }}">
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
