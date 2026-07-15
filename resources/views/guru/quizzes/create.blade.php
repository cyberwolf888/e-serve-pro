{{-- guru/quizzes/create.blade.php — FR-GR-09 / NFR-08 / M5 --}}
@extends('layouts.app')
@section('breadcrumb')<x-breadcrumb :items="[['label' => 'Kelas Saya', 'url' => route('guru.classes.index')], ['label' => $class->name, 'url' => route('guru.classes.edit', $class)], ['label' => 'Kuis', 'url' => route('guru.classes.quizzes.index', $class)], ['label' => 'Tambah Kuis']]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5 py-6 xl:w-[38.75rem] mx-auto">
    <div class="flex items-center gap-3"><a href="{{ route('guru.classes.quizzes.index', $class) }}" class="kt-btn kt-btn-ghost kt-btn-icon"><i class="ki-filled ki-arrow-left text-lg"></i></a><h1 class="text-xl font-semibold text-mono">Tambah Kuis</h1></div>
    <div class="kt-card">
        <form method="POST" action="{{ route('guru.classes.quizzes.store', $class) }}">
            @csrf
            <div class="kt-card-content grid gap-5 p-7.5">
                @include('guru.quizzes._form', ['quiz' => null])
            </div>
            <div class="kt-card-footer flex justify-end gap-2.5">
                <a href="{{ route('guru.classes.quizzes.index', $class) }}" class="kt-btn kt-btn-outline"><i class="ki-filled ki-cross"></i>Batal</a>
                <button class="kt-btn kt-btn-primary"><i class="ki-filled ki-check"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
