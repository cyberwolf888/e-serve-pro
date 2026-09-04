{{-- home.blade.php — public landing page --}}
{{-- FR-PUB-01 --}}
@extends('layouts.public')

@section('title', config('app.name') . ' LEARNING — Pembelajaran Digital F&B Service')
@section('description', 'E-SERVEPro LEARNING, ekosistem pembelajaran digital kolaboratif F&B Service berbasis layanan yang dikembangkan oleh Universitas Pendidikan Ganesha.')

@section('content')

{{-- Hero --}}
<section class="relative px-6 lg:px-16 py-16 lg:py-24 bg-muted/30 bg-[radial-gradient(theme(colors.primary/6)_1px,transparent_1px)] bg-[length:18px_18px]">
    <div class="grid lg:grid-cols-2 gap-12 items-center max-w-6xl mx-auto">
        <div class="flex flex-col gap-6">
            <h1 class="text-3xl lg:text-5xl font-semibold text-mono">
                Ekosistem Pembelajaran Digital Kolaboratif
                <span class="text-primary">F&amp;B Service</span>
            </h1>
            <p class="text-base lg:text-lg text-secondary-foreground max-w-lg">
                E-SERVEPro LEARNING menghubungkan materi, pertemuan, presensi, kuis, dan penilaian dalam
                pengalaman belajar aktif yang selaras dengan standar kinerja industri perhotelan.
            </p>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('auth.login.show') }}" class="kt-btn kt-btn-primary kt-btn-lg rounded-full">
                    <i class="ki-outline ki-entrance-left"></i>
                    <span>Masuk ke Ruang Belajar</span>
                </a>
                <a href="{{ route('auth.register.show') }}" class="kt-btn kt-btn-outline kt-btn-lg rounded-full">
                    <span>Daftar sebagai Peserta</span>
                </a>
            </div>
        </div>
        <div class="flex flex-col gap-4">
            <div class="flex flex-wrap items-center justify-center gap-4">
                <span class="bg-white rounded-lg p-2 flex items-center"><img src="{{ asset('assets/media/logo-tut-wuri-handayani.jpeg') }}" alt="Tut Wuri Handayani" class="h-10 w-auto object-contain"></span>
                <span class="bg-white rounded-lg p-2 flex items-center"><img src="{{ asset('assets/media/logo-bima.jpeg') }}" alt="BIMA" class="h-10 w-auto object-contain"></span>
                <span class="bg-white rounded-lg p-2 flex items-center"><img src="{{ asset('assets/media/logo-undiksha.png') }}" alt="Undiksha" class="h-10 w-auto object-contain"></span>
            </div>
            {{-- ASSUMPTION: picsum.photos placeholder per user direction; swap for a real product screenshot later --}}
            <img
                src="{{ url('assets/media/hero-image.jpg') }}"
                alt="Pratinjau platform E-SERVEPro"
                class="rounded-2xl shadow-xl w-full transition-transform duration-300 hover:-translate-y-1 hover:shadow-2xl"
            >
        </div>
    </div>
</section>

{{-- Platform overview --}}
<section id="tentang" class="px-6 lg:px-16 py-10 bg-muted/40">
    <div class="max-w-3xl mx-auto text-center">
        <h2 class="text-xl font-semibold text-mono mb-3">Dikembangkan oleh Undiksha, Siap untuk Lingkungan Relevan</h2>
        <p class="text-sm lg:text-base text-secondary-foreground">
            Dikembangkan oleh Universitas Pendidikan Ganesha (Undiksha), layanan pada tingkat kesiapan
            teknologi TKT 6 ini mentransformasi pembelajaran Food and Beverage Service menjadi lebih aktif,
            kontekstual, terintegrasi teknologi, dan berorientasi industri hospitality.
        </p>
    </div>
</section>

{{-- Feature highlights --}}
<section id="fitur" class="px-6 lg:px-16 py-16">
    <h2 class="text-2xl font-semibold text-mono text-center mb-10">Layanan Pembelajaran Terintegrasi</h2>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 max-w-6xl mx-auto">
        @foreach ([
            ['icon' => 'ki-book-open', 'title' => 'Materi F&B Kontekstual', 'desc' => 'Pelajari prosedur layanan F&B melalui materi visual dan dokumen pembelajaran terstruktur.'],
            ['icon' => 'ki-calendar', 'title' => 'Pertemuan Terarah', 'desc' => 'Kelola setiap sesi pembelajaran beserta materi dan aktivitas kelas yang relevan.'],
            ['icon' => 'ki-check-circle', 'title' => 'Presensi Terintegrasi', 'desc' => 'Dokumentasikan kehadiran peserta pada setiap pertemuan secara tertib dan terukur.'],
            ['icon' => 'ki-notepad-edit', 'title' => 'Evaluasi Otomatis', 'desc' => 'Ukur pemahaman melalui kuis pilihan ganda dengan penilaian otomatis.'],
            ['icon' => 'ki-chart-line', 'title' => 'Penilaian Kinerja', 'desc' => 'Rekap capaian belajar berdasarkan komponen penilaian yang berorientasi pada kompetensi hospitality.'],
            ['icon' => 'ki-shield-tick', 'title' => 'Monitoring Pembelajaran', 'desc' => 'Pantau aktivitas masuk, kuis, presensi, dan perkembangan belajar dalam satu layanan.'],
        ] as $feature)
            <div class="kt-card">
                <div class="kt-card-body p-6 flex flex-col gap-3">
                    <i class="ki-filled {{ $feature['icon'] }} text-2xl text-primary"></i>
                    <h3 class="text-base font-medium text-mono">{{ $feature['title'] }}</h3>
                    <p class="text-sm text-secondary-foreground">{{ $feature['desc'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>

{{-- Role explainer --}}
<section class="px-6 lg:px-16 py-16 bg-muted/40">
    <h2 class="text-2xl font-semibold text-mono text-center mb-10">Kolaborasi dalam Satu Ekosistem</h2>
    <div class="grid sm:grid-cols-3 gap-5 max-w-5xl mx-auto">
        @foreach ([
            ['icon' => 'ki-shield-tick', 'title' => 'Super Admin (Peneliti)', 'desc' => 'Mengelola pengguna, memantau aktivitas pembelajaran, dan menganalisis rekap capaian lintas kelas.'],
            ['icon' => 'ki-profile-circle', 'title' => 'Guru (Pengajar)', 'desc' => 'Merancang kelas F&B Service, membagikan materi, mengelola pertemuan, dan menilai capaian peserta.'],
            ['icon' => 'ki-people', 'title' => 'Siswa (Peserta Didik)', 'desc' => 'Mengakses materi kontekstual, mengikuti aktivitas kelas, mengerjakan kuis, dan memantau hasil belajar.'],
        ] as $role)
            <div class="kt-card">
                <div class="kt-card-body p-6 flex flex-col gap-3 text-center items-center">
                    <i class="ki-filled {{ $role['icon'] }} text-2xl text-primary"></i>
                    <h3 class="text-base font-medium text-mono">{{ $role['title'] }}</h3>
                    <p class="text-sm text-secondary-foreground">{{ $role['desc'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>

@endsection
