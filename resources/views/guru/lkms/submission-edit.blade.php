{{-- DATA-27 / FR-SA-08 / FR-GR-11 / FR-GR-15 / BR-09 / §9 / NFR-08 / M7.9 --}}
@extends('layouts.app')
@php($indexLabel = $routePrefix === 'admin' ? 'Kelas' : 'Kelas Saya')
@section('breadcrumb')<x-breadcrumb :items="[['label' => $indexLabel, 'url' => route($routePrefix.'.classes.index')], ['label' => $class->name, 'url' => route($routePrefix.'.classes.show', $class)], ['label' => 'LKM', 'url' => route($routePrefix.'.classes.lkms.index', $class)], ['label' => $lkm->title, 'url' => route($routePrefix.'.classes.lkms.show', [$class, $lkm])], ['label' => 'Perbaiki Kiriman']]" />@endsection
@section('content')
<div class="grid gap-5 py-6 xl:w-[38.75rem] mx-auto">
    <div class="flex items-center gap-3"><a href="{{ route($routePrefix.'.classes.lkms.show', [$class, $lkm]) }}" class="kt-btn kt-btn-ghost kt-btn-icon"><i class="ki-filled ki-arrow-left text-lg"></i></a><div><h1 class="text-xl font-semibold text-mono">Perbaiki Kiriman</h1><p class="text-sm text-secondary-foreground">{{ $assignment->student->name }} — {{ $assignment->role->name }}</p></div></div>
    <div class="kt-alert kt-alert-warning">Waktu kirim asli tetap tersimpan. Mahasiswa tetap tidak dapat mengubah kiriman.</div>
    <div class="kt-card">
        <form method="POST" action="{{ route($routePrefix.'.classes.lkms.submissions.update', [$class, $lkm, $assignment]) }}">
            @csrf @method('PUT')
            <div class="kt-card-content grid gap-5 p-7.5">
                <div><label class="kt-form-label" for="proof_url">Tautan Bukti</label><input id="proof_url" name="proof_url" type="url" class="kt-input w-full" value="{{ old('proof_url', $assignment->proof_url) }}" required />@error('proof_url')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror</div>
                @if($assignment->reflection_submitted_at)
                    <fieldset class="grid gap-3"><legend class="font-medium">Refleksi Diri/Self Assessment</legend>@foreach($assignment->role->sop_items as $index => $item)<label class="kt-label"><input class="kt-checkbox" type="checkbox" name="sop_checks[]" value="{{ $index }}" @checked(in_array($index, old('sop_checks', $assignment->sop_checks ?? [])))>{{ $item }}</label>@endforeach</fieldset>
                    @error('sop_checks')<p class="text-destructive text-xs">{{ $message }}</p>@enderror
                    <div><label class="kt-form-label" for="score">Nilai LKM</label><input id="score" name="score" type="number" min="0" max="100" step="0.01" class="kt-input w-full" value="{{ old('score', $assignment->score) }}" placeholder="0-100" />@error('score')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror</div>
                @endif
            </div>
            <div class="kt-card-footer justify-end"><button class="kt-btn kt-btn-primary"><i class="ki-filled ki-check"></i>Simpan Perbaikan</button></div>
        </form>
    </div>
</div>
@endsection
