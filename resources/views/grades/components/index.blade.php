{{-- FR-GR-12 / BR-03 / NFR-08 / M6 --}}
@extends('layouts.app')
@section('breadcrumb')<x-breadcrumb :items="[['label' => 'Kelas', 'url' => route($routePrefix.'.classes.show', $class)], ['label' => $class->name], ['label' => 'Nilai']]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5">
    @include('guru.classes._tabs', ['class' => $class, 'routePrefix' => $routePrefix])
    <div class="flex items-center justify-between gap-3"><h1 class="text-xl font-medium text-mono">Komponen Nilai — {{ $class->name }}</h1><a href="{{ route($routePrefix.'.classes.recap', $class) }}" class="kt-btn kt-btn-outline kt-btn-primary"><i class="ki-filled ki-chart"></i>Rekap Nilai</a></div>
    @if(session('success'))<div data-kt-toast data-variant="success" data-message="{{ session('success') }}" style="display:none"></div>@endif
    @if(session('error'))<div data-kt-toast data-variant="destructive" data-message="{{ session('error') }}" style="display:none"></div>@endif
    @if($errors->any())<div data-kt-toast data-variant="destructive" data-message="Periksa kembali data yang diisi." style="display:none"></div>@endif
    @if($components->sum('weight') != 100)<div class="kt-alert kt-alert-info"><div class="kt-alert-icon"><i class="ki-filled ki-information-2 text-lg"></i></div><div class="kt-alert-title">Total bobot {{ $components->sum('weight') }}%. Nilai akhir dinormalisasi saat dihitung.</div></div>@endif
    <div class="kt-card"><div class="kt-card-header"><h3 class="kt-card-title text-sm">Tambah Komponen</h3></div><div class="kt-card-content">
        <form method="POST" action="{{ route($routePrefix.'.classes.grade-components.store', $class) }}" class="grid gap-3 md:grid-cols-4">@csrf
            <input name="name" class="kt-input @error('name') border-destructive @enderror" placeholder="Nama komponen" required>
            <input name="weight" type="number" min="0" max="100" step="0.01" class="kt-input @error('weight') border-destructive @enderror" placeholder="Bobot (%)" required>
            <select name="quiz_id" class="kt-select w-full" data-kt-select="true" data-kt-select-placeholder="Pilih sumber" data-kt-select-config='{"optionsClass":"kt-scrollable overflow-auto max-h-[250px]"}'><option value="">Input manual</option>@foreach($quizzes as $quiz)<option value="{{ $quiz->id }}">{{ $quiz->title }}</option>@endforeach</select>
            <button class="kt-btn kt-btn-primary"><i class="ki-filled ki-plus"></i>Tambah</button>
        </form>
        @error('name')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror @error('weight')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror @error('quiz_id')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror
    </div></div>
    <div class="kt-card"><div class="kt-card-content p-0"><table class="kt-table table-auto kt-table-border"><thead><tr><th>Komponen</th><th>Bobot</th><th>Sumber</th><th></th></tr></thead><tbody>
        @forelse($components as $component)<tr><td colspan="4"><div class="grid gap-2 md:grid-cols-[auto_1fr_1fr_1fr_auto] items-center p-2"><button type="button" class="kt-btn kt-btn-sm kt-btn-outline text-destructive hover:bg-destructive/10"
                            data-kt-modal-toggle="#confirm_status_modal"
                            data-action="{{ route($routePrefix.'.classes.grade-components.destroy', [$class, $component]) }}"
                            data-method="DELETE"
                            data-message="Hapus komponen {{ $component->name }}?"
                            data-label="Hapus"
                            data-variant="destructive">
                        <i class="ki-filled ki-trash"></i>Hapus
                    </button><form method="POST" action="{{ route($routePrefix.'.classes.grade-components.update', [$class, $component]) }}" class="contents">@csrf @method('PUT')<input name="name" value="{{ $component->name }}" class="kt-input" required><input name="weight" type="number" min="0" max="100" step="0.01" value="{{ $component->weight }}" class="kt-input" required>            <select name="quiz_id" class="kt-select w-full" data-kt-select="true" data-kt-select-placeholder="Pilih sumber" data-kt-select-config='{"optionsClass":"kt-scrollable overflow-auto max-h-[250px]"}'><option value="">Input manual</option>@foreach($quizzes as $quiz)<option value="{{ $quiz->id }}" @selected($component->quiz_id === $quiz->id)>{{ $quiz->title }}</option>@endforeach</select><div class="flex gap-2"><button class="kt-btn kt-btn-sm kt-btn-outline"><i class="ki-filled ki-check"></i>Simpan</button><a class="kt-btn kt-btn-sm kt-btn-outline" href="{{ route($routePrefix.'.classes.grade-components.scores', [$class, $component]) }}"><i class="ki-filled ki-notepad-edit"></i>Nilai</a></div></form></div></td></tr>
        @empty<tr><td colspan="4" class="py-8 text-center text-sm text-secondary-foreground">Belum ada komponen nilai.</td></tr>@endforelse
    </tbody></table></div></div>
</div>
<x-confirm-status-modal />
@endsection
