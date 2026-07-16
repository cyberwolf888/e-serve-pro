{{-- FR-SA-05 / NFR-08 / M6 --}}
@extends('layouts.app')
@section('breadcrumb')<x-breadcrumb :items="[['label' => 'Rekap Nilai']]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5"><div class="flex items-center justify-between"><h1 class="text-xl font-medium text-mono">Rekap Semua Kelas</h1><a href="{{ route('admin.recap.export') }}" class="kt-btn kt-btn-primary">Unduh XLSX</a></div><div class="kt-card"><div class="kt-card-content p-0"><table class="kt-table table-auto kt-table-border"><thead><tr><th>Kelas</th><th>Guru</th><th>Siswa</th><th></th></tr></thead><tbody>@forelse($classes as $class)<tr><td>{{ $class->name }}</td><td>{{ $class->guru->name }}</td><td>{{ $class->members->count() }}</td><td><a class="kt-btn kt-btn-sm kt-btn-outline" href="{{ route('admin.classes.recap', $class) }}">Lihat Rekap</a></td></tr>@empty<tr><td colspan="4" class="py-8 text-center text-sm text-secondary-foreground">Belum ada kelas.</td></tr>@endforelse</tbody></table></div></div></div>
@endsection
