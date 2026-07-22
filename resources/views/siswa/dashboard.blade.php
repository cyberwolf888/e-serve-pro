{{-- siswa/dashboard.blade.php — FR-SW-06 / NFR-08 --}}
@extends('layouts.app')

@push('styles')
    <link href="{{ asset('assets/vendors/apexcharts/apexcharts.css') }}" rel="stylesheet"/>
@endpush

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => 'Dashboard']]" />
@endsection

@section('content')
<div class="grid gap-5 pb-7.5 lg:gap-7.5">
    <div class="flex flex-wrap items-end justify-between gap-5 pt-2">
        <div class="flex flex-col gap-2">
            <h1 class="text-xl font-medium leading-none text-mono">Dashboard Siswa</h1>
            <p class="text-sm text-secondary-foreground">Ringkasan pembelajaran Anda dalam 30 hari terakhir</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:gap-7.5 xl:grid-cols-4">
        @php
            $kpiStyles = [
                ['icon' => 'ki-book-open', 'iconClass' => 'bg-primary/10 text-primary', 'palette' => 'bg-gradient-to-br from-primary/15 via-primary/5 to-transparent'],
                ['icon' => 'ki-calendar', 'iconClass' => 'dashboard-kpi-success-icon', 'palette' => 'dashboard-kpi-success'],
                ['icon' => 'ki-notepad', 'iconClass' => 'dashboard-kpi-warning-icon', 'palette' => 'dashboard-kpi-warning'],
                ['icon' => 'ki-chart-line-up-2', 'iconClass' => 'bg-destructive/10 text-destructive', 'palette' => 'bg-gradient-to-br from-destructive/15 via-destructive/5 to-transparent'],
            ];
        @endphp
        @foreach ($dashboard['kpis'] as $kpi)
            @php($style = $kpiStyles[$loop->index])
            <a class="kt-card group relative overflow-hidden border-0 {{ $style['palette'] }} p-5 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-lg focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary lg:p-7.5" href="{{ route($kpi['route']) }}">
                <div class="relative flex items-start justify-between gap-4">
                    <div class="flex flex-col gap-2">
                        <span class="text-sm font-medium text-secondary-foreground">{{ $kpi['label'] }}</span>
                        <span class="text-4xl font-semibold tracking-tight text-mono">{{ number_format($kpi['value']) }}</span>
                    </div>
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-2xl {{ $style['iconClass'] }}">
                        <i class="ki-filled {{ $style['icon'] }} text-2xl"></i>
                    </span>
                </div>
                <span class="relative mt-6 flex items-center gap-1 text-xs font-medium text-secondary-foreground transition-colors group-hover:text-foreground">Lihat detail <i class="ki-filled ki-arrow-right text-sm"></i></span>
            </a>
        @endforeach
    </div>

    <div class="grid items-stretch gap-5 lg:grid-cols-5 lg:gap-7.5">
        <div class="kt-card h-full lg:col-span-3">
            <div class="kt-card-header"><h3 class="kt-card-title">Aktivitas Saya 30 Hari</h3></div>
            <div class="kt-card-content min-h-80 px-3 py-1"><div id="activity_chart"></div></div>
        </div>

        <div class="kt-card h-full lg:col-span-2">
            <div class="kt-card-header"><h3 class="kt-card-title">Perlu Diperhatikan</h3></div>
            <div class="kt-card-content flex flex-col gap-3 p-5 lg:p-7.5">
                @forelse ($dashboard['alerts'] as $alert)
                    <a class="flex items-center justify-between gap-3 text-sm hover:text-primary" href="{{ route($alert['route']) }}">
                        <span class="text-secondary-foreground">{{ $alert['label'] }}</span>
                        <span class="kt-badge kt-badge-outline kt-badge-destructive">{{ $alert['count'] }}</span>
                    </a>
                @empty
                    <p class="text-sm text-secondary-foreground">Tidak ada hal yang perlu diperhatikan.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="kt-card kt-card-grid min-w-full">
        <div class="kt-card-header"><h3 class="kt-card-title">Aktivitas Terbaru Saya</h3></div>
        <div class="kt-card-content">
            <div class="kt-scrollable-x-auto">
                <table class="kt-table kt-table-border table-auto">
                    <thead>
                        <tr>
                            <th><span class="kt-table-col"><span class="kt-table-col-label">Waktu</span></span></th>
                            <th><span class="kt-table-col"><span class="kt-table-col-label">Event</span></span></th>
                            <th><span class="kt-table-col"><span class="kt-table-col-label">Deskripsi</span></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dashboard['recentActivities'] as $activity)
                            <tr>
                                <td class="whitespace-nowrap text-secondary-foreground">{{ $activity->created_at->format('d M Y H:i') }}</td>
                                <td><span class="kt-badge kt-badge-light rounded-[30px]">{{ match ($activity->event_type) {
                                    'login' => 'Login',
                                    'logout' => 'Logout',
                                    'quiz_attempt' => 'Percobaan Kuis',
                                    'attendance' => 'Absensi',
                                    default => 'Lainnya',
                                } }}</span></td>
                                <td class="text-secondary-foreground">{{ $activity->description ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td class="py-8 text-center text-sm text-secondary-foreground" colspan="3">Tidak ada log aktivitas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @php($activityChart = Illuminate\Support\Js::from($dashboard['chart']))
    <script src="{{ asset('assets/vendors/apexcharts/apexcharts.min.js') }}"></script>
    <script>
        const activityChart = {{ $activityChart }};
        new ApexCharts(document.querySelector('#activity_chart'), {
            chart: { type: 'area', height: 320, toolbar: { show: false } },
            series: [{ name: 'Aktivitas', data: activityChart.data }],
            xaxis: { categories: activityChart.categories, tickAmount: 6 },
            stroke: { curve: 'smooth', width: 3 },
            fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.05 } },
            colors: ['#1b84ff'],
            dataLabels: { enabled: false },
            yaxis: { min: 0, forceNiceScale: true },
            grid: { borderColor: '#e4e6ef' },
        }).render();
    </script>
@endpush
