{{-- guru/meetings/index.blade.php — FR-GR-06 / FR-SA-03 / NFR-08 / M4 / ADMIN_CLASS_ACCESS_PLAN --}}
@extends('layouts.app')
@php($indexLabel = $routePrefix === 'admin' ? 'Kelas' : 'Kelas Saya')
@section('breadcrumb')<x-breadcrumb :items="[['label' => $indexLabel, 'url' => route($routePrefix.'.classes.index')], ['label' => $class->name, 'url' => route($routePrefix.'.classes.show', $class)], ['label' => 'Pertemuan']]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5">
    @include('guru.classes._tabs', ['class' => $class, 'routePrefix' => $routePrefix])
    <div class="flex items-center justify-between gap-3">
        <h1 class="text-xl font-medium text-mono">Pertemuan — {{ $class->name }}</h1>
        @can('create', [App\Models\Meeting::class, $class])
        <a href="{{ route($routePrefix.'.classes.meetings.create', $class) }}" class="kt-btn kt-btn-primary">
            <i class="ki-filled ki-plus me-1.5"></i>Tambah Pertemuan
        </a>
        @endcan
    </div>
    @if(session('success'))<div class="kt-alert kt-alert-success">{{ session('success') }}</div>@endif
    <div class="kt-card">
        <div class="kt-card-content p-0">
            <table class="kt-table table-auto kt-table-border">
                <thead>
                    <tr>
                        <th class="min-w-[200px]">Judul</th>
                        <th class="min-w-[160px]">Jadwal</th>
                        <th class="min-w-[100px]">Materi</th>
                        <th class="min-w-[100px]">Absensi</th>
                        <th class="w-[120px]"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($meetings as $meeting)
                    <tr>
                        <td><a class="font-semibold text-primary hover:text-primary-active" href="{{ route($routePrefix.'.classes.meetings.show', [$class, $meeting]) }}">{{ $meeting->title }}</a></td>
                        <td>{{ $meeting->scheduled_at->translatedFormat('d M Y H:i') }}</td>
                        <td>{{ $meeting->materials_count }}</td>
                        <td>{{ $meeting->attendances_count }}</td>
                        <td>
                            <div class="flex gap-1.5">
                                @can('update', $meeting)
                                <a href="{{ route($routePrefix.'.classes.meetings.edit', [$class, $meeting]) }}" class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost"><i class="ki-filled ki-pencil"></i></a>
                                @endcan
                                @can('delete', $meeting)
                                <form method="POST" action="{{ route($routePrefix.'.classes.meetings.destroy', [$class, $meeting]) }}" onsubmit="return confirm('Hapus pertemuan {{ $meeting->title }}?')">
                                    @csrf @method('DELETE')
                                    <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost text-destructive"><i class="ki-filled ki-trash"></i></button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-8 text-center text-sm text-secondary-foreground">Belum ada pertemuan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
