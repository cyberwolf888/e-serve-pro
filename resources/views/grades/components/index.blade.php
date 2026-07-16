{{-- FR-GR-12 / BR-03 / NFR-08 / M6 --}}
@extends('layouts.app')
@section('breadcrumb')<x-breadcrumb :items="[['label' => 'Kelas', 'url' => route($routePrefix.'.classes.show', $class)], ['label' => $class->name], ['label' => 'Nilai']]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5">
    @if($routePrefix === 'guru')@include('guru.classes._tabs', ['class' => $class])@endif
    <div class="flex items-center justify-between gap-3"><h1 class="text-xl font-medium text-mono">Komponen Nilai — {{ $class->name }}</h1><a href="{{ route($routePrefix.'.classes.recap', $class) }}" class="kt-btn kt-btn-outline kt-btn-primary">Rekap Nilai</a></div>
    @if(session('success'))<div class="kt-alert kt-alert-success">{{ session('success') }}</div>@endif
    @if($components->sum('weight') != 100)<div class="kt-alert kt-alert-warning">Total bobot {{ $components->sum('weight') }}%. Nilai akhir dinormalisasi saat dihitung.</div>@endif
    <div class="kt-card"><div class="kt-card-header"><h3 class="kt-card-title text-sm">Tambah Komponen</h3></div><div class="kt-card-content">
        <form method="POST" action="{{ route($routePrefix.'.classes.grade-components.store', $class) }}" class="grid gap-3 md:grid-cols-4">@csrf
            <input name="name" class="kt-input @error('name') border-destructive @enderror" placeholder="Nama komponen" required>
            <input name="weight" type="number" min="0" max="100" step="0.01" class="kt-input @error('weight') border-destructive @enderror" placeholder="Bobot (%)" required>
            <select name="quiz_id" class="kt-select"><option value="">Input manual</option>@foreach($quizzes as $quiz)<option value="{{ $quiz->id }}">{{ $quiz->title }}</option>@endforeach</select>
            <button class="kt-btn kt-btn-primary">Tambah</button>
        </form>
        @error('name')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror @error('weight')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror @error('quiz_id')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror
    </div></div>
    <div class="kt-card"><div class="kt-card-content p-0"><table class="kt-table table-auto kt-table-border"><thead><tr><th>Komponen</th><th>Bobot</th><th>Sumber</th><th></th></tr></thead><tbody>
        @forelse($components as $component)<tr><td colspan="4"><form method="POST" action="{{ route($routePrefix.'.classes.grade-components.update', [$class, $component]) }}" class="grid gap-2 md:grid-cols-5 items-center">@csrf @method('PUT')<input name="name" value="{{ $component->name }}" class="kt-input" required><input name="weight" type="number" min="0" max="100" step="0.01" value="{{ $component->weight }}" class="kt-input" required><select name="quiz_id" class="kt-select"><option value="">Input manual</option>@foreach($quizzes as $quiz)<option value="{{ $quiz->id }}" @selected($component->quiz_id === $quiz->id)>{{ $quiz->title }}</option>@endforeach</select><div class="flex gap-2"><button class="kt-btn kt-btn-sm kt-btn-outline">Simpan</button><a class="kt-btn kt-btn-sm kt-btn-outline" href="{{ route($routePrefix.'.classes.grade-components.scores', [$class, $component]) }}">Nilai</a></div></form><form method="POST" action="{{ route($routePrefix.'.classes.grade-components.destroy', [$class, $component]) }}" class="mt-2" onsubmit="return confirm('Hapus komponen ini?')">@csrf @method('DELETE')<button class="kt-btn kt-btn-sm kt-btn-destructive">Hapus</button></form></td></tr>
        @empty<tr><td colspan="4" class="py-8 text-center text-sm text-secondary-foreground">Belum ada komponen nilai.</td></tr>@endforelse
    </tbody></table></div></div>
</div>
@endsection
