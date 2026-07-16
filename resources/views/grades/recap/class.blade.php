{{-- FR-GR-10 / FR-SA-05 / NFR-08 / M6 --}}
@extends('layouts.app')
@section('breadcrumb')<x-breadcrumb :items="[['label' => 'Kelas', 'url' => route($routePrefix.'.classes.show', $class)], ['label' => $class->name], ['label' => 'Rekap']]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5">
    @if($routePrefix === 'guru')@include('guru.classes._tabs', ['class' => $class])@endif
    <div class="flex items-center justify-between gap-3"><h1 class="text-xl font-medium text-mono">Rekap Nilai — {{ $class->name }}</h1><div class="flex gap-2"><a href="{{ route($routePrefix.'.classes.recap.export', $class) }}" class="kt-btn kt-btn-outline">Unduh XLSX</a>@if($class->is_active)<form method="POST" action="{{ route($routePrefix.'.classes.grades.calculate', $class) }}">@csrf<button class="kt-btn kt-btn-primary">Hitung Nilai Akhir</button></form>@endif</div></div>
    @if(session('success'))<div class="kt-alert kt-alert-success">{{ session('success') }}</div>@endif @error('grades')<div class="kt-alert kt-alert-danger">{{ $message }}</div>@enderror
    <div class="kt-card"><div class="kt-card-content p-0 kt-scrollable-x-auto"><table class="kt-table table-auto kt-table-border"><thead><tr><th>Siswa</th>@foreach($class->gradeComponents as $component)<th>{{ $component->name }}<br><span class="font-normal">{{ $component->weight }}%</span></th>@endforeach<th>Nilai Akhir</th></tr></thead><tbody>
    @forelse($class->members as $member)<tr><td>{{ $member->student->name }}@if(!$member->student->is_active)<span class="text-secondary-foreground"> (nonaktif)</span>@endif</td>@foreach($class->gradeComponents as $component)<td>{{ $component->scores->firstWhere('student_id', $member->student_id)?->score ?? '—' }}</td>@endforeach<td>{{ $class->finalGrades->firstWhere('student_id', $member->student_id)?->final_score ?? '—' }}</td></tr>@empty<tr><td colspan="{{ $class->gradeComponents->count() + 2 }}" class="py-8 text-center text-sm text-secondary-foreground">Belum ada siswa.</td></tr>@endforelse
    </tbody></table></div></div>
</div>
@endsection
