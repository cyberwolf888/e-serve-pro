{{-- admin/materials/edit.blade.php — FR-SA-03 / FR-GR-04 / FR-GR-05 / NFR-08 / M4 --}}
@extends('layouts.app')
@section('breadcrumb')<x-breadcrumb :items="[['label' => 'Kelas', 'url' => route('admin.classes.index')], ['label' => $class->name, 'url' => route('admin.classes.show', $class)], ['label' => 'Materi', 'url' => route('admin.classes.materials.index', $class)], ['label' => $material->title]]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5 py-6 xl:w-[38.75rem] mx-auto">
    <div class="flex items-center gap-3"><a href="{{ route('admin.classes.materials.index', $class) }}" class="kt-btn kt-btn-ghost kt-btn-icon"><i class="ki-filled ki-arrow-left text-lg"></i></a><h1 class="text-xl font-semibold text-mono">{{ $material->title }}</h1></div>
    <div class="kt-card">
        <form method="POST" action="{{ route('admin.classes.materials.update', [$class, $material]) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="kt-card-content grid gap-5 p-7.5">
                @include('guru.materials._form', ['routePrefix' => 'admin'])
            </div>
            <div class="kt-card-footer flex justify-end gap-2.5">
                <button class="kt-btn kt-btn-primary"><i class="ki-filled ki-check"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
