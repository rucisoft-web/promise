# Panduan Instalasi Promise di cPanel

Promise (Project Management Information System) adalah aplikasi Laravel 13 + Filament 5. Panduan ini untuk hosting cPanel biasa (shared hosting atau VPS dengan cPanel), dengan dua jalur: **dengan Terminal/SSH** dan **tanpa Terminal** (File Manager + Cron Jobs).

Contoh di panduan memakai akun cPanel `userku`, folder program `/home/userku/promise`, dan alamat `https://promise.domainanda.com`. Sesuaikan dengan milik Anda.

## 1. Prasyarat hosting

| Komponen | Ketentuan | Tempat di cPanel |
|---|---|---|
| PHP | **8.3 atau lebih baru** | *MultiPHP Manager* atau *Select PHP Version* (CloudLinux) |
| Ekstensi PHP | `intl`, `pdo_mysql`, `mbstring`, `zip`, `gd`, `fileinfo`, `openssl`, `bcmath`, `ctype`, `tokenizer`, `xml`, `dom`, `curl` | *Select PHP Version › Extensions* (terutama **intl**, **zip**, **fileinfo**) |
| Database | MySQL 8 atau MariaDB 10.6+ | *MySQL Databases* |
| Batas upload | `upload_max_filesize` dan `post_max_size` minimal **25M** (lampiran dokumen maksimal 20 MB per file) | *MultiPHP INI Editor* |
| Cron | Wajib (antrian dan tugas terjadwal) | *Cron Jobs* |
| SSL | Disarankan | *SSL/TLS Status* › Run AutoSSL |

## 2. Buat database

1. **MySQL Databases** › *Create New Database*: `promise` (menjadi `userku_promise`).
2. *Add New User*: misalnya `pmis` (menjadi `userku_pmis`) dengan kata sandi kuat.
3. *Add User To Database* › centang **ALL PRIVILEGES** › Make Changes.

## 3. Taruh program di server

Program diletakkan **di luar `public_html`** (misalnya `/home/userku/promise`), supaya `.env`, kode, dan **dokumen proyek yang diunggah** tidak bisa dibuka langsung dari browser.

### Jalur A: dengan Terminal

```bash
cd ~
git clone https://github.com/rucisoft-web/promise.git
cd promise
composer install --no-dev --optimize-autoloader
```

Repo privat meminta username GitHub dan *personal access token*. Bila `composer` tidak ada: `curl -sS https://getcomposer.org/installer | php` lalu `php composer.phar install --no-dev --optimize-autoloader`. Bila `php -v` bukan 8.3, pakai path lengkap, misalnya `/opt/cpanel/ea-php83/root/usr/bin/php` atau `/opt/alt/php83/usr/bin/php`.

### Jalur B: tanpa Terminal

1. Pakai paket **`promise-cpanel.zip`** (sudah berisi folder `vendor` produksi dan aset Filament, tidak perlu Composer).
2. *File Manager* › `/home/userku` › **Upload** zip › klik kanan › **Extract** ke `/home/userku`. Hasilnya folder `/home/userku/promise`.
3. Hapus file zip setelah diekstrak.

## 4. Arahkan domain ke folder `public`

**Pilihan 1 (disarankan):** *Domains* › Create A New Domain (misalnya `promise.domainanda.com`), matikan *Share document root*, isi **Document Root** `promise/public`. Aktifkan SSL.

**Pilihan 2 (domain utama terkunci di `public_html`):** salin seluruh isi `promise/public` ke `public_html` (termasuk `.htaccess`; aktifkan *Show Hidden Files*), lalu edit `public_html/index.php`, ganti tiga baris `__DIR__.'/../` menjadi:

```php
if (file_exists($maintenance = __DIR__.'/../promise/storage/framework/maintenance.php')) {
require __DIR__.'/../promise/vendor/autoload.php';
$app = require_once __DIR__.'/../promise/bootstrap/app.php';
```

> Jangan mengekstrak seluruh program ke `public_html`.

## 5. Isi file `.env`

Di folder `promise`, salin `.env.example` menjadi `.env`, lalu ubah:

