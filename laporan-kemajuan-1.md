# Laporan Kemajuan PRO-BI SMART

**Tanggal:** 18 Juli 2026  
**Status:** M0-M6 selesai; M7 belum dimulai; M8 sebagian selesai.

## 1. Ringkasan

PRO-BI SMART telah memiliki fondasi aplikasi Laravel 13 untuk pembelajaran Online berbasis Kurikulum Merdeka. Fitur utama untuk autentikasi, manajemen pengguna, kelas, materi, pertemuan, absensi, kuis, penilaian, dan rekap telah diimplementasikan.

## 2. Pekerjaan Selesai

### M0 - Scaffold

- Aplikasi Laravel 13 berhasil dibuat dan berjalan pada PHP 8.4.
- Tailwind CSS, Vite, dan Metronic 9.5.0 Tailwind telah diintegrasikan.
- Layout autentikasi dan layout aplikasi bersama telah tersedia.
- Konfigurasi pengujian dan smoke test telah dibuat.
- Konfigurasi Docker, deployment, dan worker FrankenPHP telah ditambahkan.

### M1 - Autentikasi dan RBAC

- Login dan logout berbasis sesi tersedia untuk tiga peran: `super_admin`, `guru`, dan `siswa`.
- Siswa dapat melakukan registrasi mandiri.
- Akun guru hanya dapat dibuat oleh Super Admin.
- Reset kata sandi melalui email telah tersedia.
- Spatie Laravel Permission digunakan untuk role dan permission.
- Role, permission dasar, dan Super Admin awal telah disiapkan melalui seeder.
- Redirect dashboard berdasarkan role telah diterapkan.
- Aktivitas login dan logout dicatat pada `activity_logs`.
- Policy untuk pembatasan akses berbasis role dan kepemilikan telah dibuat.

### M2 - Manajemen Pengguna dan Read-only Guard

- Super Admin dapat membuat, mengubah, dan menonaktifkan pengguna.
- Pembuatan guru mencatat `created_by` dan menetapkan role `guru`.
- Penonaktifan menggunakan `is_active=0`, bukan penghapusan permanen.
- Data yang terkait pengguna nonaktif dilindungi oleh read-only guard.
- Validasi nama, email unik, role, dan kata sandi telah diterapkan.

### M3 - Kelas dan Keanggotaan

- CRUD kelas untuk Guru dan Super Admin tersedia.
- Kode kelas unik otomatis dibuat dengan panjang 6-8 karakter.
- Guru dapat menambahkan siswa ke kelas miliknya.
- Siswa dapat bergabung langsung menggunakan kode kelas tanpa persetujuan guru.
- Keanggotaan duplikat ditolak melalui validasi dan constraint database.
- Tidak ada batas buatan untuk jumlah kelas atau siswa.
- Daftar kelas siswa telah tersedia.

### M4 - Materi, Pertemuan, dan Absensi

- Materi melalui tautan Figma telah tersedia.
- Unggah materi dibatasi pada PDF dengan ukuran maksimal 20 MB.
- CRUD pertemuan berdasarkan kelas telah tersedia.
- Guru dapat membagikan materi ke pertemuan.
- Absensi dengan status `hadir`, `izin`, `sakit`, dan `alfa` telah tersedia.
- Siswa hanya dapat melihat pertemuan dan materi dari kelas yang diikuti.
- Aktivitas absensi dicatat pada `activity_logs`.

### M5 - Kuis

- Struktur data kuis, pertanyaan, pilihan, percobaan, dan jawaban telah dibuat.
- Guru dapat membuat kuis pilihan ganda dan mengatur pilihan jawaban.
- Setiap pertanyaan wajib memiliki tepat satu jawaban benar.
- Fitur publish/unpublish dan jadwal buka-tutup kuis tersedia.
- Siswa hanya dapat mengerjakan kuis dari kelas yang diikuti.
- Satu percobaan per siswa diterapkan dengan constraint database.
- Nilai otomatis dihitung pada saat pengumpulan jawaban.
- Ketepatan setiap jawaban dan nilai 0-100 disimpan.
- Aktivitas percobaan kuis dicatat pada `activity_logs`.

### M6 - Penilaian dan Rekap

- Komponen nilai dan bobot manual per kelas telah tersedia.
- Skor komponen per siswa dapat dicatat.
- Nilai akhir dihitung menggunakan jumlah berbobot dan disimpan pada `final_grades`.
- Rekap nilai Guru untuk kelas yang dimiliki telah tersedia.
- Dashboard dan daftar nilai siswa hanya menampilkan nilai miliknya.
- Super Admin dapat melihat rekap lintas kelas.
- Ekspor rekap ke format XLSX telah diimplementasikan.
- Tabel `component_scores` dan seeder role pengguna telah diperbarui pada pekerjaan terakhir.
- Form kelas telah ditingkatkan dengan pemilihan guru yang dapat dicari.

## 3. Verifikasi Teknis

- Versi PHP: 8.4.
- Versi Laravel: 13.19.0.
- Laravel Octane: 2.17.5 terpasang.
- Tailwind CSS: 4.3.2.
- Build frontend: berhasil melalui `npm run build`.
- Pengujian: 114 tes dijalankan, 113 lulus dan 1 gagal.
- Kegagalan tes berada pada `Tests\Feature\Auth\LoginTest::test_login_page_generates_https_urls_behind_a_tls_proxy`, karena tes masih mencari referensi `/assets/css/styles.css` lama, sedangkan halaman memakai aset Vite hasil build.

## 4. Pekerjaan Belum Selesai

### M7 - Monitoring

- UI monitoring Super Admin untuk `activity_logs` belum selesai.
- Filter berdasarkan pengguna, tipe aktivitas, dan rentang tanggal belum selesai.
- Pagination log belum selesai.
- Gate M7 belum terpenuhi.

### M8 - Hardening dan Octane

- Octane telah dipasang/diaktifkan sesuai checklist, tetapi audit kebocoran state pada worker belum selesai.
- Pengukuran performa TTFB dan waktu muat belum selesai.
- Review keamanan menyeluruh untuk seluruh route dan RBAC belum selesai.
- Verifikasi pembatasan akses PII belum selesai.
- Uji responsif lintas browser dan perangkat belum selesai.
- Full test suite belum seluruhnya hijau.
- Review seluruh flag `ASSUMPTION` dan verifikasi tidak adanya batas buatan belum selesai.

## 5. Kesimpulan

Fungsi inti pembelajaran dan penilaian telah tersedia hingga M6. Fokus pekerjaan berikutnya adalah menyelesaikan monitoring aktivitas pada M7, memperbaiki satu tes autentikasi yang masih mengacu pada aset lama, lalu menuntaskan hardening, validasi performa, keamanan, kompatibilitas, dan verifikasi akhir M8.
