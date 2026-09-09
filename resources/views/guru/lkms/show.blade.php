{{-- DATA-27 / FR-SA-08 / FR-GR-11 / FR-GR-15 / BR-09 / NFR-08 / M7.9 --}}
@extends('layouts.app')
@php($indexLabel = $routePrefix === 'admin' ? 'Kelas' : 'Kelas Saya')
@section('breadcrumb')<x-breadcrumb :items="[['label' => $indexLabel, 'url' => route($routePrefix.'.classes.index')], ['label' => $class->name, 'url' => route($routePrefix.'.classes.show', $class)], ['label' => 'LKM', 'url' => route($routePrefix.'.classes.lkms.index', $class)], ['label' => $lkm->title]]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5">
    @include('guru.classes._tabs', ['class' => $class, 'routePrefix' => $routePrefix])
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div><h1 class="text-xl font-semibold text-mono">{{ $lkm->title }}</h1><p class="text-sm text-secondary-foreground mt-1">{{ $lkm->description }}</p></div>
        <div class="flex flex-wrap items-center gap-2"><span class="kt-badge {{ $lkm->is_published ? 'kt-badge-success' : '' }} kt-badge-outline">{{ $lkm->is_published ? 'Terbit' : 'Draf' }}</span><a href="{{ route($routePrefix.'.classes.lkms.submissions.index', [$class, $lkm]) }}" class="kt-btn kt-btn-outline"><i class="ki-filled ki-check-square"></i>Kiriman & Penilaian</a>@can('update', $lkm)<a href="{{ route($routePrefix.'.classes.lkms.edit', [$class, $lkm]) }}" class="kt-btn kt-btn-outline"><i class="ki-filled ki-pencil"></i>Ubah</a>@endcan</div>
    </div>
    @if(session('success'))<div class="kt-alert kt-alert-success">{{ session('success') }}</div>@endif
    @foreach(['role', 'student_id', 'lkm_role_id', 'proof_url', 'sop_checks', 'score'] as $field)@error($field)<div class="kt-alert kt-alert-destructive">{{ $message }}</div>@enderror @endforeach

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($roles as $role)
            <div class="kt-card"><div class="kt-card-content grid gap-2 p-5"><div class="flex items-center justify-between gap-2"><h2 class="font-semibold">{{ $role->name }}</h2><span class="kt-badge kt-badge-outline">{{ $role->assignments_count }} mahasiswa</span></div><p class="text-sm text-secondary-foreground">{{ $role->description }}</p><ol class="list-decimal ps-5 text-sm">@foreach($role->sop_items as $item)<li>{{ $item }}</li>@endforeach</ol></div></div>
        @endforeach
    </div>

    <div class="kt-card">
        <div class="kt-card-header"><h2 class="kt-card-title text-sm">Pembagian Peran Mahasiswa</h2></div>
        <div class="kt-card-content p-0 overflow-x-auto">
            <table class="kt-table table-auto kt-table-border">
                <thead><tr><th class="min-w-[220px]">Mahasiswa</th><th class="min-w-[220px]">Peran</th><th class="min-w-[150px]">Status</th><th class="w-[100px]">Nilai</th><th class="w-[130px]"></th></tr></thead>
                <tbody>
                @forelse($members as $member)
                    @php($assignment = $assignments->get($member->student_id))
                    <tr>
                        <td><div class="font-medium">{{ $member->student->name }}</div><div class="text-xs text-secondary-foreground">{{ $member->student->email }}</div></td>
                        <td>
                            @if($assignment)
                                @can('reassign', $assignment)
                                    <form method="POST" action="{{ route($routePrefix.'.classes.lkms.assignments.update', [$class, $lkm, $assignment]) }}" class="flex gap-2">
                                        @csrf @method('PATCH')
                                        <select name="lkm_role_id" class="kt-select" required>@foreach($roles as $role)<option value="{{ $role->id }}" @selected($assignment->lkm_role_id === $role->id)>{{ $role->name }}</option>@endforeach</select>
                                        <button class="kt-btn kt-btn-sm kt-btn-outline">Simpan</button>
                                    </form>
                                @else
                                    <span>{{ $assignment->role->name }}</span>
                                @endcan
                            @else
                                @can('assign', $lkm)
                                    <form method="POST" action="{{ route($routePrefix.'.classes.lkms.assignments.store', [$class, $lkm]) }}" class="flex gap-2">
                                        @csrf<input type="hidden" name="student_id" value="{{ $member->student_id }}">
                                        <select name="lkm_role_id" class="kt-select" required><option value="">Pilih peran</option>@foreach($roles as $role)<option value="{{ $role->id }}">{{ $role->name }}</option>@endforeach</select>
                                        <button class="kt-btn kt-btn-sm kt-btn-outline">Tetapkan</button>
                                    </form>
                                @endcan
                            @endif
                        </td>
                        <td>
                            @if(! $assignment)<span class="kt-badge kt-badge-outline">Belum ditetapkan</span>
                            @elseif($assignment->reflection_submitted_at)<span class="kt-badge kt-badge-success kt-badge-outline">Selesai</span>
                            @elseif($assignment->proof_submitted_at)<span class="kt-badge kt-badge-warning kt-badge-outline">Menunggu refleksi</span>
                            @else<span class="kt-badge kt-badge-outline">Belum mengirim</span>@endif
                        </td>
                        <td>{{ $assignment?->score ?? '-' }}</td>
                        <td>@if($assignment?->proof_submitted_at)@can('correctSubmission', $assignment)<a href="{{ route($routePrefix.'.classes.lkms.submissions.edit', [$class, $lkm, $assignment]) }}" class="kt-btn kt-btn-sm kt-btn-outline">Perbaiki</a>@endcan @endif</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-8 text-center text-sm text-secondary-foreground">Belum ada mahasiswa di kelas.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($members->hasPages())<div class="kt-card-footer">{{ $members->links('vendor.pagination.compact') }}</div>@endif
    </div>
</div>
@endsection
