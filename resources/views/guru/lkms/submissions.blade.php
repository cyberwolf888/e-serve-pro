{{-- DATA-27 / FR-SA-08 / FR-GR-11 / FR-GR-15 / NFR-08 / M7.9 --}}
@extends('layouts.app')
@php($indexLabel = $routePrefix === 'admin' ? 'Kelas' : 'Kelas Saya')
@section('breadcrumb')<x-breadcrumb :items="[['label' => $indexLabel, 'url' => route($routePrefix.'.classes.index')], ['label' => $class->name, 'url' => route($routePrefix.'.classes.show', $class)], ['label' => 'LKM', 'url' => route($routePrefix.'.classes.lkms.index', $class)], ['label' => $lkm->title, 'url' => route($routePrefix.'.classes.lkms.show', [$class, $lkm])], ['label' => 'Kiriman & Penilaian']]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5">
    @include('guru.classes._tabs', ['class' => $class, 'routePrefix' => $routePrefix])
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div><h1 class="text-xl font-semibold text-mono">Kiriman & Penilaian</h1><p class="text-sm text-secondary-foreground mt-1">{{ $lkm->title }}</p></div>
        <a href="{{ route($routePrefix.'.classes.lkms.show', [$class, $lkm]) }}" class="kt-btn kt-btn-outline"><i class="ki-filled ki-arrow-left"></i>Kembali</a>
    </div>
    @if(session('success'))<div class="kt-alert kt-alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="kt-alert kt-alert-destructive">{{ session('error') }}</div>@endif
    @error('score')<div class="kt-alert kt-alert-destructive">{{ $message }}</div>@enderror

    <div class="kt-card kt-card-grid min-w-full">
        <div class="kt-card-header py-5"><h2 class="kt-card-title">Kiriman Mahasiswa</h2></div>
        <div class="kt-card-content overflow-x-auto">
            <table class="kt-table table-auto kt-table-border min-w-[1100px]">
                <thead><tr><th class="min-w-[210px]">Mahasiswa</th><th class="min-w-[150px]">Peran</th><th class="min-w-[190px]">Bukti</th><th class="min-w-[260px]">Refleksi</th><th class="min-w-[150px]">Status</th><th class="min-w-[220px]">Nilai</th><th class="w-[110px]"></th></tr></thead>
                <tbody>
                @forelse($assignments as $assignment)
                    <tr>
                        <td><div class="font-medium text-mono">{{ $assignment->student->name }}</div><div class="text-xs text-secondary-foreground">{{ $assignment->student->email }}</div>@unless($assignment->student->is_active)<span class="kt-badge kt-badge-destructive kt-badge-outline mt-2">Tidak aktif</span>@endunless</td>
                        <td><span class="kt-badge kt-badge-outline">{{ $assignment->role->name }}</span></td>
                        <td><a href="{{ $assignment->proof_url }}" target="_blank" rel="noopener noreferrer" class="kt-btn kt-btn-sm kt-btn-outline"><i class="ki-filled ki-exit-up"></i>Buka bukti</a><div class="mt-2 max-w-[220px] truncate text-xs text-secondary-foreground" title="{{ $assignment->proof_url }}">{{ $assignment->proof_url }}</div></td>
                        <td>
                            @if($assignment->reflection_submitted_at)
                                <fieldset disabled class="grid gap-2">
                                    @foreach($assignment->role->sop_items as $index => $item)
                                        <label class="kt-label text-xs"><input class="kt-checkbox kt-checkbox-sm" type="checkbox" value="{{ $index }}" @checked(in_array($index, $assignment->sop_checks ?? []))>{{ $item }}</label>
                                    @endforeach
                                </fieldset>
                            @else
                                <span class="text-sm text-secondary-foreground">Belum mengirim refleksi.</span>
                            @endif
                        </td>
                        <td>@if($assignment->reflection_submitted_at)<span class="kt-badge kt-badge-success kt-badge-outline">Selesai</span>@else<span class="kt-badge kt-badge-warning kt-badge-outline">Menunggu refleksi</span>@endif</td>
                        <td>
                            @if($assignment->reflection_submitted_at)
                                @can('correctSubmission', $assignment)
                                    <form method="POST" action="{{ route($routePrefix.'.classes.lkms.submissions.grade', [$class, $lkm, $assignment]) }}" class="flex items-center gap-2">
                                        @csrf @method('PATCH')
                                        <input name="score" type="number" min="0" max="100" step="0.01" class="kt-input w-24" value="{{ $assignment->score }}" aria-label="Nilai {{ $assignment->student->name }}" required>
                                        <button class="kt-btn kt-btn-sm kt-btn-primary">Simpan</button>
                                    </form>
                                @else
                                    <span>{{ $assignment->score ?? '-' }}</span>
                                @endcan
                            @else
                                <span class="text-sm text-secondary-foreground">Belum dapat dinilai</span>
                            @endif
                        </td>
                        <td>@can('correctSubmission', $assignment)<a href="{{ route($routePrefix.'.classes.lkms.submissions.edit', [$class, $lkm, $assignment]) }}" class="kt-btn kt-btn-sm kt-btn-outline">Perbaiki</a>@endcan</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="py-8 text-center text-sm text-secondary-foreground">Belum ada mahasiswa yang mengirim bukti.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($assignments->hasPages())<div class="kt-card-footer">{{ $assignments->links('vendor.pagination.compact') }}</div>@endif
    </div>
</div>
@endsection