```dotenv
APP_NAME="Promise"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://promise.domainanda.com
APP_TIMEZONE=Asia/Jakarta
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=userku_promise
DB_USERNAME=userku_pmis
DB_PASSWORD="kata-sandi-database"

SESSION_SECURE_COOKIE=true

# tanggal status Earned Value (YYYY-MM-DD); kosongkan = hari ini
PROMISE_STATUS_DATE=
# akun admin awal (opsional; bisa juga lewat promise:create-admin)
PROMISE_ADMIN_EMAIL=admin@domainanda.com
PROMISE_ADMIN_PASSWORD=

# data demo (MedcoEnergi, SMBC Indonesia, Pakuwon Jati): biarkan false untuk pemakaian sungguhan
PROMISE_SEED_DEMO=false
# situs demo/promosi: tombol "Masuk sebagai Tamu" (hanya lihat), lihat bagian 8
PROMISE_GUEST_LOGIN=false
PROMISE_WHATSAPP=6281234567890
PROMISE_COMPANY_NAME=
```

Biarkan `APP_KEY=` kosong; diisi oleh `key:generate` di langkah 6. Di cPanel `DB_HOST` hampir selalu `localhost`. Kata sandi yang memuat `#` atau spasi diberi tanda kutip.

## 6. Kunci aplikasi, tabel, dan admin

### Dengan Terminal

```bash
cd ~/promise
php artisan key:generate --force
php artisan migrate --seed --force
php artisan promise:create-admin
php artisan optimize
```

Di produksi, `migrate --seed` hanya membuat admin bila `PROMISE_ADMIN_PASSWORD` diisi, dan data demo hanya dimuat bila `PROMISE_SEED_DEMO=true`. `promise:create-admin` menanyakan nama, email, dan kata sandi (atau pakai `--name= --email= --password=`); bila email sudah ada, akun itu dijadikan admin dan kata sandinya diganti.

### Tanpa Terminal (Cron Jobs sekali jalan)

Tambahkan perintah berikut satu per satu dengan jadwal *Once Per Minute*, tunggu 1–2 menit, lalu **hapus** cron tersebut sebelum menambah yang berikutnya:

1. `cd /home/userku/promise && php artisan key:generate --force > storage/logs/setup-1.txt 2>&1`
2. `cd /home/userku/promise && php artisan migrate --seed --force > storage/logs/setup-2.txt 2>&1`
3. `cd /home/userku/promise && php artisan promise:create-admin --name="Administrator" --email=admin@domainanda.com --password='SandiSementara2026' > storage/logs/setup-3.txt 2>&1`
4. `cd /home/userku/promise && php artisan optimize > storage/logs/setup-4.txt 2>&1`

Periksa hasilnya di `promise/storage/logs/setup-*.txt`. Setelah login pertama, **ganti kata sandi** di menu profil.

## 7. Cron permanen (wajib)

```
cd /home/userku/promise && php artisan schedule:run >> /dev/null 2>&1
```

Jadwal *Once Per Minute*. Cron ini menjalankan antrian (notifikasi) tanpa perlu `queue:work` terpisah.

## 8. Mulai memakai

1. Buka `https://promise.domainanda.com/admin` dan login sebagai admin.
2. **Administrasi › Pengguna**: buat akun dengan peran *PMO*, *Manajer Proyek*, *Anggota Tim*, atau *Eksekutif*.
3. **Integrasi › Portofolio & Program**, lalu **Integrasi › Proyek & Piagam** › Buat (isi Piagam Proyek, tanggal baseline, anggaran, cadangan).
4. **Ruang Lingkup › Work Breakdown Structure**: susun WBS level 1 dan 2 (paket kerja berbobot biaya), lalu **Waktu › Keterkaitan Tugas (FS/SS/FF/SF)** (FS, SS, FF, SF dan lag). Gantt chart tampil di **Waktu › Gantt Chart** dan di Ruang Proyek.
5. Isi register lain sesuai 9 area pengetahuan PMBOK: Mutu, SDM (sumber daya, penugasan, lembar waktu), Komunikasi (pemangku kepentingan, rencana komunikasi, laporan status), Risiko & Isu, Pengadaan (vendor, kontrak), Integrasi (perubahan, pelajaran).
6. **Pusat Dokumen**: unggah PDF, DOC/DOCX, XLS/XLSX, PPT, PNG, JPG (maks. 20 MB per file). Dokumen bisa ditautkan ke proyek, tugas WBS, risiko, isu, permintaan perubahan, pemeriksaan mutu, kontrak, atau laporan status, dan juga bisa dilampirkan langsung dari form masing-masing (tab/bagian *Lampiran*).

