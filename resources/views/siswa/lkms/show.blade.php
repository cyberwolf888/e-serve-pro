{{-- FR-SW-08 / BR-09 / §9 / NFR-08 / M7.9 --}}
@extends('layouts.app')
@section('breadcrumb')<x-breadcrumb :items="[['label' => 'Kelas Saya', 'url' => route('siswa.classes.index')], ['label' => $class->name, 'url' => route('siswa.classes.show', $class)], ['label' => $lkm->title]]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5 py-6 xl:w-[46rem] mx-auto">
    <div class="flex items-center gap-3"><a href="{{ route('siswa.classes.show', $class) }}" class="kt-btn kt-btn-ghost kt-btn-icon"><i class="ki-filled ki-arrow-left text-lg"></i></a><div><h1 class="text-xl font-semibold text-mono">{{ $lkm->title }}</h1><p class="text-sm text-secondary-foreground">Peran: {{ $assignment->role->name }}</p></div></div>
    @if(session('success'))<div class="kt-alert kt-alert-success">{{ session('success') }}</div>@endif
    <div class="kt-card"><div class="kt-card-content grid gap-4 p-7.5"><p>{{ $lkm->description }}</p><div><h2 class="font-semibold">Deskripsi Peran</h2><p class="text-sm mt-1">{{ $assignment->role->description }}</p></div><div><h2 class="font-semibold">Instruksi</h2><p class="text-sm mt-1 whitespace-pre-line">{{ $assignment->role->instructions }}</p></div></div></div>

    @if(! $assignment->proof_submitted_at)
        <div class="kt-alert kt-alert-warning">Bukti hanya dapat dikirim satu kali. Pastikan tautan HTTPS dapat dibuka sebelum mengirim.</div>
        @can('submitProof', $lkm)
            <div class="kt-card"><form method="POST" action="{{ route('siswa.classes.lkms.proof.store', [$class, $lkm]) }}">@csrf<div class="kt-card-content grid gap-3 p-7.5"><label class="kt-form-label" for="proof_url">Tautan Bukti Google Drive/Docs atau YouTube</label><input id="proof_url" name="proof_url" type="url" class="kt-input w-full" value="{{ old('proof_url') }}" placeholder="https://drive.google.com/..." required />@error('proof_url')<p class="text-destructive text-xs">{{ $message }}</p>@enderror</div><div class="kt-card-footer justify-end"><button class="kt-btn kt-btn-primary">Kirim Bukti</button></div></form></div>
        @endcan
    @else
        <div class="kt-card"><div class="kt-card-content grid gap-2 p-5"><span class="text-xs uppercase tracking-wide text-secondary-foreground">Bukti Terkirim</span><a href="{{ $assignment->proof_url }}" target="_blank" rel="noopener" class="text-primary break-all">{{ $assignment->proof_url }}</a><span class="text-xs text-secondary-foreground">{{ $assignment->proof_submitted_at->translatedFormat('d M Y H:i') }}</span></div></div>
    @endif

    @if($assignment->proof_submitted_at && ! $assignment->reflection_submitted_at)
        @can('submitReflection', $lkm)
            <div class="kt-card"><form method="POST" action="{{ route('siswa.classes.lkms.reflection.store', [$class, $lkm]) }}">@csrf<div class="kt-card-content grid gap-4 p-7.5"><div><h2 class="font-semibold">Refleksi Diri/Self Assessment</h2><p class="text-sm text-secondary-foreground">Centang langkah yang sudah dilakukan. Tidak mencentang apa pun tetap diperbolehkan.</p></div><fieldset class="grid gap-3">@foreach($assignment->role->sop_items as $index => $item)<label class="kt-label"><input class="kt-checkbox" type="checkbox" name="sop_checks[]" value="{{ $index }}" @checked(in_array($index, old('sop_checks', [])))>{{ $item }}</label>@endforeach</fieldset>@error('sop_checks')<p class="text-destructive text-xs">{{ $message }}</p>@enderror</div><div class="kt-card-footer justify-end"><button class="kt-btn kt-btn-primary">Kirim Refleksi</button></div></form></div>
        @endcan
    @elseif($assignment->reflection_submitted_at)
        <div class="kt-card"><div class="kt-card-header"><h2 class="kt-card-title text-sm">Refleksi Terkirim</h2></div><div class="kt-card-content grid gap-3 p-5">@foreach($assignment->role->sop_items as $index => $item)<div class="flex items-center gap-2"><i class="ki-filled {{ in_array($index, $assignment->sop_checks ?? []) ? 'ki-check-circle text-success' : 'ki-cross-circle text-muted-foreground' }}"></i><span>{{ $item }}</span></div>@endforeach</div></div>
    @endif
</div>
@endsection
