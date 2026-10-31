# Promise — Project Management Information System

Sistem informasi manajemen proyek berbasis web yang mengacu pada **PMBOK® Guide (Project Management Institute)** dan cakupan fitur Project Online / Project Web App: portofolio, piagam proyek, WBS, jadwal Gantt dengan keterkaitan tugas, Earned Value, sumber daya dan lembar waktu, risiko, isu, perubahan, mutu, komunikasi, pengadaan, serta pusat dokumen digital.

## Fitur

- **9 area pengetahuan PMBOK** sebagai grup menu: Integrasi, Ruang Lingkup, Waktu, Biaya, Mutu, SDM, Komunikasi, Risiko, Pengadaan; 5 kelompok proses sebagai fase proyek.
- **Ruang Proyek**: KPI, Gantt, WBS, matriks risiko 5×5, perubahan, pengadaan, mutu, tim, dokumen, piagam, kurva S dan biaya per WBS.
- **Gantt chart** dengan keterkaitan **FS, SS, FF, SF** dan lag/lead, baseline, milestone, garis tanggal status, filter level WBS.
- **Earned Value Management**: PV, EV, AC, SV, CV, SPI, CPI, EAC, ETC, VAC, TCPI, status kesehatan (hijau/kuning/merah).
- **Dasbor portofolio** dengan 10+ grafik (kurva S, fase, kesehatan per unit, SPI–CPI, anggaran vs EAC, risiko, jam kerja, kontrak, milestone).
- **Pusat Dokumen**: unggah PDF, DOC/DOCX, XLS/XLSX, PPT/PPTX, PNG, JPG (maks. 20 MB) ke proyek, tugas WBS, risiko, isu, perubahan, mutu, kontrak, laporan status; disimpan privat, unduh/pratinjau setelah login.
- Peran: Administrator, PMO, Manajer Proyek (hanya proyeknya), Anggota Tim, Eksekutif (lihat), Tamu (demo, hanya lihat).
- Halaman depan promosi dan tombol **Masuk sebagai Tamu** (`PROMISE_GUEST_LOGIN=true`).
- Data demo 17 proyek bertema MedcoEnergi 2025 dengan WBS level 2 (DATA DEMO, bukan data resmi).

## Teknologi

Laravel 13, Filament 5, Livewire 4, Chart.js, PHP 8.3+, MySQL/MariaDB (SQLite untuk pengembangan dan tes).

## Menjalankan secara lokal

```bash
composer install
cp .env.example .env            # untuk SQLite: DB_CONNECTION=sqlite, DB_DATABASE=/path/absolut/database.sqlite
php artisan key:generate
php artisan migrate --seed      # di luar produksi otomatis memuat data demo
php artisan serve
```

Login: `admin@promise.local` / `Promise@2026` (atau nilai `PROMISE_ADMIN_*`). Akun demo `@contoh.local` memakai sandi `Promise-2026!`.

## Tes

```bash
composer test     # atau: vendor/bin/phpunit
```

## Instalasi di hosting

Lihat [PANDUAN-CPANEL.md](PANDUAN-CPANEL.md).
