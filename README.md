<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="260" alt="Laravel Logo">
</p>

<h1 align="center">PRO-BI SMART</h1>

<p align="center">
  <strong>Platform Pembelajaran Bahasa Indonesia Berbasis Web</strong><br>
  Selaras dengan <em>Kurikulum Merdeka</em>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.3%2B-777BB4?logo=php&logoColor=white" alt="PHP 8.3+">
  <img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?logo=laravel&logoColor=white" alt="Laravel 13.x">
  <img src="https://img.shields.io/badge/MariaDB-10.11%2B-003545?logo=mariadb&logoColor=white" alt="MariaDB">
  <img src="https://img.shields.io/badge/Tailwind%20CSS-v4-06B6D4?logo=tailwindcss&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Metronic-9.5.0-1B84FF" alt="Metronic">
</p>

---

## ✨ Tentang PRO-BI SMART

**PRO-BI SMART** adalah platform pembelajaran Bahasa Indonesia berbasis web yang dirancang untuk mendukung pembelajaran sesuai dengan *Kurikulum Merdeka*. Platform ini mengintegrasikan materi visual, kelas, pertemuan, absensi, kuis pilihan ganda, penilaian, dan rekap progres dalam satu sistem yang terpadu.

### 🎯 Tiga Peran Utama

| Peran | Deskripsi |
|-------|-----------|
| 👨‍💼 **Super Admin (Peneliti)**** | Otoritas tertinggi. Mengelola semua data, pengguna, konfigurasi, dan log aktivitas. |
| 👨‍🏫 **Guru** | Membuat dan mengelola kelas, materi, pertemuan, kuis, serta penilaian siswa. |
| 🎓 **Siswa** | Bergabung ke kelas, mengikuti pertemuan, mengerjakan kuis, dan melihat nilai sendiri. |

---

## 🚀 Fitur Utama

- 🔐 **Autentikasi & Peran** — Login berbasis sesi, siswa dapat mendaftar mandiri, guru dibuat oleh Super Admin, dan reset password via email.
- 🏫 **Manajemen Kelas** — Guru membuat kelas dengan kode unik; siswa bergabung langsung tanpa persetujuan guru.
- 📚 **Materi Pembelajaran** — Bagi materi melalui tautan Figma atau unggah file PDF (maks. 20 MB).
- 📅 **Pertemuan & Absensi** — Jadwalkan pertemuan dan catat kehadiran siswa.
- 📝 **Kuis Pilihan Ganda** — Buat kuis, terbitkan, dan siswa mengerjakannya dengan penilaian otomatis.
- 📊 **Penilaian & Rekap** — Bobot komponen nilai diatur guru, perhitungan nilai akhir manual, dan rekap kelas dapat diunduh.
- 📜 **Monitoring Aktivitas** — Super Admin dapat melihat log login, kuis, dan absensi.

---

## 🛠️ Teknologi

| Lapisan | Teknologi |
|---------|-----------|
| Bahasa | PHP 8.3+ |
| Framework | Laravel 13.x |
| Database | MariaDB 10.11+ (InnoDB, utf8mb4) |
| CSS | Tailwind CSS v4 |
| UI Kit | Metronic 9.5.0 (Tailwind edition) |
| Build Tool | Vite |
| RBAC | spatie/laravel-permission |
| Queue | Laravel Queue |
| Mail | Laravel Mailer (SMTP) |
| App Server | Laravel Octane (FrankenPHP/Swoole) — diaktifkan pada M8 |

---

## ⚡ Cara Menjalankan

```bash
# 1. Instal dependensi PHP
composer install

# 2. Salin konfigurasi environment
cp .env.example .env
php artisan key:generate

# 3. Atur database MariaDB di .env, lalu jalankan migrasi & seeder
php artisan migrate --seed

# 4. Instal dependensi frontend
npm install && npm run dev

# 5. Jalankan server pengembangan
php artisan serve
```

Uji aplikasi:

```bash
php artisan test
```

Format kode sebelum commit:

```bash
./vendor/bin/pint
```

> **Catatan Octane:** Laravel Octane sengaja dimatikan hingga milestone M8 untuk menghindari gangguan debugging. Aktifkan pada M8 dengan `php artisan octane:start`.

---

## 📁 Struktur Proyek

```
app/
├── Models/               # Model Eloquent
├── Http/Controllers/     # Controller, dikelompokkan per peran
├── Http/Requests/        # Form request validation
├── Policies/             # RBAC & otorisasi record
├── Repositories/         # Logika akses dan query data
└── Services/             # Logika bisnis dan alur kerja
database/
├── migrations/           # Migrasi sesuai skema PRD
└── seeders/              # Seeder awal Super Admin & role
resources/views/          # Blade + komponen Metronic
routes/web.php            # Rute sesi berbasis peran
tests/Feature/            # Tes fitur per FR/BR
```

---

## 📖 Dokumen Spesifikasi

Semua persyaratan fungsional, aturan bisnis, skema data, dan kriteria penerimaan tertuang dalam:

- **[`PRD_PRO-BI_SMART_EN.md`](./PRD_PRO-BI_SMART_EN.md)** — Spesifikasi lengkap dan otoritatif.
- **[`AGENTS.md`](./AGENTS.md)** — Panduan pengembangan untuk AI coding agents.
- **[`todos.md`](./todos.md)** — Checklist milestone M0–M8.

---

## 🤝 Kontribusi

Kontribusi sangat terbuka! Pastikan:

1. Setiap fitur memiliki ID PRD yang sesuai (`FR-*` / `BR-*`).
2. Validasi sesuai PRD §9.
3. Tes fitur mencakup jalur sukses dan gagal.
4. Jalankan `./vendor/bin/pint` sebelum commit.

---

## 🔒 Keamanan & Privasi

- RBAC diterapkan sepenuhnya di server melalui Policy + Spatie.
- Password di-hash; gunakan HTTPS di produksi.
- Data pengguna non-aktif menjadi hanya-baca tanpa penghapusan permanen.
- Semua aktivitas login, kuis, dan absensi tercatat dalam log.

---

<p align="center">
  Dibangun dengan ❤️ untuk pendidikan Bahasa Indonesia.
</p>
