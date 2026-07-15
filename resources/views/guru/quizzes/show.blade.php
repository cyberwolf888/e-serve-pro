{{-- guru/quizzes/show.blade.php — FR-GR-09 / NFR-08 / M5 --}}
@extends('layouts.app')
@section('breadcrumb')<x-breadcrumb :items="[['label' => 'Kelas Saya', 'url' => route('guru.classes.index')], ['label' => $class->name, 'url' => route('guru.classes.show', $class)], ['label' => 'Kuis', 'url' => route('guru.classes.quizzes.index', $class)], ['label' => $quiz->title]]" />@endsection
@section('content')
@php($locked = $quiz->is_published || $quiz->attempts_count > 0)
<div class="grid gap-5 lg:gap-7.5 py-6 xl:w-[46rem] mx-auto">
    <div class="flex items-center gap-3">
        <a href="{{ route('guru.classes.quizzes.index', $class) }}" class="kt-btn kt-btn-ghost kt-btn-icon"><i class="ki-filled ki-arrow-left text-lg"></i></a>
        <h1 class="text-xl font-semibold text-mono grow">{{ $quiz->title }}</h1>
        @if($quiz->is_published)
            <span class="kt-badge kt-badge-success kt-badge-outline">Terbit</span>
        @else
            <span class="kt-badge kt-badge-outline">Draf</span>
        @endif
    </div>

    @if(session('success'))<div class="kt-alert kt-alert-success">{{ session('success') }}</div>@endif
    @error('quiz')<div class="kt-alert kt-alert-destructive">{{ $message }}</div>@enderror

    <div class="kt-card">
        <div class="kt-card-content grid gap-3 p-7.5">
            @if($quiz->description)<p>{{ $quiz->description }}</p>@endif
            <p class="text-secondary-foreground text-sm">
                Dibuka: {{ $quiz->opens_at?->translatedFormat('d M Y H:i') ?? 'Segera setelah dipublikasikan' }} &middot;
                Ditutup: {{ $quiz->closes_at?->translatedFormat('d M Y H:i') ?? 'Tanpa batas waktu' }}
            </p>
            @if($class->is_active)
            <div class="flex gap-2.5">
                <a href="{{ route('guru.classes.quizzes.edit', [$class, $quiz]) }}" class="kt-btn kt-btn-outline"><i class="ki-filled ki-pencil me-1.5"></i>Ubah Detail</a>
                @if($quiz->is_published)
                <form method="POST" action="{{ route('guru.classes.quizzes.unpublish', [$class, $quiz]) }}">
                    @csrf @method('PATCH')
                    <button class="kt-btn kt-btn-outline">Batalkan Publikasi</button>
                </form>
                @else
                <form method="POST" action="{{ route('guru.classes.quizzes.publish', [$class, $quiz]) }}">
                    @csrf @method('PATCH')
                    <button class="kt-btn kt-btn-primary">Publikasikan</button>
                </form>
                @endif
            </div>
            @endif
        </div>
    </div>

    <div class="kt-card">
        <div class="kt-card-header flex items-center justify-between">
            <h3 class="kt-card-title text-sm">Soal</h3>
            @if($class->is_active && ! $locked)
            <a href="{{ route('guru.classes.quizzes.questions.create', [$class, $quiz]) }}" class="kt-btn kt-btn-sm kt-btn-primary">
                <i class="ki-filled ki-plus me-1"></i>Tambah Soal
            </a>
            @endif
        </div>
        <div class="kt-card-content grid gap-5 p-7.5">
            @if($locked)
                <p class="text-secondary-foreground text-xs">Kuis terkunci — batalkan publikasi untuk mengubah soal. Soal yang sudah pernah dikerjakan tidak dapat diubah.</p>
            @endif
            @forelse($quiz->questions as $index => $question)
                <div class="grid gap-2">
                    <div class="flex items-start justify-between gap-3">
                        <p class="font-medium">{{ $index + 1 }}. {{ $question->question_text }}</p>
                        @if($class->is_active && ! $locked)
                        <div class="flex gap-1.5 shrink-0">
                            <a href="{{ route('guru.classes.quizzes.questions.edit', [$class, $quiz, $question]) }}" class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost"><i class="ki-filled ki-pencil"></i></a>
                            <form method="POST" action="{{ route('guru.classes.quizzes.questions.destroy', [$class, $quiz, $question]) }}" onsubmit="return confirm('Hapus soal ini?')">
                                @csrf @method('DELETE')
                                <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost text-destructive"><i class="ki-filled ki-trash"></i></button>
                            </form>
                        </div>
                        @endif
                    </div>
                    <ul class="grid gap-1 ps-4 text-sm">
                        @foreach($question->options as $option)
                            <li class="{{ $option->is_correct ? 'text-success font-medium' : 'text-secondary-foreground' }}">
                                {{ $option->label }}. {{ $option->option_text }} @if($option->is_correct)<i class="ki-filled ki-check-circle text-xs"></i>@endif
                            </li>
                        @endforeach
                    </ul>
                </div>
                @if(! $loop->last)<div class="border-b border-border"></div>@endif
            @empty
                <p class="text-secondary-foreground text-sm">Belum ada soal.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
