{{-- siswa/classes/show.blade.php — FR-SW-04 / NFR-08 / M3 / M4 --}}
@extends('layouts.app')
@section('breadcrumb')<x-breadcrumb :items="[['label' => 'Kelas Saya', 'url' => route('siswa.classes.index')], ['label' => $class->name]]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5">
    <div class="kt-card">
        <div class="kt-card-content grid gap-3 p-7.5">
            <h1 class="text-xl font-medium text-mono">{{ $class->name }}</h1>
            <p class="text-secondary-foreground">Guru: {{ $class->guru->name }}</p>
            @if ($class->description)
                <p>{{ $class->description }}</p>
            @endif
            @if (! $class->is_active)
                <div class="kt-alert kt-alert-warning">Kelas nonaktif. Konten hanya dapat dibaca.</div>
            @endif
        </div>
    </div>

    <div class="kt-card">
        <div class="kt-card-header"><h3 class="kt-card-title text-sm">Pertemuan</h3></div>
        <div class="kt-card-content grid gap-5 p-7.5">
            @forelse($meetings as $meeting)
                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <span class="font-medium">{{ $meeting->title }}</span>
                        <span class="text-secondary-foreground text-sm">{{ $meeting->scheduled_at->translatedFormat('d M Y H:i') }}</span>
                    </div>
                    @if($meeting->notes)<p class="text-sm text-secondary-foreground">{{ $meeting->notes }}</p>@endif
                    @if($meeting->materials->isNotEmpty())
                        <div class="flex flex-wrap gap-2">
                            @foreach($meeting->materials as $material)
                                @if($material->type === 'figma')
                                    <a href="{{ $material->figma_url }}" target="_blank" rel="noopener" class="kt-btn kt-btn-sm kt-btn-outline">
                                        <i class="ki-filled ki-share me-1"></i>{{ $material->title }}
                                    </a>
                                @else
                                    <a href="{{ route('materials.download', $material) }}" class="kt-btn kt-btn-sm kt-btn-outline">
                                        <i class="ki-filled ki-file-down me-1"></i>{{ $material->title }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
                @if(! $loop->last)<div class="border-b border-border"></div>@endif
            @empty
                <p class="text-secondary-foreground text-sm">Belum ada pertemuan.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

