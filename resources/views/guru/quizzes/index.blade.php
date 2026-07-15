{{-- guru/quizzes/index.blade.php — FR-GR-09 / NFR-08 / M5 --}}
@extends('layouts.app')
@section('breadcrumb')<x-breadcrumb :items="[['label' => 'Kelas Saya', 'url' => route('guru.classes.index')], ['label' => $class->name, 'url' => route('guru.classes.show', $class)], ['label' => 'Kuis']]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5">
    @include('guru.classes._tabs', ['class' => $class])
    <div class="flex items-center justify-between gap-3">
        <h1 class="text-xl font-medium text-mono">Kuis — {{ $class->name }}</h1>
        @if($class->is_active)
        <a href="{{ route('guru.classes.quizzes.create', $class) }}" class="kt-btn kt-btn-primary">
            <i class="ki-filled ki-plus me-1.5"></i>Tambah Kuis
        </a>
        @endif
    </div>
    @if(session('success'))<div class="kt-alert kt-alert-success">{{ session('success') }}</div>@endif
    <div class="kt-card">
        <div class="kt-card-content p-0">
            <table class="kt-table table-auto kt-table-border">
                <thead>
                    <tr>
                        <th class="min-w-[220px]">Judul</th>
                        <th class="min-w-[100px]">Status</th>
                        <th class="min-w-[80px]">Soal</th>
                        <th class="min-w-[100px]">Percobaan</th>
                        <th class="w-[100px]"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quizzes as $quiz)
                    <tr>
                        <td><a class="hover:text-primary" href="{{ route('guru.classes.quizzes.show', [$class, $quiz]) }}">{{ $quiz->title }}</a></td>
                        <td>
                            @if($quiz->is_published)
                                <span class="kt-badge kt-badge-success kt-badge-outline">Terbit</span>
                            @else
                                <span class="kt-badge kt-badge-outline">Draf</span>
                            @endif
                        </td>
                        <td>{{ $quiz->questions_count }}</td>
                        <td>{{ $quiz->attempts_count }}</td>
                        <td>
                            <div class="flex gap-1.5">
                                @if($class->is_active && ! $quiz->is_published && $quiz->attempts_count === 0)
                                <form method="POST" action="{{ route('guru.classes.quizzes.destroy', [$class, $quiz]) }}" onsubmit="return confirm('Hapus kuis {{ $quiz->title }}?')">
                                    @csrf @method('DELETE')
                                    <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost text-destructive"><i class="ki-filled ki-trash"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-8 text-center text-sm text-secondary-foreground">Belum ada kuis.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
