{{-- NFR-08 — shared dashboard hero using Metronic card patterns --}}
@props(['title', 'description', 'icon'])

<section class="kt-card relative overflow-hidden border-0 bg-gradient-to-br from-primary via-primary to-primary-active shadow-lg" aria-labelledby="dashboard-title">
    <div class="pointer-events-none absolute -start-16 -top-20 size-64 rounded-full bg-white/10"></div>
    <div class="pointer-events-none absolute bottom-0 start-1/3 size-32 translate-y-1/2 rounded-full bg-white/10"></div>

    <div class="relative grid items-center gap-6 p-5 sm:p-7.5 lg:grid-cols-[minmax(0,1fr)_minmax(320px,520px)] lg:gap-10">
        <div class="flex flex-col items-start gap-4 text-white">
            <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1.5 text-xs font-semibold uppercase tracking-wider">
                <i class="ki-filled {{ $icon }} text-base"></i>
                E-SERVEPro
            </span>
            <div class="flex flex-col gap-2">
                <h1 id="dashboard-title" class="text-2xl font-semibold leading-tight sm:text-3xl">{{ $title }}</h1>
                <p class="max-w-2xl text-sm leading-6 text-white/80 sm:text-base">
                    Selamat datang, <span class="font-semibold text-white">{{ auth()->user()->name }}</span>. {{ $description }}
                </p>
            </div>
        </div>

        <div class="rounded-xl bg-white p-3 shadow-sm sm:p-4">
            <p class="mb-2 text-center text-[11px] font-semibold uppercase tracking-[0.18em] text-secondary-foreground">Kolaborasi dan Dukungan</p>
            <img
                src="{{ asset('assets/media/partner-logo.png') }}"
                alt="Logo Kemendikbudristek, Diktisaintek Berdampak, BIMA, dan Undiksha"
                class="h-auto w-full object-contain"
            >
        </div>
    </div>
</section>
