{{-- FR-SA-08 / FR-GR-15 / BR-09 / §9 / NFR-08 / M7.9 --}}
@extends('layouts.app')
@php
    $indexLabel = $routePrefix === 'admin' ? 'Kelas' : 'Kelas Saya';
    $oldRoles = old('roles', [['name' => '', 'description' => '', 'instructions' => '', 'sop_items' => ['']]]);
@endphp
@section('breadcrumb')<x-breadcrumb :items="[['label' => $indexLabel, 'url' => route($routePrefix.'.classes.index')], ['label' => $class->name, 'url' => route($routePrefix.'.classes.show', $class)], ['label' => 'LKM', 'url' => route($routePrefix.'.classes.lkms.index', $class)], ['label' => 'Tambah']]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5 py-6 xl:w-[46rem] mx-auto">
    <div class="flex items-center gap-3"><a href="{{ route($routePrefix.'.classes.lkms.index', $class) }}" class="kt-btn kt-btn-ghost kt-btn-icon"><i class="ki-filled ki-arrow-left text-lg"></i></a><h1 class="text-xl font-semibold text-mono">Tambah LKM</h1></div>
    <form method="POST" action="{{ route($routePrefix.'.classes.lkms.store', $class) }}" class="grid gap-5">
        @csrf
        <div class="kt-card"><div class="kt-card-content grid gap-4 p-7.5">
            <div><label class="kt-form-label" for="title">Judul LKM</label><input id="title" name="title" type="text" class="kt-input w-full" value="{{ old('title') }}" required />@error('title')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror</div>
            <div><label class="kt-form-label" for="description">Deskripsi</label><textarea id="description" name="description" class="kt-textarea w-full" rows="4" required>{{ old('description') }}</textarea>@error('description')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror</div>
        </div></div>
        <div class="flex items-center justify-between gap-3"><h2 class="font-semibold">Peran dan SOP</h2><button type="button" class="kt-btn kt-btn-outline" data-add-role><i class="ki-filled ki-plus"></i>Tambah Peran</button></div>
        <div class="grid gap-5" data-lkm-roles data-next-index="{{ count($oldRoles) }}">
            @foreach($oldRoles as $index => $roleData)
                @include('guru.lkms._role-fields', ['namePrefix' => "roles[$index]", 'idPrefix' => "role_{$index}", 'removable' => true])
            @endforeach
        </div>
        <template data-lkm-role-template>
            @include('guru.lkms._role-fields', ['namePrefix' => 'roles[__ROLE__]', 'idPrefix' => 'role___ROLE__', 'roleData' => ['sop_items' => ['']], 'removable' => true])
        </template>
        <div class="flex justify-end gap-2.5"><a href="{{ route($routePrefix.'.classes.lkms.index', $class) }}" class="kt-btn kt-btn-outline">Batal</a><button class="kt-btn kt-btn-primary"><i class="ki-filled ki-check"></i>Simpan</button></div>
    </form>
</div>
@endsection
