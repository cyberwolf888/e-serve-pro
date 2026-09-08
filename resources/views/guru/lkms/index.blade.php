{{-- FR-SA-08 / FR-GR-15 / NFR-08 / M7.9 --}}
@extends('layouts.app')
@php($indexLabel = $routePrefix === 'admin' ? 'Kelas' : 'Kelas Saya')
@section('breadcrumb')<x-breadcrumb :items="[['label' => $indexLabel, 'url' => route($routePrefix.'.classes.index')], ['label' => $class->name, 'url' => route($routePrefix.'.classes.show', $class)], ['label' => 'LKM']]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5">
    @include('guru.classes._tabs', ['class' => $class, 'routePrefix' => $routePrefix])
    <div class="flex items-center justify-between gap-3">
        <h1 class="text-xl font-medium text-mono">LKM — {{ $class->name }}</h1>
        @can('create', [App\Models\Lkm::class, $class])
            <a href="{{ route($routePrefix.'.classes.lkms.create', $class) }}" class="kt-btn kt-btn-primary"><i class="ki-filled ki-plus"></i>Tambah LKM</a>
        @endcan
    </div>
    @if(session('success'))<div class="kt-alert kt-alert-success">{{ session('success') }}</div>@endif
    <div class="kt-card">
        <div class="kt-card-content p-0 overflow-x-auto">
            <table class="kt-table table-auto kt-table-border">
                <thead><tr><th class="min-w-[240px]">Judul</th><th>Status</th><th>Peran</th><th>Mahasiswa</th><th class="w-[120px]"></th></tr></thead>
                <tbody>
                @forelse($lkms as $lkm)
                    <tr>
                        <td><a href="{{ route($routePrefix.'.classes.lkms.show', [$class, $lkm]) }}" class="font-semibold text-primary hover:text-primary-active">{{ $lkm->title }}</a></td>
                        <td><span class="kt-badge {{ $lkm->is_published ? 'kt-badge-success' : '' }} kt-badge-outline">{{ $lkm->is_published ? 'Terbit' : 'Draf' }}</span></td>
                        <td>{{ $lkm->roles_count }}</td>
                        <td>{{ $lkm->assignments_count }}</td>
                        <td>@can('update', $lkm)<a href="{{ route($routePrefix.'.classes.lkms.edit', [$class, $lkm]) }}" class="kt-btn kt-btn-sm kt-btn-outline"><i class="ki-filled ki-pencil"></i>Ubah</a>@endcan</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-8 text-center text-sm text-secondary-foreground">Belum ada LKM.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($lkms->hasPages())<div class="kt-card-footer">{{ $lkms->links('vendor.pagination.compact') }}</div>@endif
    </div>
</div>
@endsection
