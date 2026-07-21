{{-- siswa/classes/show.blade.php — FR-SW-04 / FR-SW-05 / NFR-08 / M3 / M4 --}}
@extends('layouts.app')
@section('breadcrumb')<x-breadcrumb :items="[['label' => 'Kelas Saya', 'url' => route('siswa.classes.index')], ['label' => $class->name]]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5">
    <div class="kt-card">
        <div class="kt-card-content grid gap-3 p-7.5">
            <h1 class="text-2xl font-semibold text-mono">{{ $class->name }}</h1>
            <div class="flex items-center gap-1.5 text-secondary-foreground">
                <i class="ki-filled ki-user text-base"></i>
                <span>Guru: {{ $class->guru->name }}</span>
            </div>
            @if ($class->description)
                <p>{{ $class->description }}</p>
            @endif
            @if (! $class->is_active)
                <div class="kt-alert kt-alert-warning">Kelas nonaktif. Konten hanya dapat dibaca.</div>
            @endif
        </div>
    </div>

    <div class="grid lg:grid-cols-12 gap-5 lg:gap-7.5">
        <div class="lg:col-span-4 kt-card">
            <div class="kt-card-header">
                <h3 class="kt-card-title flex items-center gap-2 text-xs font-semibold uppercase tracking-wide">
                    <i class="ki-filled ki-notepad text-base text-primary"></i>Kuis Tersedia
                </h3>
            </div>
            <div class="kt-card-content flex flex-col gap-[0.65rem] p-7.5">
                @forelse($quizzes as $quiz)
                    <div class="flex items-center justify-between gap-3">
                        <span class="font-medium {{ $quiz->attempted ? 'text-muted-foreground' : '' }}">{{ $quiz->title }}</span>
                        @if($quiz->attempted)
                            <span class="kt-badge kt-badge-success kt-badge-outline shrink-0">Selesai</span>
                        @else
                            <a href="{{ route('siswa.quizzes.show', $quiz) }}" class="kt-btn kt-btn-sm rounded-full border-0 bg-primary/10 text-primary hover:bg-primary/20 shrink-0">Kerjakan</a>
                        @endif
                    </div>
                @empty
                    <p class="text-secondary-foreground text-sm">Tidak ada kuis yang tersedia saat ini.</p>
                @endforelse
            </div>
        </div>

        <div class="lg:col-span-8 kt-card">
            <div class="kt-card-header">
                <h3 class="kt-card-title flex items-center gap-2 text-xs font-semibold uppercase tracking-wide">
                    <i class="ki-filled ki-calendar text-base text-primary"></i>Pertemuan
                </h3>
            </div>
            <div class="kt-card-content p-7.5">
                @forelse($meetings as $meeting)
                    <div class="grid grid-cols-[auto_1fr] gap-4 md:gap-5">
                        <div class="flex flex-col items-center">
                            {{-- ASSUMPTION: no sequential meeting-number column (DATA-05); derived from desc-ordered position --}}
                            <div class="flex size-11 shrink-0 items-center justify-center rounded-full border-2 font-semibold {{ $loop->first ? 'border-primary text-primary' : 'border-border text-secondary-foreground' }}">
                                {{ $meetings->count() - $loop->index }}
                            </div>
                            @if(! $loop->last)<div class="w-px grow bg-border"></div>@endif
                        </div>
                        <div class="kt-card {{ ! $loop->last ? 'mb-5' : '' }}">
                            <div class="kt-card-content grid gap-3 p-5">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <span class="font-medium">{{ $meeting->title }}</span>
                                    <span class="kt-badge bg-primary/10 text-primary border-0 gap-1.5">
                                        <i class="ki-filled ki-time text-sm"></i>{{ $meeting->scheduled_at->translatedFormat('d M Y H:i') }}
                                    </span>
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
                        </div>
                    </div>
                @empty
                    <p class="text-secondary-foreground text-sm">Belum ada pertemuan.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
