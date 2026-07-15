{{-- guru/materials/index.blade.php — FR-GR-04 / FR-GR-05 / NFR-08 / M4 --}}
@extends('layouts.app')
@section('breadcrumb')<x-breadcrumb :items="[['label' => 'Kelas Saya', 'url' => route('guru.classes.index')], ['label' => $class->name, 'url' => route('guru.classes.edit', $class)], ['label' => 'Materi']]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5">
    @include('guru.classes._tabs', ['class' => $class])
    <div class="flex items-center justify-between gap-3">
        <h1 class="text-xl font-medium text-mono">Materi — {{ $class->name }}</h1>
        @if($class->is_active)
        <a href="{{ route('guru.classes.materials.create', $class) }}" class="kt-btn kt-btn-primary">
            <i class="ki-filled ki-plus me-1.5"></i>Tambah Materi
        </a>
        @endif
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
                        <td>{{ $material->title }}</td>
                        <td>
                            <span class="kt-badge kt-badge-outline">{{ $material->type === 'figma' ? 'Figma' : 'PDF' }}</span>
                        </td>
                        <td>
                            <div class="flex gap-1.5">
                                @if($class->is_active)
                                <a href="{{ route('guru.classes.materials.edit', [$class, $material]) }}" class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost">
                                    <i class="ki-filled ki-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('guru.classes.materials.destroy', [$class, $material]) }}" onsubmit="return confirm('Hapus materi {{ $material->title }}?')">
                                    @csrf @method('DELETE')
                                    <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost text-destructive"><i class="ki-filled ki-trash"></i></button>
                                </form>
                                @endif
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
