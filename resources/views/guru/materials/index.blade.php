{{-- guru/materials/index.blade.php — FR-GR-04 / FR-GR-05 / FR-SA-03 / NFR-08 / M4 / ADMIN_CLASS_ACCESS_PLAN --}}
@extends('layouts.app')
@php($indexLabel = $routePrefix === 'admin' ? 'Kelas' : 'Kelas Saya')
@section('breadcrumb')<x-breadcrumb :items="[['label' => $indexLabel, 'url' => route($routePrefix.'.classes.index')], ['label' => $class->name, 'url' => route($routePrefix.'.classes.show', $class)], ['label' => 'Materi']]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5">
    @include('guru.classes._tabs', ['class' => $class, 'routePrefix' => $routePrefix])
    <div class="flex items-center justify-between gap-3">
        <h1 class="text-xl font-medium text-mono">Materi — {{ $class->name }}</h1>
        @can('create', [App\Models\Material::class, $class])
        <a href="{{ route($routePrefix.'.classes.materials.create', $class) }}" class="kt-btn kt-btn-primary">
            <i class="ki-filled ki-plus me-1.5"></i>Tambah Materi
        </a>
        @endcan
    </div>
    @if(session('success'))<div class="kt-alert kt-alert-success">{{ session('success') }}</div>@endif
    <div class="kt-card">
        <div class="kt-card-content p-0">
            <table class="kt-table table-auto kt-table-border">
                <thead>
                    <tr>
                        <th class="min-w-[240px]">Judul</th>
                        <th class="min-w-[100px]">Jenis</th>
                        <th class="w-[100px]"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materials as $material)
                    <tr>
                        <td><span class="font-semibold text-primary">{{ $material->title }}</span></td>
                        <td>
                            <span class="kt-badge kt-badge-outline">{{ $material->type === 'figma' ? 'Tautan' : 'PDF' }}</span>
                        </td>
                        <td>
                            <div class="flex gap-1.5">
                                @can('update', $material)
                                <a href="{{ route($routePrefix.'.classes.materials.edit', [$class, $material]) }}" class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost">
                                    <i class="ki-filled ki-pencil"></i>
                                </a>
                                @endcan
                                @can('delete', $material)
                                <form method="POST" action="{{ route($routePrefix.'.classes.materials.destroy', [$class, $material]) }}" onsubmit="return confirm('Hapus materi {{ $material->title }}?')">
                                    @csrf @method('DELETE')
                                    <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost text-destructive"><i class="ki-filled ki-trash"></i></button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="py-8 text-center text-sm text-secondary-foreground">Belum ada materi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
