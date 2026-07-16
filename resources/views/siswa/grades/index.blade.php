{{-- FR-SW-06 / NFR-08 / M6 --}}
@extends('layouts.app')
@section('breadcrumb')<x-breadcrumb :items="[['label' => 'Nilai Saya']]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5"><h1 class="text-xl font-medium text-mono">Nilai Saya</h1><div class="kt-card"><div class="kt-card-content p-0"><table class="kt-table table-auto kt-table-border"><thead><tr><th>Kelas</th><th>Guru</th><th>Nilai Akhir</th><th>Dihitung</th></tr></thead><tbody>@forelse($grades as $grade)<tr><td>{{ $grade->schoolClass->name }}</td><td>{{ $grade->schoolClass->guru->name }}</td><td>{{ $grade->final_score }}</td><td>{{ $grade->calculated_at->translatedFormat('d M Y H:i') }}</td></tr>@empty<tr><td colspan="4" class="py-8 text-center text-sm text-secondary-foreground">Belum ada nilai akhir.</td></tr>@endforelse</tbody></table></div></div></div>
@endsection