Dokumen tersimpan privat di `promise/storage/app/private/dokumen` dan hanya bisa diunduh oleh pengguna yang sudah login. **Sertakan folder ini saat backup**, selain database. Tidak perlu `php artisan storage:link`.

### Data demo (untuk presentasi)

Tersedia tiga perusahaan contoh, masing-masing dengan WBS level 2, keterkaitan tugas FS/SS/FF/SF, Earned Value bulanan, risiko, kontrak, lembar waktu, dan dokumen contoh:

1. **MedcoEnergi** (`DemoMedcoSeeder`): 17 proyek hulu migas, ketenagalistrikan, tambang, dan transformasi korporat.
2. **SMBC Indonesia** (`DemoSmbcSeeder`): 12 proyek perbankan, yaitu peningkatan core banking, AI generatif, adopsi cloud, XDR & WAF, konsolidasi aplikasi, kualitas data laporan, pusat data pemulihan (DR), fitur Jenius, TOUCHBIZ, penguatan peran PIKK, pembiayaan berkelanjutan, dan transformasi digital HR. 
3. **Pakuwon Jati** (`DemoPakuwonSeeder`): 12 proyek properti dan perhotelan, yaitu Pakuwon Mall Semarang Tahap 1, extension Pakuwon Mall Phase 5, Kota Kasablanka Phase 4, Superblok Pakuwon Nusantara & Four Points IKN, Hotel Fairfield Bekasi, Aloft Surabaya, Four Points Bekasi, Pakuwon Residence Bekasi, klaster Grand Pakuwon, Park Shanghai, upgrade Tunjungan Plaza, dan digitalisasi operasional.

Muat ketiganya dengan `PROMISE_SEED_DEMO=true` sebelum `migrate --seed`, atau satu per satu:

```bash
cd ~/promise
php artisan db:seed --class=DemoMedcoSeeder --force
php artisan db:seed --class=DemoSmbcSeeder --force
php artisan db:seed --class=DemoPakuwonSeeder --force
php artisan optimize:clear && php artisan optimize
```

Setiap perintah dilewati bila demo tersebut sudah ada. Akun demo SMBC memakai awalan `smbc.` (mis. `smbc.pmo@contoh.local`) dan Pakuwon memakai awalan `pwn.`. Akun demo memakai domain `@contoh.local` dengan kata sandi `Promise-2026!`, jadi **jangan memuat demo di situs sungguhan** yang bisa diakses publik.

> **Peringatan: DATA DEMO, bukan proyek yang sesungguhnya.** Seluruh proyek contoh hanya untuk keperluan pembelajaran dan peragaan fitur Promise. Nama proyek mengacu pada Laporan Tahunan 2025 PT Medco Energi Internasional Tbk, PT Bank SMBC Indonesia Tbk, dan PT Pakuwon Jati Tbk, tetapi jadwal, anggaran, progres, risiko, kontrak, vendor, personel, dan dokumen adalah simulasi, bukan data resmi perusahaan tersebut. Selama data demo terpasang, peringatan ini tampil otomatis di setiap halaman aplikasi, halaman login, halaman depan, dan di setiap dokumen contoh (PDF, XLSX, DOCX, JPG).

### Situs demo untuk pengunjung (akses Tamu)

Isi `PROMISE_GUEST_LOGIN=true` lalu `php artisan optimize`. Alamat utama menjadi **halaman depan** (masalah, 9 area pengetahuan PMBOK, fitur bergambar, daftar proyek demo, tanya jawab, tombol WhatsApp dari `PROMISE_WHATSAPP`), dan halaman login menampilkan tombol **Masuk sebagai Tamu**. Akun Tamu (`PROMISE_GUEST_EMAIL`, dibuat otomatis) hanya bisa melihat: tombol buat/ubah/hapus tersembunyi, menu Pengguna tertutup, dan setiap penyimpanan data ditolak sistem. Tamu tetap bisa membuka dan mengunduh dokumen demo. Untuk mematikan, ubah menjadi `false` lalu `php artisan optimize`.

