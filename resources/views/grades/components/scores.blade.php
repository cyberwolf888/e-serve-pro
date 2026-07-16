{{-- DATA-15 / FR-GR-11 / NFR-08 / M6 --}}
@extends('layouts.app')
@section('breadcrumb')<x-breadcrumb :items="[['label' => 'Nilai', 'url' => route($routePrefix.'.classes.grade-components.index', $class)], ['label' => $component->name]]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5">
    <div class="flex items-center justify-between"><h1 class="text-xl font-medium text-mono">Nilai {{ $component->name }}</h1><a href="{{ route($routePrefix.'.classes.grade-components.index', $class) }}" class="kt-btn kt-btn-outline">Kembali</a></div>
    @if(session('success'))<div class="kt-alert kt-alert-success">{{ session('success') }}</div>@endif
    <div class="kt-card"><form method="POST" action="{{ route($routePrefix.'.classes.grade-components.scores.store', [$class, $component]) }}">@csrf<div class="kt-card-content p-0"><table class="kt-table table-auto kt-table-border"><thead><tr><th>Siswa</th><th>Nilai</th><th>Status</th></tr></thead><tbody>
        @forelse($members as $member)@php($score = $component->scores->firstWhere('student_id', $member->student_id))<tr><td>{{ $member->student->name }}</td><td><input name="scores[{{ $member->student_id }}]" type="number" min="0" max="100" step="0.01" value="{{ old('scores.'.$member->student_id, $score?->score) }}" class="kt-input w-28"></td><td>{{ $score?->is_manual_override ? 'Manual' : ($score ? 'Otomatis' : 'Belum ada') }}</td></tr>@empty<tr><td colspan="3" class="py-8 text-center text-sm text-secondary-foreground">Belum ada siswa.</td></tr>@endforelse
    </tbody></table></div><div class="kt-card-footer justify-end"><button class="kt-btn kt-btn-primary">Simpan Nilai</button></div></form></div>
</div>
@endsection
