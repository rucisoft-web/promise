<?php

namespace App\Support;

/**
 * Isi tombol "Bantuan" per layar: konsep PMBOK, langkah, data yang diisi, contoh, dan tangkapan layar.
 * Kunci = segmen URL setelah /admin (lihat keyForPath()).
 */
class Help
{
    public static function keyForPath(string $path): string
    {
        $seg = explode('/', trim(preg_replace('#^admin/?#', '', trim($path, '/')), '/'));
        $first = $seg[0] ?? '';

        if ($first === '') {
            return 'dasbor';
        }
        if ($first === 'proyek' && isset($seg[1])) {
            return $seg[1] === 'create' || ($seg[2] ?? null) === 'edit' ? 'proyek-form' : 'ruang-proyek';
        }

        return isset(self::all()[$first]) ? $first : 'umum';
    }

    public static function get(string $key): array
    {
        return self::all()[$key] ?? self::all()['umum'];
    }

    /** Gambar yang benar-benar ada di public/images/bantuan */
    public static function shots(string $key): array
    {
        return collect(self::get($key)['shots'] ?? [])
            ->filter(fn ($caption, $file) => is_file(public_path('images/bantuan/'.$file)))
            ->all();
    }

    public static function all(): array
    {
        return [
            'umum' => [
                'title' => 'Bantuan Promise',
                'area' => 'Umum',
                'konsep' => [
                    'Promise mengikuti PMBOK® Guide: setiap proyek melewati lima kelompok proses (Inisiasi, Perencanaan, Eksekusi, Pemantauan & Pengendalian, Penutupan) dan dikelola melalui sembilan area pengetahuan yang menjadi grup menu di kiri.',
                ],
                'langkah' => ['Pilih menu di kiri sesuai area pengetahuan yang ingin dikelola.', 'Tekan tombol Bantuan di setiap layar untuk melihat penjelasan layar tersebut.'],
                'isian' => [],
                'contoh' => [],
                'shots' => [],
            ],

            'dasbor' => [
                'title' => 'Dasbor Portofolio',
                'area' => 'Umum · Integrasi',
                'konsep' => [
                    'Dasbor merangkum kesehatan seluruh proyek aktif per tanggal status (data date). Angka utama memakai Earned Value Management (EVM): PV (nilai rencana), EV (nilai hasil), AC (biaya aktual), SPI = EV/PV untuk jadwal, dan CPI = EV/AC untuk biaya.',
                    'Status kesehatan proyek: hijau bila SPI dan CPI ≥ 0,95, kuning bila ≥ 0,85, merah bila di bawahnya.',
                ],
                'langkah' => [
                    'Klik kartu angka (Proyek aktif, BAC, EV, SPI, CPI, Risiko tinggi) untuk membuka daftar atau halaman detailnya.',
                    'Klik bagian grafik: irisan fase membuka daftar proyek pada fase itu; batang unit bisnis membuka proyek unit tersebut; gelembung SPI × CPI atau batang Anggaran vs EAC membuka Ruang Proyek; batang kategori risiko membuka daftar risiko kategori itu; irisan kontrak membuka kontrak berstatus itu.',
                    'Klik angka di sel peta panas risiko untuk membuka risiko dengan probabilitas dan dampak tersebut, dan klik judul risiko atau milestone untuk membuka proyek/Gantt-nya.',
                ],
                'isian' => [],
                'contoh' => [
                    'SPI 0,81 dan CPI 0,92 berarti proyek baru menghasilkan 81% dari pekerjaan yang direncanakan, dan setiap Rp 1.000 biaya hanya menghasilkan nilai Rp 920.',
                ],
                'shots' => ['dasbor.jpg' => 'Dasbor portofolio: kartu angka, kurva S, dan grafik dapat diklik.'],
            ],

            'proyek' => [
                'title' => 'Proyek & Piagam',
                'area' => 'Integrasi',
                'konsep' => [
                    'Proyek adalah usaha sementara untuk menghasilkan produk, layanan, atau hasil yang unik. Piagam proyek (project charter) secara resmi mengesahkan proyek dan memberi wewenang kepada manajer proyek.',
                    'Daftar ini menampilkan kesehatan, % selesai, BAC, SPI, dan CPI setiap proyek yang dihitung otomatis dari WBS dan tanggal status.',
                ],
                'langkah' => [
                    'Klik baris proyek untuk membuka Ruang Proyek (ringkasan lengkap).',
                    'Pakai ikon corong untuk menyaring menurut portofolio, unit bisnis, fase, atau status.',
                    'Tombol Buat Proyek (Admin/PMO) membuka formulir piagam.',
                ],
                'isian' => [],
                'contoh' => [],
                'shots' => ['proyek.jpg' => 'Daftar proyek dengan indikator kesehatan dan EVM.'],
            ],

            'proyek-form' => [
                'title' => 'Formulir Proyek & Piagam',
                'area' => 'Integrasi (Develop Project Charter)',
                'konsep' => [
                    'Formulir ini adalah piagam proyek. Isi selengkap mungkin karena menjadi acuan lingkup, jadwal, dan anggaran. Baseline adalah rencana yang disetujui; perbandingan aktual terhadap baseline menghasilkan SPI, CPI, dan keterlambatan.',
                ],
                'langkah' => [
                    'Tab Identitas: kode, nama, portofolio, unit bisnis, manajer, sponsor, fase, status, prioritas.',
                    'Tab Piagam Proyek: justifikasi, tujuan terukur, kriteria keberhasilan, ruang lingkup, asumsi, kendala.',
                    'Tab Jadwal & Anggaran: tanggal mulai/selesai, baseline, tanggal status, anggaran, kontinjensi.',
                    'Tab Lampiran: unggah piagam bertanda tangan, studi kelayakan, atau dokumen lain.',
                    'Simpan, lalu lanjutkan menyusun WBS.',
                ],
                'isian' => [
                    ['Kode proyek', 'Singkat dan unik, mis. NTN-FRL. Dipakai di semua laporan.'],
                    ['Fase (kelompok proses)', 'Inisiasi, Perencanaan, Eksekusi, Pemantauan & Pengendalian, atau Penutupan.'],
                    ['Keselarasan strategis (1–5)', 'Seberapa kuat proyek mendukung sasaran strategis portofolio.'],
                    ['Tujuan terukur', 'Satu tujuan per baris, sebaiknya SMART.'],
                    ['Kriteria keberhasilan', 'Ukuran penerimaan, mis. SPI ≥ 0,95; nol kecelakaan kerja.'],
                    ['Di luar lingkup', 'Pekerjaan yang tegas tidak termasuk, untuk mencegah scope creep.'],
                    ['Tanggal status (data date)', 'Tanggal acuan perhitungan EVM. Kosong = hari ini.'],
                    ['Baseline mulai/selesai', 'Jadwal yang disetujui saat perencanaan; jangan diubah tanpa permintaan perubahan.'],
                    ['Anggaran & kontinjensi', 'Anggaran (BAC) dihitung ulang dari paket kerja WBS; kontinjensi adalah cadangan risiko.'],
                ],
                'contoh' => [
                    'Kode NTN-FRL · Pengembangan Lapangan Forel · Fase Eksekusi · Baseline 01/03/2024–30/09/2026 · Anggaran Rp 6,93 T · Kontinjensi 8%.',
                ],
                'shots' => ['proyek-form.jpg' => 'Formulir proyek dengan tab Identitas, Piagam, Jadwal & Anggaran, Lampiran.'],
            ],

            'ruang-proyek' => [
                'title' => 'Ruang Proyek',
                'area' => 'Integrasi (Monitor & Control Project Work)',
                'konsep' => [
                    'Ruang Proyek menyatukan seluruh informasi satu proyek: kartu EVM, Gantt, WBS, risiko & isu, perubahan, pengadaan, mutu, tim, dokumen, dan piagam. Kurva S di bawah membandingkan PV, EV, dan AC kumulatif.',
                ],
                'langkah' => [
                    'Pindah tab untuk melihat area pengetahuan yang berbeda.',
                    'Tautan "Kelola" di setiap tab membuka daftar data yang sudah tersaring untuk proyek ini.',
                    'Klik sel matriks risiko untuk membuka risiko pada sel tersebut.',
                    'Tab Dokumen menampilkan semua lampiran proyek; klik untuk pratinjau atau unduh.',
                ],
                'isian' => [],
                'contoh' => ['TCPI 1,12 berarti sisa pekerjaan harus dikerjakan 12% lebih efisien agar selesai sesuai anggaran.'],
                'shots' => ['ruang-proyek.jpg' => 'Ruang Proyek: KPI, tab area pengetahuan, dan Gantt.'],
            ],

            'portofolio' => [
                'title' => 'Portofolio & Program',
                'area' => 'Integrasi (manajemen portofolio)',
                'konsep' => [
                    'Portofolio adalah kumpulan proyek dan program yang dikelola bersama untuk mencapai sasaran strategis. Program adalah kelompok proyek terkait yang dikelola terkoordinasi untuk manfaat yang tidak diperoleh bila dikelola terpisah.',
                ],
                'langkah' => ['Buat portofolio per unit/strategi bisnis, lalu pilih portofolio saat membuat proyek.'],
                'isian' => [
                    ['Kode & Nama', 'Mis. HULU · Portofolio Hulu Migas.'],
                    ['Jenis', 'Portofolio atau Program.'],
                    ['Pemilik', 'Pejabat yang bertanggung jawab atas portofolio.'],
                    ['Sasaran strategis', 'Tujuan bisnis yang didukung portofolio.'],
                    ['Warna grafik', 'Warna yang dipakai di grafik dasbor.'],
                ],
                'contoh' => ['HULU · Portofolio · Pemilik: Direktur Operasi · Sasaran: menjaga produksi 150 MBOEPD.'],
                'shots' => ['portofolio.jpg' => 'Daftar portofolio dan program.'],
            ],

            'perubahan' => [
                'title' => 'Pengendalian Perubahan',
                'area' => 'Integrasi (Perform Integrated Change Control)',
                'konsep' => [
                    'Setiap perubahan lingkup, jadwal, biaya, atau mutu diajukan sebagai Change Request (CR), dianalisis dampaknya, lalu diputuskan oleh Change Control Board (CCB). Hanya CR yang disetujui boleh mengubah baseline.',
                ],
                'langkah' => ['Ajukan CR dengan uraian dan dampaknya.', 'CCB meninjau (status Dianalisis), lalu klik Setujui atau ubah status menjadi Ditolak.', 'Lampirkan memo analisis dampak bila ada.'],
                'isian' => [
                    ['Nomor CR', 'Mis. CR-007.'],
                    ['Kategori', 'Ruang lingkup, jadwal, biaya, mutu, atau pengadaan.'],
                    ['Perubahan yang diminta', 'Ringkas, satu kalimat.'],
                    ['Uraian & alasan', 'Latar belakang dan alternatif yang dipertimbangkan.'],
                    ['Dampak biaya / jadwal', 'Tambahan biaya (Rp) dan hari keterlambatan (negatif bila mempercepat).'],
                    ['Diputuskan oleh / catatan', 'Diisi CCB saat memutuskan.'],
                ],
                'contoh' => ['CR-004 · Penambahan 2 sumur sisipan · Dampak biaya Rp 297 M, jadwal +45 hari · Disetujui CCB 12/06/2026.'],
                'shots' => ['perubahan.jpg' => 'Daftar permintaan perubahan.', 'perubahan-form.jpg' => 'Formulir permintaan perubahan.'],
            ],

            'pelajaran' => [
                'title' => 'Pelajaran (Lessons Learned)',
                'area' => 'Integrasi (Manage Project Knowledge, Close Project)',
                'konsep' => ['Pelajaran mencatat apa yang berjalan baik dan apa yang perlu diperbaiki, agar proyek berikutnya tidak mengulang kesalahan yang sama.'],
                'langkah' => ['Catat pelajaran kapan saja, terutama setelah milestone dan saat penutupan proyek.'],
                'isian' => [
                    ['Area pengetahuan', 'Area PMBOK yang paling terkait.'],
                    ['Sifat', 'Positif (diulang) atau negatif (dihindari).'],
                    ['Rekomendasi', 'Tindakan konkret untuk proyek berikutnya.'],
                ],
                'contoh' => ['Pengadaan · Negatif · Pengiriman pipa terlambat 6 minggu karena antrean pabrikan → pesan barang lead-time panjang sebelum FID.'],
                'shots' => ['pelajaran.jpg' => 'Daftar pelajaran per area pengetahuan.'],
            ],

            'wbs' => [
                'title' => 'Work Breakdown Structure (WBS)',
                'area' => 'Ruang Lingkup (Create WBS)',
                'konsep' => [
                    'WBS memecah seluruh lingkup proyek secara hierarkis menjadi bagian yang lebih kecil. Level 1 adalah deliverable utama; level 2 adalah paket kerja yang dijadwalkan, dianggarkan, dan diukur progresnya. Aturan 100%: jumlah anak harus mencakup seluruh pekerjaan induknya.',
                    'Progres dan biaya level 1 dihitung otomatis dari paket kerjanya (rata-rata tertimbang biaya).',
                ],
                'langkah' => [
                    'Buat elemen level 1 dengan mengosongkan Induk.',
                    'Buat paket kerja level 2 dengan memilih induknya.',
                    'Isi jadwal, baseline, anggaran, dan penanggung jawab paket kerja.',
                    'Perbarui % selesai dan biaya aktual secara berkala; lampirkan foto progres atau berita acara.',
                ],
                'isian' => [
                    ['Kode WBS', '1, 2, 3 untuk level 1; 1.1, 1.2 untuk level 2.'],
                    ['Kamus WBS', 'Uraian pekerjaan, batasan, dan kriteria penerimaan paket kerja.'],
                    ['Anggaran (PV/BAC)', 'Biaya rencana paket kerja; jumlahnya menjadi BAC proyek.'],
                    ['Biaya aktual (AC)', 'Biaya yang sudah terjadi.'],
                    ['% selesai', 'Progres fisik; EV = % selesai × anggaran.'],
                    ['Milestone', 'Centang untuk peristiwa penting berdurasi nol (mulai = selesai).'],
                ],
                'contoh' => ['1 Rekayasa & Desain → 1.1 FEED, 1.2 Detail engineering; 2 Pengadaan → 2.1 Pipa & katup, 2.2 Kompresor.'],
                'shots' => ['wbs.jpg' => 'Daftar WBS dikelompokkan per proyek.', 'wbs-form.jpg' => 'Formulir elemen WBS.'],
            ],

            'jadwal' => [
                'title' => 'Gantt Chart',
                'area' => 'Waktu (Develop & Control Schedule)',
                'konsep' => [
                    'Gantt menampilkan setiap elemen WBS sebagai batang pada sumbu waktu. Batang tipis abu-abu adalah baseline, batang berwarna adalah jadwal saat ini dengan isian progres, ◆ adalah milestone, garis merah adalah tanggal status.',
                    'Panah menunjukkan keterkaitan antartugas: FS (Finish-to-Start), SS (Start-to-Start), FF (Finish-to-Finish), dan SF (Start-to-Finish), dengan lag (jeda) atau lead (percepatan) dalam hari.',
                ],
                'langkah' => ['Pilih proyek di atas grafik.', 'Pilih Level 1 untuk ringkasan atau Level 2 untuk paket kerja.', 'Keterkaitan merah berarti aturannya dilanggar jadwal saat ini dan perlu ditinjau.'],
                'isian' => [],
                'contoh' => ['1.2 Detail engineering → 2.1 Pengadaan pipa: SS + 30 hari, artinya pengadaan boleh mulai 30 hari setelah detail engineering dimulai.'],
                'shots' => ['jadwal.jpg' => 'Gantt chart dengan baseline, progres, dan panah keterkaitan.'],
            ],

            'keterkaitan-tugas' => [
                'title' => 'Keterkaitan Tugas (FS/SS/FF/SF)',
                'area' => 'Waktu (Sequence Activities)',
                'konsep' => [
                    'Metode Precedence Diagramming (PDM) menghubungkan pendahulu (predecessor) dan penerus (successor):',
                    'FS: penerus mulai setelah pendahulu selesai (paling umum). SS: penerus mulai setelah pendahulu mulai. FF: penerus selesai setelah pendahulu selesai. SF: penerus selesai setelah pendahulu mulai (jarang, mis. serah terima shift).',
                ],
                'langkah' => ['Pilih proyek, pendahulu, penerus, jenis, dan lag.', 'Periksa hasilnya di Gantt Chart.'],
                'isian' => [
                    ['Pendahulu / Penerus', 'Elemen WBS dari proyek yang sama; tidak boleh sama.'],
                    ['Jenis keterkaitan', 'FS, SS, FF, atau SF.'],
                    ['Lag (+) / lead (−)', 'Jeda dalam hari kalender; angka negatif berarti tumpang tindih.'],
                ],
                'contoh' => ['3.1 Konstruksi sipil → 3.2 Pemasangan mekanikal · FS + 7 hari (masa curing beton).'],
                'shots' => ['keterkaitan-tugas.jpg' => 'Daftar keterkaitan tugas.', 'keterkaitan-tugas-form.jpg' => 'Formulir keterkaitan tugas.'],
            ],

            'earned-value' => [
                'title' => 'Earned Value (EVM)',
                'area' => 'Biaya (Control Costs)',
                'konsep' => [
                    'PV (Planned Value) = anggaran pekerjaan yang dijadwalkan sampai tanggal status. EV (Earned Value) = anggaran pekerjaan yang benar-benar selesai. AC (Actual Cost) = biaya aktual.',
                    'SV = EV − PV, CV = EV − AC, SPI = EV/PV, CPI = EV/AC, EAC = BAC/CPI (perkiraan biaya akhir), ETC = EAC − AC, VAC = BAC − EAC, TCPI = (BAC − EV)/(BAC − AC).',
                ],
                'langkah' => ['Baca tabel per proyek; nilai merah perlu tindakan korektif.', 'Klik kode proyek untuk membuka Ruang Proyek.'],
                'isian' => [],
                'contoh' => ['BAC 100, PV 50, EV 40, AC 50 → SPI 0,80; CPI 0,80; EAC 125; VAC −25.'],
                'shots' => ['earned-value.jpg' => 'Tabel Earned Value seluruh proyek.'],
            ],

            'mutu' => [
                'title' => 'Pemeriksaan Mutu',
                'area' => 'Mutu (Plan, Manage & Control Quality)',
                'konsep' => ['Pemeriksaan mutu memastikan deliverable memenuhi standar dan kriteria penerimaan. Catat inspeksi, uji, audit, dan tinjauan beserta hasil dan jumlah temuan.'],
                'langkah' => ['Rencanakan pemeriksaan per paket kerja.', 'Setelah dilaksanakan, isi tanggal, hasil, jumlah temuan, dan lampirkan laporan/foto.'],
                'isian' => [
                    ['Jenis', 'Inspeksi, pengujian, audit mutu, tinjauan desain, atau FAT/SAT.'],
                    ['Standar acuan', 'Mis. API 5L, ASME B31.3, SNI.'],
                    ['Kriteria penerimaan', 'Batas lulus yang terukur.'],
                    ['Hasil', 'Terjadwal, Lulus, Lulus bersyarat, atau Gagal.'],
                ],
                'contoh' => ['QC-012 · Uji hidrostatik pipa 12" · ASME B31.3 · tahan 1,5× tekanan desain 4 jam · Lulus · 0 temuan.'],
                'shots' => ['mutu.jpg' => 'Daftar pemeriksaan mutu.', 'mutu-form.jpg' => 'Formulir pemeriksaan mutu.'],
            ],

            'sumber-daya' => [
                'title' => 'Sumber Daya',
                'area' => 'Sumber Daya Manusia (Plan & Acquire Resources)',
                'konsep' => ['Kelompok sumber daya berisi personel dan peralatan beserta tarif dan kapasitas mingguan. Data ini dipakai untuk penugasan, lembar waktu, dan analisis kapasitas.'],
                'langkah' => ['Daftarkan personel/peralatan.', 'Tautkan ke akun pengguna agar personel bisa mengisi lembar waktu sendiri.'],
                'isian' => [
                    ['Jenis', 'Tenaga kerja, tenaga kontrak, atau peralatan.'],
                    ['Tarif per jam', 'Untuk menghitung biaya tenaga kerja.'],
                    ['Kapasitas per minggu', 'Jam tersedia per minggu, mis. 40.'],
                ],
                'contoh' => ['RS-014 · Budi Santoso · Tenaga kerja · Piping Engineer · Tarif Rp 1.402.500/jam · 40 jam/minggu.'],
                'shots' => ['sumber-daya.jpg' => 'Daftar sumber daya.'],
            ],

            'penugasan' => [
                'title' => 'Penugasan',
                'area' => 'Sumber Daya Manusia (Acquire & Develop Team)',
                'konsep' => ['Penugasan menempatkan sumber daya di proyek dengan peran, alokasi, dan periode tertentu. Gabungan penugasan membentuk tim proyek.'],
                'langkah' => ['Pilih proyek dan sumber daya.', 'Isi peran, alokasi (%), periode, dan jam rencana.'],
                'isian' => [
                    ['Peran di proyek', 'Mis. Lead Engineer, Planner, HSE Officer.'],
                    ['Alokasi', 'Persentase waktu untuk proyek ini (100% = penuh).'],
                    ['Jam rencana / aktual', 'Jam aktual diperbarui dari lembar waktu.'],
                ],
                'contoh' => ['Budi Santoso · NTN-FRL · Lead Piping · 75% · 01/01–31/12/2026 · 1.560 jam rencana.'],
                'shots' => ['penugasan.jpg' => 'Daftar penugasan tim proyek.'],
            ],

            'lembar-waktu' => [
                'title' => 'Lembar Waktu (Timesheet)',
                'area' => 'Sumber Daya Manusia · Biaya',
                'konsep' => ['Lembar waktu mencatat jam kerja mingguan per proyek. Setelah disetujui, jam menjadi dasar biaya aktual tenaga kerja dan analisis kapasitas.'],
                'langkah' => ['Isi lembar waktu tiap minggu (Senin sebagai awal minggu).', 'Ajukan; manajer proyek/PMO menyetujui dengan tombol Setujui.'],
                'isian' => [
                    ['Minggu mulai (Senin)', 'Tanggal Senin minggu tersebut.'],
                    ['Jam kerja', 'Total jam di proyek itu dalam minggu tersebut.'],
                    ['Status', 'Draf, Diajukan, Disetujui, atau Ditolak.'],
                    ['Uraian pekerjaan', 'Ringkas pekerjaan yang dilakukan.'],
                ],
                'contoh' => ['Minggu 26/10/2026 · NTN-FRL · 38 jam · "Review isometrik area 200, rapat koordinasi vendor".'],
                'shots' => ['lembar-waktu.jpg' => 'Daftar lembar waktu dengan status persetujuan.'],
            ],

            'kapasitas' => [
                'title' => 'Kapasitas SDM',
                'area' => 'Sumber Daya Manusia (Control Resources)',
                'konsep' => ['Peta panas kapasitas membandingkan jam terpakai dengan kapasitas setiap sumber daya per minggu. Hijau = wajar, kuning = mendekati penuh, merah = kelebihan beban (over-allocation).'],
                'langkah' => ['Cari sel merah, lalu seimbangkan beban dengan memindah penugasan atau menggeser jadwal (resource leveling).'],
                'isian' => [],
                'contoh' => ['Kapasitas 40 jam, terpakai 52 jam → 130% (merah).'],
                'shots' => ['kapasitas.jpg' => 'Peta panas kapasitas mingguan.'],
            ],

            'pemangku-kepentingan' => [
                'title' => 'Pemangku Kepentingan',
                'area' => 'Komunikasi · Pemangku Kepentingan',
                'konsep' => ['Register pemangku kepentingan mencatat siapa yang terpengaruh atau memengaruhi proyek, seberapa besar pengaruh dan kepentingannya, serta strategi keterlibatannya (grid pengaruh/kepentingan).'],
                'langkah' => ['Identifikasi pihak sejak inisiasi.', 'Nilai pengaruh dan kepentingan 1–5, lalu tentukan strategi.'],
                'isian' => [
                    ['Pengaruh / Kepentingan (1–5)', 'Tinggi-tinggi = kelola secara dekat; rendah-rendah = pantau.'],
                    ['Sikap', 'Pendukung, netral, atau penentang.'],
                    ['Strategi keterlibatan', 'Mis. rapat bulanan dengan direksi, konsultasi publik.'],
                ],
                'contoh' => ['SKK Migas · Regulator · Pengaruh 5 · Kepentingan 4 · Netral · Laporan bulanan & rapat triwulanan.'],
                'shots' => ['pemangku-kepentingan.jpg' => 'Register pemangku kepentingan.'],
            ],

            'rencana-komunikasi' => [
                'title' => 'Rencana Komunikasi',
                'area' => 'Komunikasi (Plan Communications Management)',
                'konsep' => ['Rencana komunikasi menetapkan informasi apa, untuk siapa, kapan, lewat media apa, dan siapa penanggung jawabnya.'],
                'langkah' => ['Tambahkan setiap kegiatan komunikasi rutin proyek.'],
                'isian' => [
                    ['Jenis', 'Rapat, laporan, presentasi, surat resmi, atau dasbor daring.'],
                    ['Frekuensi', 'Harian, mingguan, dua mingguan, bulanan, triwulanan, atau saat milestone.'],
                    ['Audiens & media', 'Mis. Direksi · rapat daring.'],
                ],
                'contoh' => ['Laporan status bulanan · Laporan · Bulanan · Sponsor & PMO · Promise + surel · PM.'],
                'shots' => ['rencana-komunikasi.jpg' => 'Daftar rencana komunikasi.'],
            ],

            'laporan-status' => [
                'title' => 'Laporan Status',
                'area' => 'Komunikasi (Monitor Communications) · Integrasi',
                'konsep' => ['Laporan status bulanan merangkum kinerja proyek: status umum, jadwal, biaya, dan lingkup (hijau/kuning/merah), progres rencana vs aktual, serta PV/EV/AC periode itu. Kurva S portofolio dibentuk dari laporan ini.'],
                'langkah' => ['Buat satu laporan per proyek per akhir bulan.', 'Isi status RAG, progres, ringkasan, capaian, dan rencana berikutnya; lampirkan laporan PDF bila ada.'],
                'isian' => [
                    ['Periode (akhir bulan)', 'Mis. 31/10/2026; satu proyek hanya satu laporan per periode.'],
                    ['Status umum / Jadwal / Biaya / Lingkup', 'Hijau, Kuning, atau Merah.'],
                    ['% rencana / % aktual', 'Progres kumulatif.'],
                    ['PV / EV / AC', 'Nilai kumulatif sampai periode itu.'],
                ],
                'contoh' => ['Okt 2026 · Kuning · rencana 64% / aktual 58% · "Kedatangan kompresor mundur 3 minggu; mitigasi: percepat instalasi pipa".'],
                'shots' => ['laporan-status.jpg' => 'Daftar laporan status bulanan.'],
            ],

            'risiko' => [
                'title' => 'Register Risiko',
                'area' => 'Risiko (Identify, Analyze, Plan & Monitor Risks)',
                'konsep' => [
                    'Risiko adalah peristiwa tidak pasti yang bila terjadi berdampak pada tujuan proyek. Skor = probabilitas × dampak (skala 1–5): ≥ 15 tinggi, 8–14 sedang, di bawahnya rendah.',
                    'Strategi respons ancaman: hindari, alihkan, mitigasi, terima, atau eskalasi; untuk peluang: eksploitasi atau tingkatkan.',
                ],
                'langkah' => ['Identifikasi risiko dengan format "karena (penyebab), mungkin terjadi (risiko), berakibat (dampak)".', 'Nilai probabilitas dan dampak, pilih strategi, tetapkan pemilik dan tenggat.', 'Pantau dan perbarui status; risiko yang terjadi dicatat sebagai isu.'],
                'isian' => [
                    ['Kategori', 'Teknis, HSSE, komersial, regulasi & perizinan, pengadaan & rantai pasok, eksternal/alam, organisasi & SDM, keuangan.'],
                    ['Penyebab / Dampak bila terjadi', 'Pisahkan sebab dan akibat agar respons tepat.'],
                    ['Probabilitas & Dampak (1–5)', 'Skor dihitung otomatis.'],
                    ['Eksposur biaya', 'Perkiraan kerugian (Rp) bila risiko terjadi; dipakai untuk cadangan kontinjensi.'],
                    ['Pemilik risiko', 'Orang yang memantau dan menjalankan respons.'],
                    ['Rencana respons', 'Tindakan konkret, biaya, dan pemicu (trigger).'],
                ],
                'contoh' => ['Karena cuaca musim barat, pemasangan jacket mungkin tertunda, berakibat jadwal mundur 30 hari · P4 × D4 = 16 (tinggi) · Mitigasi: geser jendela instalasi ke Maret–Oktober.'],
                'shots' => ['risiko.jpg' => 'Register risiko berurutan skor.', 'risiko-form.jpg' => 'Formulir risiko dengan skor otomatis.'],
            ],

            'isu' => [
                'title' => 'Log Isu',
                'area' => 'Risiko · Integrasi',
                'konsep' => ['Isu adalah masalah yang sedang terjadi dan perlu diselesaikan, berbeda dengan risiko yang belum terjadi. Isu bisa berasal dari risiko yang terwujud.'],
                'langkah' => ['Catat isu, tentukan prioritas, penanggung jawab, dan tenggat.', 'Isi penyelesaian lalu ubah status menjadi Selesai.'],
                'isian' => [
                    ['Prioritas', 'Tinggi, sedang, atau rendah.'],
                    ['Risiko asal', 'Opsional: risiko yang menjadi sumber isu.'],
                    ['Penyelesaian', 'Tindakan yang diambil dan hasilnya.'],
                ],
                'contoh' => ['IS-009 · Tinggi · Izin lingkungan revisi belum terbit · PIC Legal · tenggat 15/11/2026.'],
                'shots' => ['isu.jpg' => 'Log isu proyek.'],
            ],

            'kontrak' => [
                'title' => 'Kontrak',
                'area' => 'Pengadaan (Conduct & Control Procurements)',
                'konsep' => ['Kontrak mencatat paket pengadaan dengan vendor: jenis kontrak, metode pengadaan, nilai, jangka waktu, status, dan progres pekerjaan.'],
                'langkah' => ['Buat paket kontrak sejak perencanaan pengadaan.', 'Perbarui status dan progres; lampirkan kontrak, amandemen, dan berita acara.'],
                'isian' => [
                    ['Jenis kontrak', 'Lump sum, harga satuan, cost plus fee, EPCI terima jadi, atau time & material.'],
                    ['Metode pengadaan', 'Tender terbuka, tender terbatas, seleksi langsung, atau penunjukan langsung.'],
                    ['Nilai', 'Nilai kontrak (Rp).'],
                    ['Progres pekerjaan', '% fisik yang diselesaikan vendor.'],
                ],
                'contoh' => ['NTN-FRL/EPC-01 · PT Contoh Rekayasa · EPC · Tender terbuka · Rp 3,47 T · 2024–2026 · Berjalan 62%.'],
                'shots' => ['kontrak.jpg' => 'Daftar kontrak.', 'kontrak-form.jpg' => 'Formulir kontrak.'],
            ],

            'vendor' => [
                'title' => 'Vendor',
                'area' => 'Pengadaan',
                'konsep' => ['Daftar vendor menyimpan penyedia barang/jasa beserta bidang dan penilaian kinerjanya, untuk dipakai berulang di kontrak.'],
                'langkah' => ['Tambahkan vendor sebelum membuat kontrak.', 'Perbarui penilaian kinerja setelah evaluasi.'],
                'isian' => [['Bidang', 'Mis. EPC, pengeboran, peralatan, konsultan.'], ['Penilaian kinerja', '1–5 bintang dari evaluasi pekerjaan sebelumnya.']],
                'contoh' => ['VND-03 · PT Contoh Drilling · Pengeboran · Indonesia · Penilaian 4.'],
                'shots' => ['vendor.jpg' => 'Daftar vendor.'],
            ],

            'dokumen' => [
                'title' => 'Pusat Dokumen',
                'area' => 'Integrasi (Manage Project Knowledge) · Komunikasi',
                'konsep' => ['Pusat Dokumen adalah arsip digital semua dokumen proyek: piagam, kontrak, gambar, laporan, notulen, foto. Dokumen bisa melekat ke proyek atau ke data tertentu (tugas WBS, risiko, isu, perubahan, mutu, kontrak, laporan status). Berkas disimpan privat dan hanya bisa dibuka pengguna yang login.'],
                'langkah' => ['Klik tombol tambah dokumen, pilih proyek dan data yang dilampiri, isi judul, nomor, revisi, kategori, lalu pilih berkas.', 'Pakai saringan proyek, kategori, "melekat pada", dan jenis berkas untuk mencari.', 'Klik ikon mata untuk pratinjau PDF/gambar, atau unduh.'],
                'isian' => [
                    ['Lampirkan ke', 'Kosongkan untuk dokumen umum proyek, atau pilih tugas/risiko/kontrak tertentu.'],
                    ['Nomor dokumen & revisi', 'Mis. NTN-FRL-PRC-0012 · Rev. B.'],
                    ['Kategori', 'Gambar teknik, spesifikasi, laporan, foto lapangan, kontrak/legal, notulen, sertifikat/izin, lainnya.'],
                    ['Berkas', 'PDF, DOC/DOCX, XLS/XLSX, PPT/PPTX, PNG, JPG; maks. 20 MB.'],
                ],
                'contoh' => ['Notulen rapat kick-off.docx · kategori Notulen · melekat pada proyek NTN-FRL.'],
                'shots' => ['dokumen.jpg' => 'Pusat Dokumen dengan saringan dan pratinjau.', 'dokumen-form.jpg' => 'Formulir unggah dokumen.'],
            ],

            'pengguna' => [
                'title' => 'Pengguna',
                'area' => 'Administrasi',
                'konsep' => ['Hak akses berdasarkan peran: Administrator (semua), PMO (semua proyek), Manajer Proyek (hanya proyek yang dikelolanya), Anggota Tim (lembar waktu dan lihat), Eksekutif (lihat semua), Tamu (demo, hanya lihat).'],
                'langkah' => ['Buat akun, pilih peran, dan aktifkan.', 'Untuk manajer proyek, pilih namanya di kolom Manajer pada proyek.'],
                'isian' => [['Peran', 'Menentukan menu dan tombol yang tampil.'], ['Kata sandi', 'Minimal 8 karakter; kosongkan saat mengubah bila tidak diganti.']],
                'contoh' => ['Rina Wijaya · rina@perusahaan.co.id · Manajer Proyek · Aktif.'],
                'shots' => ['pengguna.jpg' => 'Daftar pengguna dan perannya.'],
            ],

            'panduan' => [
                'title' => 'Panduan PMBOK',
                'area' => 'Umum',
                'konsep' => ['Ringkasan lima kelompok proses dan sembilan area pengetahuan PMBOK beserta menu Promise yang terkait, serta panduan lengkap setiap layar.'],
                'langkah' => ['Baca dari atas untuk memahami alur, lalu gunakan daftar panduan layar sebagai rujukan.'],
                'isian' => [],
                'contoh' => [],
                'shots' => [],
            ],
        ];
    }
}
