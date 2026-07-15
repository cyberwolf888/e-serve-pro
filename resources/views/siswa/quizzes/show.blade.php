{{-- siswa/quizzes/show.blade.php — FR-SW-05 / NFR-08 / M5 --}}
@extends('layouts.app')
@section('breadcrumb')<x-breadcrumb :items="[['label' => 'Kelas Saya', 'url' => route('siswa.classes.index')], ['label' => $quiz->schoolClass->name, 'url' => route('siswa.classes.show', $quiz->class_id)], ['label' => $quiz->title]]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5 py-6 xl:w-[46rem] mx-auto">
    <h1 class="text-xl font-semibold text-mono">{{ $quiz->title }}</h1>
    @if($quiz->description)<p class="text-secondary-foreground">{{ $quiz->description }}</p>@endif

    @error('answers')<div class="kt-alert kt-alert-destructive">{{ $message }}</div>@enderror

    <form method="POST" action="{{ route('siswa.quizzes.submit', $quiz) }}">
        @csrf
        <div class="grid gap-5">
            @foreach($quiz->questions as $index => $question)
                <div class="kt-card">
                    <div class="kt-card-content grid gap-3 p-7.5">
                        <p class="font-medium">{{ $index + 1 }}. {{ $question->question_text }}</p>
                        <div class="grid gap-2">
                            @foreach($question->options as $option)
                                <label class="kt-form-label flex items-center gap-2.5">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" class="kt-radio" required />
                                    <span>{{ $option->label }}. {{ $option->option_text }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="flex justify-end pt-5">
            <button class="kt-btn kt-btn-primary" onclick="return confirm('Kirim jawaban? Kuis hanya dapat dikerjakan satu kali.')">
                <i class="ki-filled ki-check me-1.5"></i>Kirim Jawaban
            </button>
        </div>
    </form>
</div>
@endsection
