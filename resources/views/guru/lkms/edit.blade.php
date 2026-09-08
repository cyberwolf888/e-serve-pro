{{-- FR-SA-08 / FR-GR-15 / BR-09 / NFR-08 / M7.9 --}}
@extends('layouts.app')
@php($indexLabel = $routePrefix === 'admin' ? 'Kelas' : 'Kelas Saya')
@section('breadcrumb')<x-breadcrumb :items="[['label' => $indexLabel, 'url' => route($routePrefix.'.classes.index')], ['label' => $class->name, 'url' => route($routePrefix.'.classes.show', $class)], ['label' => 'LKM', 'url' => route($routePrefix.'.classes.lkms.index', $class)], ['label' => $lkm->title, 'url' => route($routePrefix.'.classes.lkms.show', [$class, $lkm])], ['label' => 'Ubah']]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5 py-6 xl:w-[46rem] mx-auto">
    <div class="flex items-center gap-3"><a href="{{ route($routePrefix.'.classes.lkms.show', [$class, $lkm]) }}" class="kt-btn kt-btn-ghost kt-btn-icon"><i class="ki-filled ki-arrow-left text-lg"></i></a><h1 class="text-xl font-semibold text-mono">Ubah LKM</h1></div>
    @if(session('success'))<div class="kt-alert kt-alert-success">{{ session('success') }}</div>@endif
    @error('role')<div class="kt-alert kt-alert-destructive">{{ $message }}</div>@enderror
    <div class="kt-card">
        <form method="POST" action="{{ route($routePrefix.'.classes.lkms.update', [$class, $lkm]) }}">
            @csrf @method('PUT')
            <div class="kt-card-content grid gap-4 p-7.5">
                <div><label class="kt-form-label" for="title">Judul LKM</label><input id="title" name="title" type="text" class="kt-input w-full" value="{{ old('title', $lkm->title) }}" required />@error('title')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror</div>
                <div><label class="kt-form-label" for="description">Deskripsi</label><textarea id="description" name="description" class="kt-textarea w-full" rows="4" required>{{ old('description', $lkm->description) }}</textarea>@error('description')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror</div>
                <label class="kt-label"><input type="hidden" name="is_published" value="0"><input class="kt-checkbox" type="checkbox" name="is_published" value="1" @checked(old('is_published', $lkm->is_published))>Publikasikan LKM</label>
            </div>
            <div class="kt-card-footer justify-end"><button class="kt-btn kt-btn-primary"><i class="ki-filled ki-check"></i>Simpan Detail</button></div>
        </form>
    </div>
    <h2 class="font-semibold">Peran dan SOP</h2>
    @if($structureLocked)
        <div class="kt-alert kt-alert-warning">Struktur peran dan SOP terkunci karena bukti sudah dikirim.</div>
        @foreach($lkm->roles as $role)
            <div class="kt-card"><div class="kt-card-content grid gap-3 p-5"><h3 class="font-semibold">{{ $role->name }}</h3><p class="text-sm">{{ $role->description }}</p><p class="text-sm text-secondary-foreground">{{ $role->instructions }}</p><ol class="list-decimal ps-5 text-sm">@foreach($role->sop_items as $item)<li>{{ $item }}</li>@endforeach</ol></div></div>
        @endforeach
    @else
        @foreach($lkm->roles as $role)
            <form method="POST" action="{{ route($routePrefix.'.classes.lkms.roles.update', [$class, $lkm, $role]) }}" class="grid gap-2">
                @csrf @method('PUT')
                @include('guru.lkms._role-fields', ['namePrefix' => '', 'idPrefix' => 'role_'.$role->id, 'roleData' => $role->toArray(), 'removable' => false])
                <div class="flex justify-end gap-2">
                    <button class="kt-btn kt-btn-primary">Simpan Peran</button>
                </div>
            </form>
            <form method="POST" action="{{ route($routePrefix.'.classes.lkms.roles.destroy', [$class, $lkm, $role]) }}" onsubmit="return confirm('Hapus peran {{ $role->name }}?')" class="flex justify-end">
                @csrf @method('DELETE')
                <button class="kt-btn kt-btn-sm kt-btn-destructive" @disabled($role->assignments_count)>Hapus Peran</button>
            </form>
        @endforeach
        <form method="POST" action="{{ route($routePrefix.'.classes.lkms.roles.store', [$class, $lkm]) }}" class="grid gap-2">
            @csrf
            @include('guru.lkms._role-fields', ['namePrefix' => '', 'idPrefix' => 'new_role', 'roleData' => ['sop_items' => ['']], 'removable' => false])
            <div class="flex justify-end"><button class="kt-btn kt-btn-outline kt-btn-primary"><i class="ki-filled ki-plus"></i>Tambah Peran</button></div>
        </form>
    @endif
</div>
@endsection