**Mata uang tunggal Rupiah (IDR).** Semua nilai uang (anggaran, Earned Value, kontrak, risiko, perubahan, tarif SDM) disimpan dan ditampilkan dalam Rupiah: `Rp 350 jt`, `Rp 12,5 M` (miliar), `Rp 6,93 T` (triliun); grafik memakai satuan Rp miliar. Data demo disusun dari laporan tahunan dan dikonversi dengan kurs asumsi Rp 16.500 per US$. Saat memperbarui dari versi lama, `php artisan migrate --force` mengonversi data demo yang sudah terpasang sekali saja; data milik Anda sendiri tidak diubah.

**Data demo per perusahaan.** Ada tiga data demo yang terpisah: **PT Medco Energi Internasional Tbk**, **PT Bank SMBC Indonesia Tbk**, dan **PT Pakuwon Jati Tbk**. Di halaman login dan halaman depan, pengunjung mengklik kartu perusahaan dulu, lalu tombol **Masuk sebagai Tamu** aktif dan dasbor langsung menampilkan data perusahaan itu (proyek, portofolio, WBS, risiko, SDM, vendor, kontrak, dokumen). Tidak ada tampilan gabungan. Di bagian atas setiap halaman ada teks kecil nama perusahaan dengan tautan **Ganti ke** perusahaan lain. Bila memperbarui dari versi sebelumnya: `php artisan migrate --force`, lalu `php artisan db:seed --class=DemoPakuwonSeeder --force` untuk menambah data Pakuwon, lalu `php artisan optimize`. Untuk instalasi tanpa data demo, isi `PROMISE_COMPANY_NAME=Nama Perusahaan Anda` agar namanya tampil di setiap halaman.

## 9. Memperbarui program

```bash
cd ~/promise
php artisan down
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
php artisan up
```

Tanpa Terminal: upload zip versi baru, ekstrak menimpa folder `promise` (zip tidak berisi `.env`, dan isi `storage` termasuk dokumen tidak terhapus), lalu jalankan `migrate --force` dan `optimize` lewat cron sekali jalan. Selalu **backup database dan folder `storage/app/private`** dulu.

## 10. Masalah umum

| Gejala | Solusi |
|---|---|
| Error 500 tanpa keterangan | Lihat `promise/storage/logs/laravel-*.log`. Sementara boleh `APP_DEBUG=true`, lalu kembalikan ke `false`. |
| `Your PHP version … does not satisfy` | Ubah PHP domain ke 8.3, dan pastikan cron/Terminal juga memakai PHP 8.3. |
| `Class "Collator" not found` | Aktifkan ekstensi **intl**. |
| Unggah dokumen gagal / berputar terus | Naikkan `upload_max_filesize` dan `post_max_size` (25M) di *MultiPHP INI Editor*; pastikan ekstensi **fileinfo** aktif dan folder `storage` bisa ditulis. |
| `SQLSTATE[HY000] [1045] Access denied` | Periksa awalan `userku_` pada nama database/user dan ALL PRIVILEGES. |
| `No application encryption key` | Jalankan `php artisan key:generate --force`. |
| Halaman tanpa gaya / 404 untuk `/css/...` atau `/js/filament/...` | Document root belum ke `promise/public`, atau isi `public` belum tersalin lengkap. |
| Semua halaman selain beranda 404 | `.htaccess` dari `public` belum ikut tersalin. |
| `Permission denied` di `storage` | Izin folder `storage` dan `bootstrap/cache` 755. |
| `419 Page Expired` saat login | `APP_URL` harus sama persis dengan alamat yang dibuka. Tanpa SSL, hapus `SESSION_SECURE_COOKIE=true`. |
| Perubahan `.env` tidak berlaku | `php artisan optimize:clear` lalu `php artisan optimize`. |
