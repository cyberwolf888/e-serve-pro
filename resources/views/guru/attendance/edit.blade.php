{{-- guru/attendance/edit.blade.php — FR-GR-07 / §9 / NFR-08 / M4 --}}
@extends('layouts.app')
@section('breadcrumb')<x-breadcrumb :items="[['label' => 'Kelas Saya', 'url' => route('guru.classes.index')], ['label' => $class->name, 'url' => route('guru.classes.show', $class)], ['label' => 'Pertemuan', 'url' => route('guru.classes.meetings.index', $class)], ['label' => $meeting->title, 'url' => route('guru.classes.meetings.show', [$class, $meeting])], ['label' => 'Absensi']]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5">
    <div class="flex items-center gap-3">
        <a href="{{ route('guru.classes.meetings.show', [$class, $meeting]) }}" class="kt-btn kt-btn-ghost kt-btn-icon"><i class="ki-filled ki-arrow-left text-lg"></i></a>
        <h1 class="text-xl font-semibold text-mono">Absensi — {{ $meeting->title }}</h1>
    </div>

    @if(session('success'))<div class="kt-alert kt-alert-success">{{ session('success') }}</div>@endif

    <div class="kt-card">
        <form method="POST" action="{{ route('guru.classes.meetings.attendance.store', [$class, $meeting]) }}">
            @csrf
            <div class="kt-card-content p-0">
                <table class="kt-table table-auto kt-table-border">
                    <thead>
                        <tr>
                            <th class="min-w-[220px]">Siswa</th>
                            <th class="min-w-[160px]">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $member)
                        @php($current = $existing->get($member->student_id)?->status)
                        <tr>
                            <td>{{ $member->student->name }}</td>
                            <td>
                                <select name="statuses[{{ $member->student_id }}]" class="kt-select w-full" data-kt-select="true">
                                    @foreach(['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alfa' => 'Alfa'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old("statuses.$member->student_id", $current) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="py-8 text-center text-sm text-secondary-foreground">Belum ada siswa di kelas ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($members->isNotEmpty())
            <div class="kt-card-footer flex justify-end">
                <button class="kt-btn kt-btn-primary"><i class="ki-filled ki-check"></i>Simpan Absensi</button>
            </div>
            @endif
        </form>
    </div>
</div>
@endsection
