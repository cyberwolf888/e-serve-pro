{{-- guru/classes/_tabs.blade.php — nav between Detail Kelas / Materi / Pertemuan for a class --}}
<div class="kt-menu flex-nowrap border-b border-b-border mb-2" data-kt-menu="true">
    <div class="kt-menu-item border-b-2 border-b-transparent kt-menu-item-active:border-b-primary {{ request()->routeIs($routePrefix.'.classes.show') ? 'active' : '' }}">
        <a class="kt-menu-link gap-1.5 pb-2 lg:pb-3 px-2" href="{{ route($routePrefix.'.classes.show', $class) }}">
            <i class="ki-filled ki-user text-sm"></i>
            <span class="kt-menu-title text-sm font-medium text-secondary-foreground kt-menu-item-active:text-primary kt-menu-item-active:font-semibold">
                Detail Kelas
            </span>
        </a>
    </div>
    <div class="kt-menu-item border-b-2 border-b-transparent kt-menu-item-active:border-b-primary {{ request()->routeIs($routePrefix.'.classes.materials.*') ? 'active' : '' }}">
        <a class="kt-menu-link gap-1.5 pb-2 lg:pb-3 px-2" href="{{ route($routePrefix.'.classes.materials.index', $class) }}">
            <i class="ki-filled ki-book text-sm"></i>
            <span class="kt-menu-title text-sm font-medium text-secondary-foreground kt-menu-item-active:text-primary kt-menu-item-active:font-semibold">
                Materi
            </span>
        </a>
    </div>
    <div class="kt-menu-item border-b-2 border-b-transparent kt-menu-item-active:border-b-primary {{ request()->routeIs($routePrefix.'.classes.meetings.*') ? 'active' : '' }}">
        <a class="kt-menu-link gap-1.5 pb-2 lg:pb-3 px-2" href="{{ route($routePrefix.'.classes.meetings.index', $class) }}">
            <i class="ki-filled ki-calendar text-sm"></i>
            <span class="kt-menu-title text-sm font-medium text-secondary-foreground kt-menu-item-active:text-primary kt-menu-item-active:font-semibold">
                Pertemuan
            </span>
        </a>
    </div>
    <div class="kt-menu-item border-b-2 border-b-transparent kt-menu-item-active:border-b-primary {{ request()->routeIs($routePrefix.'.classes.quizzes.*') ? 'active' : '' }}">
        <a class="kt-menu-link gap-1.5 pb-2 lg:pb-3 px-2" href="{{ route($routePrefix.'.classes.quizzes.index', $class) }}">
            <i class="ki-filled ki-notepad text-sm"></i>
            <span class="kt-menu-title text-sm font-medium text-secondary-foreground kt-menu-item-active:text-primary kt-menu-item-active:font-semibold">
                Kuis
            </span>
        </a>
    </div>
    <div class="kt-menu-item border-b-2 border-b-transparent kt-menu-item-active:border-b-primary {{ request()->routeIs($routePrefix.'.classes.discussions.*') ? 'active' : '' }}">
        <a class="kt-menu-link gap-1.5 pb-2 lg:pb-3 px-2" href="{{ route($routePrefix.'.classes.discussions.index', $class) }}">
            <i class="ki-filled ki-message-text text-sm"></i>
            <span class="kt-menu-title text-sm font-medium text-secondary-foreground kt-menu-item-active:text-primary kt-menu-item-active:font-semibold">
                Diskusi
            </span>
        </a>
    </div>
    <div class="kt-menu-item border-b-2 border-b-transparent kt-menu-item-active:border-b-primary {{ request()->routeIs($routePrefix.'.classes.grade-components.*', $routePrefix.'.classes.recap') ? 'active' : '' }}">
        <a class="kt-menu-link gap-1.5 pb-2 lg:pb-3 px-2" href="{{ route($routePrefix.'.classes.grade-components.index', $class) }}">
            <i class="ki-filled ki-chart-simple text-sm"></i>
            <span class="kt-menu-title text-sm font-medium text-secondary-foreground kt-menu-item-active:text-primary kt-menu-item-active:font-semibold">Nilai</span>
        </a>
    </div>
</div>
