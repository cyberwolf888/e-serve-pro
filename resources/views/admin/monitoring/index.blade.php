{{-- admin/monitoring/index.blade.php — FR-SA-04 / NFR-02 / M7 --}}
@extends('layouts.app')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Monitoring Aktivitas'],
    ]" />
@endsection

@section('content')
<div class="grid gap-5 lg:gap-7.5">

    {{-- Page header --}}
    <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-2">
        <div class="flex flex-col justify-center gap-2">
            <h1 class="text-xl font-medium leading-none text-mono">Monitoring Aktivitas</h1>
            <div class="flex items-center flex-wrap gap-1.5 font-medium">
                <span class="text-base text-secondary-foreground">Total Log:</span>
                <span class="text-base text-foreground font-medium">{{ $logs->total() }}</span>
            </div>
        </div>
    </div>

    {{-- Filter panel --}}
    <div class="kt-card">
        <div class="kt-card-header">
            <h3 class="kt-card-title text-sm">Filter Log</h3>
        </div>
        <div class="kt-card-content">
            <form class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" method="GET">
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium text-secondary-foreground" for="user_id">Pengguna</label>
                    <select class="kt-select" data-kt-select="true" data-kt-select-placeholder="Semua Pengguna" id="user_id" name="user_id">
                        <option value="">Semua Pengguna</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>
                                {{ $u->name }} ({{ $u->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium text-secondary-foreground" for="event_type">Tipe Event</label>
                    <select class="kt-select" data-kt-select="true" data-kt-select-placeholder="Semua Event" id="event_type" name="event_type">
                        <option value="">Semua Event</option>
                        @foreach ($eventTypes as $type)
                            <option value="{{ $type }}" @selected(request('event_type') === $type)>
                                {{ match ($type) {
                                    'login' => 'Login',
                                    'logout' => 'Logout',
                                    'quiz_attempt' => 'Percobaan Kuis',
                                    'attendance' => 'Absensi',
                                    default => 'Lainnya',
                                } }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium text-secondary-foreground" for="date_from">Dari Tanggal</label>
                    <input class="kt-input" id="date_from" name="date_from" type="date" value="{{ request('date_from') }}" />
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium text-secondary-foreground" for="date_to">Sampai Tanggal</label>
                    <input class="kt-input" id="date_to" name="date_to" type="date" value="{{ request('date_to') }}" />
                </div>

                <div class="flex items-end gap-2 lg:col-span-4">
                    <button class="kt-btn kt-btn-primary" type="submit">
                        <i class="ki-filled ki-magnifier me-1.5"></i>
                        Terapkan
                    </button>
                    <a class="kt-btn kt-btn-outline kt-btn-secondary" href="{{ route('admin.monitoring') }}">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Logs table --}}
    <div class="kt-card kt-card-grid min-w-full">
        <div class="kt-card-header">
            <h3 class="kt-card-title text-sm">Daftar Aktivitas</h3>
        </div>
        <div class="kt-card-content">
            <div class="kt-scrollable-x-auto">
                <table class="kt-table table-auto kt-table-border">
                    <thead>
                        <tr>
                            <th class="min-w-[170px]">
                                <span class="kt-table-col"><span class="kt-table-col-label">Waktu</span></span>
                            </th>
                            <th class="min-w-[220px]">
                                <span class="kt-table-col"><span class="kt-table-col-label">Pengguna</span></span>
                            </th>
                            <th class="min-w-[140px]">
                                <span class="kt-table-col"><span class="kt-table-col-label">Tipe Event</span></span>
                            </th>
                            <th class="min-w-[240px]">
                                <span class="kt-table-col"><span class="kt-table-col-label">Deskripsi</span></span>
                            </th>
                            <th class="min-w-[130px]">
                                <span class="kt-table-col"><span class="kt-table-col-label">Alamat IP</span></span>
                            </th>
                            <th class="min-w-[180px]">
                                <span class="kt-table-col"><span class="kt-table-col-label">Subjek</span></span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td class="text-foreground font-normal whitespace-nowrap">
                                    {{ $log->created_at?->format('d M Y H:i') ?? '—' }}
                                </td>
                                <td>
                                    @if ($log->user)
                                        <div class="flex items-center gap-2.5">
                                            <div class="rounded-full size-8 shrink-0 flex items-center justify-center font-semibold text-xs bg-primary/15 text-primary">
                                                {{ mb_strtoupper(mb_substr($log->user->name, 0, 1)) }}
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-sm font-medium text-foreground">{{ $log->user->name }}</span>
                                                <span class="text-xs text-secondary-foreground">{{ $log->user->email }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-secondary-foreground">System / Guest</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="kt-badge kt-badge-light rounded-[30px]">
                                        {{ match ($log->event_type) {
                                            'login' => 'Login',
                                            'logout' => 'Logout',
                                            'quiz_attempt' => 'Percobaan Kuis',
                                            'attendance' => 'Absensi',
                                            default => 'Lainnya',
                                        } }}
                                    </span>
                                </td>
                                <td class="text-secondary-foreground font-normal">
                                    {{ $log->description ?? '—' }}
                                </td>
                                <td class="text-secondary-foreground font-normal whitespace-nowrap">
                                    {{ $log->ip_address ?? '—' }}
                                </td>
                                <td class="text-secondary-foreground font-normal">
                                    @if ($log->subject_type && $log->subject_id)
                                        {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-sm text-secondary-foreground">
                                    Tidak ada log aktivitas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="kt-card-footer justify-center md:justify-between flex-col md:flex-row gap-5 text-secondary-foreground text-sm font-medium">
            <div class="order-2 md:order-1">
                Menampilkan {{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }} log
            </div>
            <div class="order-1 md:order-2">
                {{ $logs->links() }}
            </div>
        </div>
    </div>

</div>
@endsection
