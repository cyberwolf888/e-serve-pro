{{-- guru/meetings/show.blade.php — FR-GR-06 / FR-GR-07 / FR-GR-08 / NFR-08 / M4 --}}
@extends('layouts.app')
@section('breadcrumb')<x-breadcrumb :items="[['label' => 'Kelas Saya', 'url' => route('guru.classes.index')], ['label' => $class->name, 'url' => route('guru.classes.edit', $class)], ['label' => 'Pertemuan', 'url' => route('guru.classes.meetings.index', $class)], ['label' => $meeting->title]]" />@endsection
@section('content')
@php($sharedIds = $meeting->materials->pluck('id')->all())
<div class="grid gap-5 lg:gap-7.5 py-6 xl:w-[38.75rem] mx-auto">
    <div class="flex items-center gap-3">
        <a href="{{ route('guru.classes.meetings.index', $class) }}" class="kt-btn kt-btn-ghost kt-btn-icon"><i class="ki-filled ki-arrow-left text-lg"></i></a>
        <h1 class="text-xl font-semibold text-mono">{{ $meeting->title }}</h1>
    </div>

    @if(session('success'))<div class="kt-alert kt-alert-success">{{ session('success') }}</div>@endif

    <div class="kt-card">
        <div class="kt-card-content grid gap-2 p-7.5">
            <p class="text-secondary-foreground">{{ $meeting->scheduled_at->translatedFormat('d M Y H:i') }}</p>
            @if($meeting->notes)<p>{{ $meeting->notes }}</p>@endif
            <a href="{{ route('guru.classes.meetings.attendance.edit', [$class, $meeting]) }}" class="kt-btn kt-btn-outline kt-btn-primary self-start">
                <i class="ki-filled ki-notepad-edit me-1.5"></i>Catat Absensi
            </a>
        </div>
    </div>

    <div class="kt-card">
        <div class="kt-card-header"><h3 class="kt-card-title text-sm">Bagikan Materi</h3></div>
        <form method="POST" action="{{ route('guru.classes.meetings.share', [$class, $meeting]) }}">
            @csrf
            <div class="kt-card-content grid gap-3 p-7.5">
                @forelse($classMaterials as $material)
                    <label class="kt-form-label flex items-center gap-2.5">
                        <input type="checkbox" name="material_ids[]" value="{{ $material->id }}" class="kt-checkbox" @checked(in_array($material->id, $sharedIds)) />
                        <span>{{ $material->title }}</span>
                        <span class="kt-badge kt-badge-outline">{{ $material->type === 'figma' ? 'Figma' : 'PDF' }}</span>
                    </label>
                @empty
                    <p class="text-secondary-foreground text-sm">Belum ada materi di kelas ini.</p>
                @endforelse
            </div>
            <div class="kt-card-footer flex justify-end">
                <button class="kt-btn kt-btn-primary"><i class="ki-filled ki-check"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
