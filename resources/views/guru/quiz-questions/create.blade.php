{{-- guru/quiz-questions/create.blade.php — FR-GR-09 / FR-SA-03 / NFR-08 / M5 / ADMIN_CLASS_ACCESS_PLAN --}}
@extends('layouts.app')
@php($indexLabel = $routePrefix === 'admin' ? 'Kelas' : 'Kelas Saya')
@section('breadcrumb')<x-breadcrumb :items="[['label' => $indexLabel, 'url' => route($routePrefix.'.classes.index')], ['label' => $class->name, 'url' => route($routePrefix.'.classes.show', $class)], ['label' => 'Kuis', 'url' => route($routePrefix.'.classes.quizzes.index', $class)], ['label' => $quiz->title, 'url' => route($routePrefix.'.classes.quizzes.show', [$class, $quiz])], ['label' => 'Tambah Soal']]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5 py-6 xl:w-[46rem] mx-auto">
    <div class="flex items-center gap-3"><a href="{{ route($routePrefix.'.classes.quizzes.show', [$class, $quiz]) }}" class="kt-btn kt-btn-ghost kt-btn-icon"><i class="ki-filled ki-arrow-left text-lg"></i></a><h1 class="text-xl font-semibold text-mono">Tambah Soal</h1></div>
    <div class="kt-card">
        <form method="POST" action="{{ route($routePrefix.'.classes.quizzes.questions.store', [$class, $quiz]) }}">
            @csrf
            <div class="kt-card-content grid gap-5 p-7.5">
                @include('guru.quiz-questions._form', ['question' => null])
            </div>
            <div class="kt-card-footer flex justify-end gap-2.5">
                <a href="{{ route($routePrefix.'.classes.quizzes.show', [$class, $quiz]) }}" class="kt-btn kt-btn-outline"><i class="ki-filled ki-cross"></i>Batal</a>
                <button class="kt-btn kt-btn-primary"><i class="ki-filled ki-check"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
