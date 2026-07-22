{{-- guru/classes/show.blade.php — FR-GR-02 / FR-GR-03 / FR-SA-03 / NFR-08 / M3 / ADMIN_CLASS_ACCESS_PLAN --}}
@extends('layouts.app')
@php($indexLabel = $routePrefix === 'admin' ? 'Kelas' : 'Kelas Saya')
@section('breadcrumb')<x-breadcrumb :items="[['label' => $indexLabel, 'url' => route($routePrefix.'.classes.index')], ['label' => $class->name]]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5">
    <div class="kt-card">
        <div class="kt-card-content p-5 lg:p-7.5">
            <div class="flex items-center justify-between gap-3 mb-5">
                <div class="flex items-center gap-3">
                    <a href="{{ route($routePrefix.'.classes.index') }}" class="kt-btn kt-btn-ghost kt-btn-icon"><i class="ki-filled ki-arrow-left text-lg"></i></a>
                    <h1 class="text-xl font-semibold text-mono">{{ $class->name }}</h1>
                </div>
                @can('update', $class)
                <div class="flex items-center gap-2.5">
                    <a href="{{ route($routePrefix.'.classes.edit', $class) }}" class="kt-btn kt-btn-outline kt-btn-primary"><i class="ki-filled ki-pencil"></i>Edit</a>
                    @can('deactivate', $class)
                    <button type="button" class="kt-btn kt-btn-destructive"
                            data-kt-modal-toggle="#confirm_status_modal"
                            data-action="{{ route($routePrefix.'.classes.destroy', $class) }}"
                            data-method="DELETE"
                            data-message="Nonaktifkan kelas {{ $class->name }}? Kelas menjadi hanya-baca."
                            data-label="Nonaktifkan"
                            data-variant="destructive">
                        <i class="ki-filled ki-minus-circle"></i>Nonaktifkan Kelas
                    </button>
                    @endcan
                </div>
                @endcan
            </div>
            @include('guru.classes._tabs', ['class' => $class, 'routePrefix' => $routePrefix])
        </div>
    </div>

    @if(session('success'))<div class="kt-alert kt-alert-success">{{ session('success') }}</div>@endif

    <div class="grid lg:grid-cols-12 gap-5 lg:gap-7.5 items-start">
        <div class="lg:col-span-4 kt-card self-start">
            <div class="kt-card-header"><h3 class="kt-card-title text-sm">Info Kelas</h3></div>
            <div class="kt-card-content p-5 lg:p-7.5">
                <div>
                    <span class="block text-xs font-medium uppercase tracking-wide text-secondary-foreground mb-1">Kode Kelas</span>
                    <span class="inline-flex items-center rounded-md border border-border px-2.5 py-1.5 text-sm font-medium">{{ $class->class_code }}</span>
                </div>
                @if($routePrefix === 'admin')
                <div class="mt-5">
                    <span class="block text-xs font-medium uppercase tracking-wide text-secondary-foreground mb-1">Guru</span>
                    <p class="text-sm text-foreground">{{ $class->guru->name }}</p>
                </div>
                @endif
                <div class="mt-5">
                    <span class="block text-xs font-medium uppercase tracking-wide text-secondary-foreground mb-1">Deskripsi</span>
                    <p class="text-sm text-foreground">{{ $class->description ?: '—' }}</p>
                </div>
                <div class="mt-5">
                    <span class="block text-xs font-medium uppercase tracking-wide text-secondary-foreground mb-1">Status</span>
                    @if($class->is_active)
                        <span class="kt-badge kt-badge-success kt-badge-outline rounded-[30px]"><span class="kt-badge-dot size-1.5"></span>Aktif</span>
                    @else
                        <span class="kt-badge kt-badge-danger kt-badge-outline rounded-[30px]"><span class="kt-badge-dot size-1.5"></span>Nonaktif</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="lg:col-span-8 kt-card">
            <div class="kt-card-header flex items-center justify-between gap-3">
                <h3 class="kt-card-title text-sm">Siswa Terdaftar ({{ $members->total() }})</h3>
                @can('addStudent', $class)
                <form method="POST" action="{{ route($routePrefix.'.classes.students.store', $class) }}" class="flex items-start gap-2.5">
                    @csrf
                    <input name="email" type="email" class="kt-input min-w-[16rem] @error('email') border-destructive @enderror" placeholder="email siswa" required />
                    <button class="kt-btn kt-btn-outline kt-btn-primary shrink-0"><i class="ki-filled ki-plus"></i>Tambah</button>
                </form>
                @endcan
            </div>
            @can('addStudent', $class)
                @error('email')<div class="px-5 lg:px-7.5 pb-3"><p class="text-destructive text-xs">{{ $message }}</p></div>@enderror
            @endcan
            <div class="kt-card-content p-0">
                <table class="kt-table table-auto kt-table-border">
                    <thead>
                        <tr>
                            <th class="min-w-[200px]">Nama</th>
                            <th class="min-w-[220px]">Email</th>
                            <th class="min-w-[160px]">Tanggal Gabung</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $member)
                        <tr>
                            <td>{{ $member->student->name }}</td>
                            <td class="text-secondary-foreground">{{ $member->student->email }}</td>
                            <td class="text-secondary-foreground">{{ $member->joined_at->translatedFormat('d M Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="py-8 text-center text-sm text-secondary-foreground">Belum ada siswa.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($members->hasPages())
            <div class="kt-card-footer justify-between items-center">
                <span class="text-sm text-secondary-foreground">Showing {{ $members->firstItem() }} to {{ $members->lastItem() }} of {{ $members->total() }} results</span>
                {{ $members->links('vendor.pagination.compact') }}
            </div>
            @endif
        </div>
    </div>
</div>
<x-confirm-status-modal />
@endsection
