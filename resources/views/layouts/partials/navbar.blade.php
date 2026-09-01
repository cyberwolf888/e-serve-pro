{{-- NFR-08 / FR-AUTH-01 — Metronic Demo 2 role navigation --}}
@php
    $dashboardRoute = auth()->user()->hasRole('super_admin')
        ? route('admin.dashboard')
        : (auth()->user()->hasRole('guru')
            ? route('guru.dashboard')
            : route('siswa.dashboard'));

    $itemClass = 'kt-menu-item border-b-2 border-b-transparent kt-menu-item-active:border-b-mono kt-menu-item-here:border-b-mono';
    $linkClass = 'kt-menu-link gap-2.5 pb-2 lg:pb-4';
    $titleClass = 'kt-menu-title text-nowrap text-sm text-foreground kt-menu-item-active:text-mono kt-menu-item-active:font-medium kt-menu-item-here:text-mono kt-menu-item-here:font-medium kt-menu-item-show:text-mono kt-menu-link-hover:text-mono';
@endphp

<div class="border-b border-border pb-5 lg:pb-0 mb-5 lg:mb-10">
    <div class="kt-container-fixed flex justify-between items-center gap-2">
        <div class="grid min-w-0">
            <div class="kt-scrollable-x-auto">
                <nav class="kt-menu gap-5 lg:gap-7.5" data-kt-menu="true" id="primary_navigation" aria-label="Navigasi utama">
                    <div class="{{ $itemClass }} {{ request()->routeIs('admin.dashboard', 'guru.dashboard', 'siswa.dashboard') ? 'active' : '' }}">
                        <a class="{{ $linkClass }}" href="{{ $dashboardRoute }}">
                            <span class="{{ $titleClass }}">Dashboard</span>
                        </a>
                    </div>

                    @role('super_admin')
                        <div class="{{ $itemClass }} {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <a class="{{ $linkClass }}" href="{{ route('admin.users.index') }}">
                                <span class="{{ $titleClass }}">Pengguna</span>
                            </a>
                        </div>
                        <div class="{{ $itemClass }} {{ request()->routeIs('admin.classes.*') && ! request()->routeIs('admin.classes.recap*') ? 'active' : '' }}">
                            <a class="{{ $linkClass }}" href="{{ route('admin.classes.index') }}">
                                <span class="{{ $titleClass }}">Kelas</span>
                            </a>
                        </div>
                        <div class="{{ $itemClass }} {{ request()->routeIs('admin.recap.*', 'admin.classes.recap', 'admin.classes.recap.export') ? 'active' : '' }}">
                            <a class="{{ $linkClass }}" href="{{ route('admin.recap.index') }}">
                                <span class="{{ $titleClass }}">Rekap Nilai</span>
                            </a>
                        </div>
                        <div class="{{ $itemClass }} {{ request()->routeIs('admin.monitoring') ? 'active' : '' }}">
                            <a class="{{ $linkClass }}" href="{{ route('admin.monitoring') }}">
                                <span class="{{ $titleClass }}">Monitoring</span>
                            </a>
                        </div>
                    @endrole

                    @role('guru')
                        <div class="{{ $itemClass }} {{ request()->routeIs('guru.classes.*') ? 'active' : '' }}">
                            <a class="{{ $linkClass }}" href="{{ route('guru.classes.index') }}">
                                <span class="{{ $titleClass }}">Kelas Saya</span>
                            </a>
                        </div>
                    @endrole

                    @role('siswa')
                        <div class="{{ $itemClass }} {{ request()->routeIs('siswa.classes.*') ? 'active' : '' }}">
                            <a class="{{ $linkClass }}" href="{{ route('siswa.classes.index') }}">
                                <span class="{{ $titleClass }}">Kelas Saya</span>
                            </a>
                        </div>
                        <div class="{{ $itemClass }} {{ request()->routeIs('siswa.grades.*') ? 'active' : '' }}">
                            <a class="{{ $linkClass }}" href="{{ route('siswa.grades.index') }}">
                                <span class="{{ $titleClass }}">Nilai Saya</span>
                            </a>
                        </div>
                    @endrole
                </nav>
            </div>
        </div>
    </div>
</div>
