{{-- siswa/classes/show.blade.php — FR-SW-04 / FR-SW-05 / FR-SW-08 / NFR-08 / M3 / M4 / M7.9 --}}
@extends('layouts.app')
@section('breadcrumb')<x-breadcrumb :items="[['label' => 'Kelas Saya', 'url' => route('siswa.classes.index')], ['label' => $class->name]]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5">
    <div class="kt-card">
        <div class="kt-card-content grid gap-3 p-7.5">
            <h1 class="text-2xl font-semibold text-mono">{{ $class->name }}</h1>
            <div class="flex items-center gap-1.5 text-secondary-foreground">
                <i class="ki-filled ki-user text-base"></i>
                <span>Dosen: {{ $class->guru->name }}</span>
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
                    <i class="ki-filled ki-book-open text-base text-primary"></i>Materi Terbit
                </h3>
            </div>
            <div class="kt-card-content grid gap-4 p-7.5">
                @forelse($materials as $material)
                    <div class="kt-card">
                        <div class="kt-card-content grid gap-3 p-5">
                            <span class="font-medium">{{ $material->title }}</span>
                            @if($material->description)<p class="text-sm text-secondary-foreground">{{ $material->description }}</p>@endif
                            <div>
                                @if($material->type === 'figma')
                                    <a href="{{ $material->figma_url }}" target="_blank" rel="noopener" class="kt-btn kt-btn-sm kt-btn-outline">
                                        <i class="ki-filled ki-share me-1"></i>Buka Materi
                                    </a>
                                @else
                                    <a href="{{ route('materials.download', $material) }}" class="kt-btn kt-btn-sm kt-btn-outline">
                                        <i class="ki-filled ki-file-down me-1"></i>Unduh Materi
                                    </a>
                                @endif
                            </div>
                        </div>
                        <x-material-discussions :school-class="$class" :material="$material" route-prefix="siswa" class="mx-5 mb-5" />
                    </div>
                @empty
                    <p class="text-secondary-foreground text-sm">Belum ada materi yang diterbitkan.</p>
                @endforelse
                <div class="border-t border-dashed border-input pt-4">
                    <a href="{{ route('siswa.classes.discussions.index', $class) }}" class="kt-btn kt-btn-sm kt-btn-outline">
                        <i class="ki-filled ki-message-text"></i>Diskusi Umum
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="kt-card">
        <div class="kt-card-header"><h3 class="kt-card-title flex items-center gap-2 text-xs font-semibold uppercase tracking-wide"><i class="ki-filled ki-people text-base text-primary"></i>LKM Saya</h3></div>
        <div class="kt-card-content grid sm:grid-cols-2 lg:grid-cols-3 gap-4 p-7.5">
            @forelse($lkmAssignments as $assignment)
                <div class="kt-card"><div class="kt-card-content grid gap-3 p-5"><div class="flex items-start justify-between gap-2"><h4 class="font-semibold">{{ $assignment->lkm->title }}</h4>@if($assignment->reflection_submitted_at)<span class="kt-badge kt-badge-success kt-badge-outline">Selesai</span>@elseif($assignment->proof_submitted_at)<span class="kt-badge kt-badge-warning kt-badge-outline">Refleksi</span>@else<span class="kt-badge kt-badge-outline">Belum mulai</span>@endif</div><p class="text-sm text-secondary-foreground">Peran: {{ $assignment->role->name }}</p><a href="{{ route('siswa.classes.lkms.show', [$class, $assignment->lkm]) }}" class="kt-btn kt-btn-sm kt-btn-outline">Buka LKM</a></div></div>
            @empty
                <p class="text-sm text-secondary-foreground">Belum ada LKM terbit yang ditugaskan.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
