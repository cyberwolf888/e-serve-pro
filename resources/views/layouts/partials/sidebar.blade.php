{{-- layouts/partials/sidebar.blade.php — NFR-08, M0 shell (nav items wired in M1+) --}}
<div class="kt-sidebar bg-background border-e border-e-border fixed top-0 bottom-0 z-20 hidden lg:flex flex-col items-stretch shrink-0 [--kt-drawer-enable:true] lg:[--kt-drawer-enable:false]"
     data-kt-drawer="true"
     data-kt-drawer-class="kt-drawer kt-drawer-start top-0 bottom-0"
     id="sidebar">

    {{-- Sidebar Header: Logo + Collapse Toggle --}}
    <div class="kt-sidebar-header hidden lg:flex items-center relative justify-between px-3 lg:px-6 shrink-0" id="sidebar_header">
        <a href="{{ url('/') }}" aria-label="{{ config('app.name') }}">
            <x-brand-logo class="default-logo" icon-class="size-8" text-class="text-lg"/>
            <x-brand-logo class="small-logo" icon-class="size-8" :show-text="false"/>
        </a>
        <button class="kt-btn kt-btn-outline kt-btn-icon size-[30px] absolute start-full top-2/4 -translate-x-2/4 -translate-y-2/4 rtl:translate-x-2/4"
                data-kt-toggle="body"
                data-kt-toggle-class="kt-sidebar-collapse"
                id="sidebar_toggle">
            <i class="ki-filled ki-black-left-line kt-toggle-active:rotate-180 transition-all duration-300 rtl:translate rtl:rotate-180 rtl:kt-toggle-active:rotate-0"></i>
        </button>
    </div>

    {{-- Sidebar Scrollable Content --}}
    <div class="kt-sidebar-content flex grow shrink-0 py-5 pe-2" id="sidebar_content">
        <div class="kt-scrollable-y-hover grow shrink-0 flex ps-2 lg:ps-5 pe-1 lg:pe-3"
             data-kt-scrollable="true"
             data-kt-scrollable-dependencies="#sidebar_header"
             data-kt-scrollable-height="auto"
             data-kt-scrollable-offset="0px"
             data-kt-scrollable-wrappers="#sidebar_content"
             id="sidebar_scrollable">

            {{-- Sidebar Menu — role-based nav items injected in M1+ --}}
            <div class="kt-menu flex flex-col grow gap-1"
                 data-kt-menu="true"
                 data-kt-menu-accordion-expand-all="false"
                 id="sidebar_menu">

                {{-- Dashboard — FR-AUTH-01 / §3.2: routes are role-scoped, so link directly to the role's dashboard --}}
                @php
                    $dashboardRoute = auth()->user()?->hasRole('super_admin')
                        ? route('admin.dashboard')
                        : (auth()->user()?->hasRole('guru')
                            ? route('guru.dashboard')
                            : route('siswa.dashboard'));
                @endphp
                <div class="kt-menu-item {{ request()->routeIs('admin.dashboard', 'guru.dashboard', 'siswa.dashboard') ? 'active' : '' }}">
                    <a class="kt-menu-link border border-transparent items-center grow
                              kt-menu-item-active:bg-accent/60 kt-menu-item-active:rounded-lg
                              hover:bg-accent/60 hover:rounded-lg gap-[10px] ps-[10px] pe-[10px] py-[8px]"
                       href="{{ $dashboardRoute }}">
                        <span class="kt-menu-icon items-start text-muted-foreground w-[20px]
                                     kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary">
                            <i class="ki-filled ki-element-11 text-lg"></i>
                        </span>
                        <span class="kt-menu-title text-sm font-medium text-foreground
                                     kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary">
                            Dashboard
                        </span>
                    </a>
                </div>

                {{-- Role-specific nav items will be @include'd here in M1 --}}
                @stack('sidebar_nav')

                {{-- Super Admin nav — FR-SA-02 / M2 --}}
                @role('super_admin')
                <div class="kt-menu-item pt-2">
                    <span class="kt-menu-heading text-xs font-semibold text-secondary-foreground uppercase tracking-wider ps-[10px]">Admin</span>
                </div>
                <div class="kt-menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <a class="kt-menu-link border border-transparent items-center grow
                              kt-menu-item-active:bg-accent/60 kt-menu-item-active:rounded-lg
                              hover:bg-accent/60 hover:rounded-lg gap-[10px] ps-[10px] pe-[10px] py-[8px]"
                       href="{{ route('admin.users.index') }}">
                        <span class="kt-menu-icon items-start text-muted-foreground w-[20px]">
                            <i class="ki-filled ki-profile-circle text-lg"></i>
                        </span>
                        <span class="kt-menu-title text-sm font-medium text-foreground
                                     kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary">
                            Pengguna
                        </span>
                    </a>
                </div>
                <div class="kt-menu-item {{ request()->routeIs('admin.classes.*') && ! request()->routeIs('admin.classes.recap*') ? 'active' : '' }}">
                    <a class="kt-menu-link border border-transparent items-center grow
                              kt-menu-item-active:bg-accent/60 kt-menu-item-active:rounded-lg
                              hover:bg-accent/60 hover:rounded-lg gap-[10px] ps-[10px] pe-[10px] py-[8px]"
                       href="{{ route('admin.classes.index') }}">
                        <span class="kt-menu-icon items-start text-muted-foreground w-[20px]
                                     kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary">
                            <i class="ki-filled ki-book-open text-lg"></i>
                        </span>
                        <span class="kt-menu-title text-sm font-medium text-foreground
                                     kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary">
                            Kelas
                        </span>
                    </a>
                </div>
                <div class="kt-menu-item {{ request()->routeIs('admin.recap.*', 'admin.classes.recap', 'admin.classes.recap.export') ? 'active' : '' }}">
                    <a class="kt-menu-link border border-transparent items-center grow
                              kt-menu-item-active:bg-accent/60 kt-menu-item-active:rounded-lg
                              hover:bg-accent/60 hover:rounded-lg gap-[10px] ps-[10px] pe-[10px] py-[8px]"
                       href="{{ route('admin.recap.index') }}">
                        <span class="kt-menu-icon items-start text-muted-foreground w-[20px]
                                     kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary">
                            <i class="ki-filled ki-chart-line text-lg"></i>
                        </span>
                        <span class="kt-menu-title text-sm font-medium text-foreground
                                     kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary">
                            Rekap Nilai
                        </span>
                    </a>
                </div>
                <div class="kt-menu-item {{ request()->routeIs('admin.monitoring') ? 'active' : '' }}">
                    <a class="kt-menu-link border border-transparent items-center grow
                              kt-menu-item-active:bg-accent/60 kt-menu-item-active:rounded-lg
                              hover:bg-accent/60 hover:rounded-lg gap-[10px] ps-[10px] pe-[10px] py-[8px]"
                       href="{{ route('admin.monitoring') }}">
                        <span class="kt-menu-icon items-start text-muted-foreground w-[20px]
                                     kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary">
                            <i class="ki-filled ki-status text-lg"></i>
                        </span>
                        <span class="kt-menu-title text-sm font-medium text-foreground
                                     kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary">
                            Monitoring
                        </span>
                    </a>
                </div>
                @endrole

                @role('guru')
                <div class="kt-menu-item {{ request()->routeIs('guru.classes.*') ? 'active' : '' }}">
                    <a class="kt-menu-link border border-transparent items-center grow
                              kt-menu-item-active:bg-accent/60 kt-menu-item-active:rounded-lg
                              hover:bg-accent/60 hover:rounded-lg gap-[10px] ps-[10px] pe-[10px] py-[8px]"
                       href="{{ route('guru.classes.index') }}">
                        <span class="kt-menu-icon items-start text-muted-foreground w-[20px]
                                     kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary">
                            <i class="ki-filled ki-book-open text-lg"></i>
                        </span>
                        <span class="kt-menu-title text-sm font-medium text-foreground
                                     kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary">
                            Kelas Saya
                        </span>
                    </a>
                </div>
                @endrole

                @role('siswa')
                <div class="kt-menu-item {{ request()->routeIs('siswa.classes.*') ? 'active' : '' }}">
                    <a class="kt-menu-link border border-transparent items-center grow
                              kt-menu-item-active:bg-accent/60 kt-menu-item-active:rounded-lg
                              hover:bg-accent/60 hover:rounded-lg gap-[10px] ps-[10px] pe-[10px] py-[8px]"
                       href="{{ route('siswa.classes.index') }}">
                        <span class="kt-menu-icon items-start text-muted-foreground w-[20px]
                                     kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary">
                            <i class="ki-filled ki-book-open text-lg"></i>
                        </span>
                        <span class="kt-menu-title text-sm font-medium text-foreground
                                     kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary">
                            Kelas Saya
                        </span>
                    </a>
                </div>
                <div class="kt-menu-item {{ request()->routeIs('siswa.grades.*') ? 'active' : '' }}">
                    <a class="kt-menu-link border border-transparent items-center grow hover:bg-accent/60 hover:rounded-lg gap-[10px] ps-[10px] pe-[10px] py-[8px]" href="{{ route('siswa.grades.index') }}"><span class="kt-menu-icon w-[20px]"><i class="ki-filled ki-chart-line text-lg"></i></span><span class="kt-menu-title text-sm font-medium text-foreground">Nilai Saya</span></a>
                </div>
                @endrole

            </div>
            {{-- End Sidebar Menu --}}
        </div>
    </div>
</div>
