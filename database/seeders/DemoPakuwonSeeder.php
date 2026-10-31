<?php

namespace Database\Seeders;

use App\Models;

/**
 * Portofolio contoh ketiga: PT Pakuwon Jati Tbk (DATA DEMO).
 * Proyek disarikan dari Laporan Tahunan 2025 (Pakuwon Mall Semarang, extension Pakuwon Mall Phase 5,
 * Kota Kasablanka Phase 4, Pakuwon Nusantara IKN, hotel baru Bekasi & Surabaya, Park Shanghai);
 * anggaran (disusun setara US$, disimpan dalam Rupiah), jadwal rinci, progres, risiko, kontrak, vendor, personel, dan dokumen adalah simulasi.
 */
class DemoPakuwonSeeder extends DemoMedcoSeeder
{
    public const MARKER = 'PWN-SMG';

    protected const SEED = 2027;

    protected const PREFIX = 'pwn.';

    protected const COMPANY = 'pakuwon';

    protected function resourceCodePatterns(): array
    {
        return ['PWN-%'];
    }

    protected function people(): void
    {
        $users = [
            ['pmo', 'Lukas Wirawan', 'pmo', 'Head of Project Development Office'],
            ['pmo2', 'Felicia Hendrawan', 'pmo', 'Senior Manager Project Control'],
            ['direksi', 'Hendra Kusuma', 'eksekutif', 'Direktur Pengembangan (contoh)'],
            ['pm.mall', 'Andreas Gunawan', 'manajer', 'Project Director Superblok'],
            ['pm.smg', 'Rina Puspitasari', 'manajer', 'Project Manager Pakuwon Mall Semarang'],
            ['pm.jkt', 'Daniel Santoso', 'manajer', 'Project Manager Jakarta & Bekasi'],
            ['pm.hotel', 'Maya Christina', 'manajer', 'Hotel Development Manager'],
            ['pm.ikn', 'Rahmat Hidayat', 'manajer', 'Project Manager Pakuwon Nusantara'],
            ['pm.ritel', 'Vanessa Halim', 'manajer', 'Retail Asset Enhancement Manager'],
            ['pm.digital', 'Kurniawan Tjahjadi', 'manajer', 'Digital Transformation Lead'],
            ['planner', 'Bella Anggraini', 'anggota', 'Planning & Scheduling Engineer'],
            ['cost', 'Teguh Santoso', 'anggota', 'Quantity Surveyor & Cost Controller'],
        ];
        foreach ($users as [$key, $name, $role, $title]) {
            $this->users[$key] = Models\User::forceCreate([
                'name' => $name, 'email' => static::PREFIX.$key.'@contoh.local', 'password' => self::PASSWORD,
                'job_title' => $title, 'role' => $role, 'is_active' => true,
            ]);
        }

        $pool = [
            // [nama, disiplin, fungsi, tarif setara US$/jam, disimpan dalam Rupiah, user]
            ['Andreas Gunawan', 'Manajemen proyek konstruksi', 'Project Management', 90, 'pm.mall'],
            ['Rina Puspitasari', 'Manajemen proyek konstruksi', 'Project Management', 85, 'pm.smg'],
            ['Daniel Santoso', 'Manajemen proyek konstruksi', 'Project Management', 85, 'pm.jkt'],
            ['Maya Christina', 'Pengembangan & pra-pembukaan hotel', 'Hospitality', 80, 'pm.hotel'],
            ['Rahmat Hidayat', 'Manajemen proyek konstruksi', 'Project Management', 85, 'pm.ikn'],
            ['Vanessa Halim', 'Manajemen aset ritel', 'Retail Leasing', 75, 'pm.ritel'],
            ['Kurniawan Tjahjadi', 'Transformasi digital', 'Digital', 80, 'pm.digital'],
            ['Bella Anggraini', 'Perencanaan & penjadwalan', 'Project Control', 50, 'planner'],
            ['Teguh Santoso', 'Quantity surveyor & biaya', 'Project Control', 50, 'cost'],
            ['Yanuar Prasetyo', 'Perencanaan & penjadwalan', 'Project Control', 48, null],
            ['Ignatius Wibowo', 'Arsitektur & desain', 'Design & Engineering', 70, null],
            ['Shinta Kumala', 'Arsitektur & desain', 'Design & Engineering', 65, null],
            ['Robert Siahaan', 'Rekayasa struktur & geoteknik', 'Design & Engineering', 75, null],
            ['Wawan Setiadi', 'Rekayasa struktur & geoteknik', 'Design & Engineering', 70, null],
            ['Hendro Saputro', 'Rekayasa MEP', 'Design & Engineering', 68, null],
            ['Lilis Suryani', 'Rekayasa MEP', 'Design & Engineering', 65, null],
            ['Bambang Irawan', 'Manajer lapangan konstruksi', 'Construction', 62, null],
            ['Eko Wahyudi', 'Manajer lapangan konstruksi', 'Construction', 60, null],
            ['Taufan Hakim', 'Pengawas mutu (QA/QC)', 'Quality', 50, null],
            ['Ria Novitasari', 'Pengawas mutu (QA/QC)', 'Quality', 48, null],
            ['Sutrisno Adi', 'K3 konstruksi', 'HSE', 48, null],
            ['Dewi Kartika', 'Perizinan & legal', 'Legal & Perizinan', 65, null],
            ['Albert Tanuwijaya', 'Pengadaan & kontrak', 'Procurement', 55, null],
            ['Novita Sari', 'Pengadaan & kontrak', 'Procurement', 52, null],
            ['Stevanus Wijaya', 'Pemasaran & penjualan properti', 'Marketing', 55, null],
            ['Cynthia Liman', 'Leasing & tenant mix', 'Retail Leasing', 58, null],
            ['Benny Lukito', 'Pengembangan & pra-pembukaan hotel', 'Hospitality', 70, null],
            ['Ayu Widyastuti', 'Desain interior & FF&E', 'Design & Engineering', 60, null],
            ['Gregorius Ardi', 'Building management & fasilitas', 'Property Management', 55, null],
            ['Melinda Sutanto', 'Analis keuangan proyek', 'Finance', 60, null],
            ['Fajar Ramadhan', 'Sistem informasi & data', 'Digital', 60, null],
            ['Hartono Lim', 'Konsultan manajemen konstruksi', 'Mitra MK', 85, null],
            ['Kristina Dewi', 'Konsultan manajemen konstruksi', 'Mitra MK', 85, null],
        ];
        foreach ($pool as $i => [$name, $disc, $dept, $rate, $user]) {
            $this->resources[] = Models\Resource::create([
                'code' => sprintf('PWN-SDM-%03d', $i + 1), 'name' => $name, 'discipline' => $disc, 'department' => $dept,
                'type' => $i >= 31 ? 'kontraktor' : 'tenaga_kerja', 'cost_rate' => $rate, 'capacity_hours' => 40,
                'email' => static::PREFIX.strtolower(str_replace(' ', '.', $name)).'@contoh.local',
                'user_id' => $user ? $this->users[$user]->id : null,
            ]);
        }
        foreach ([['Tower crane (sewa)', 'Peralatan angkat'], ['Mesin bore pile', 'Peralatan fondasi'], ['Batching plant lapangan', 'Peralatan beton']] as $i => [$n, $d]) {
            Models\Resource::create(['code' => sprintf('PWN-ALT-%02d', $i + 1), 'name' => $n, 'discipline' => $d, 'department' => 'Peralatan', 'type' => 'peralatan', 'cost_rate' => 0, 'capacity_hours' => 168]);
        }
    }

    protected function vendors(): void
    {
        $list = [
            ['KUT', 'PT Konstruksi Utama Tangguh (fiktif)', 'Kontraktor utama gedung', 'Indonesia'],
            ['FDS', 'PT Fondasi Dalam Sentosa (fiktif)', 'Pekerjaan fondasi & bore pile', 'Indonesia'],
            ['MEP', 'PT Mekanikal Elektrikal Prima (fiktif)', 'Pekerjaan MEP', 'Indonesia'],
            ['FAS', 'PT Fasad Kaca Nusantara (fiktif)', 'Fasad & curtain wall', 'Indonesia'],
            ['LFT', 'PT Lift & Eskalator Andalan (fiktif)', 'Lift & eskalator', 'Indonesia'],
            ['ARS', 'Studio Arsitektur Ruang Kota (fiktif)', 'Konsultan arsitektur', 'Singapura'],
            ['MKO', 'PT Manajemen Konstruksi Optima (fiktif)', 'Konsultan manajemen konstruksi', 'Indonesia'],
            ['INT', 'PT Interior Hotel Elegan (fiktif)', 'Interior & FF&E hotel', 'Indonesia'],
            ['ENV', 'PT Kajian Lingkungan Mandiri (fiktif)', 'Konsultan AMDAL & andalalin', 'Indonesia'],
            ['DGT', 'PT Solusi Properti Digital (fiktif)', 'Aplikasi & smart building', 'Indonesia'],
            ['LSK', 'PT Lanskap Hijau Asri (fiktif)', 'Lanskap & infrastruktur kawasan', 'Indonesia'],
            ['QS', 'Quantity Surveying Partners (fiktif)', 'Konsultan biaya (QS)', 'Singapura'],
        ];
        foreach ($list as [$code, $name, $cat, $country]) {
            $this->vendors[$code] = Models\Vendor::create([
                'code' => 'VP-'.$code, 'name' => $name, 'category' => $cat, 'country' => $country, 'rating' => mt_rand(3, 5),
                'contact_name' => 'Bagian Komersial', 'email' => strtolower($code).'@vendor-properti.contoh',
            ]);
        }
    }

    /** @return array<string,Models\Portfolio> */
    protected function portfolios(): array
    {
        $out = [];
        foreach ([
            ['PWN-SUP', 'Portofolio Pengembangan Superblok & Mal', 'Memperkuat model kawasan terpadu (superblok) sebagai pilar pendapatan berulang: Pakuwon Mall Semarang, extension Pakuwon Mall Phase 5, Kota Kasablanka Phase 4, dan Pakuwon Nusantara IKN.', '#B4462B', 'Direktur Pengembangan', 'portofolio'],
            ['PWN-HTL', 'Portofolio Perhotelan', 'Menambah kamar hotel operasional di kawasan terpadu (Fairfield & Four Points Bekasi, Aloft Surabaya) dengan fokus segmen korporasi dan MICE.', '#8C6D1F', 'Direktur Perhotelan', 'portofolio'],
            ['PWN-RES', 'Portofolio Residensial & Township', 'Produk hunian yang relevan dengan pasar dan skema pembayaran fleksibel, memanfaatkan insentif PPN DTP.', '#2F6E8E', 'Direktur Pemasaran', 'portofolio'],
            ['PWN-OPT', 'Program Optimalisasi Aset & Digital', 'Upgrade mal, tenant mix experiential, aktivasi kawasan, serta modernisasi aset dan pemanfaatan teknologi digital.', '#5E7D3A', 'Direktur Utama', 'program'],
        ] as [$code, $name, $obj, $color, $owner, $type]) {
            $out[$code] = Models\Portfolio::create(['code' => $code, 'name' => $name, 'strategic_objective' => $obj, 'color' => $color, 'owner_name' => $owner, 'type' => $type]);
        }

        return $out;
    }

    protected function projects(): array
    {
        return [
            ['code' => 'PWN-SMG', 'portfolio' => 'PWN-SUP', 'unit' => 'properti', 'type' => 'mall', 'pm' => 'pm.smg', 'start' => '2025-08-01', 'finish' => '2029-06-30', 'budget' => 215e6, 'spi' => 0.94, 'cpi' => 0.98, 'status' => 'aktif', 'phase' => 'eksekusi', 'priority' => 'tinggi',
                'name' => 'Pakuwon Mall Semarang Tahap 1', 'location' => 'Semarang, Jawa Tengah', 'ptype' => 'Superblok: pusat perbelanjaan & convention hall',
                'desc' => 'Groundbreaking Agustus 2025, dijadwalkan beroperasi 2029. Tahap pertama mencakup area 12,7 hektar dengan pusat perbelanjaan NLA 134.000 m², piazza berkapasitas 5.000 orang, serta Exhibition dan Convention Hall (Laporan Tahunan 2025).',
                'objectives' => "Struktur atas mal selesai 2027\nPra-sewa (pre-leasing) ≥ 70% NLA sebelum pembukaan\nPembukaan mal 2029 sesuai baseline"],
            ['code' => 'PWN-PM5', 'portfolio' => 'PWN-SUP', 'unit' => 'properti', 'type' => 'mall', 'pm' => 'pm.mall', 'start' => '2025-03-01', 'finish' => '2027-09-30', 'budget' => 96e6, 'spi' => 0.83, 'cpi' => 0.9, 'status' => 'aktif', 'phase' => 'pengendalian', 'priority' => 'tinggi', 'slip' => 75,
                'name' => 'Extension Pakuwon Mall Phase 5', 'location' => 'Superblok Pakuwon Mall, Surabaya Barat', 'ptype' => 'Perluasan pusat perbelanjaan',
                'desc' => 'Perluasan Pakuwon Mall Surabaya. Laporan Tahunan menyebut proyek strategis ini menghadapi kompleksitas teknis, termasuk kondisi tanah yang membutuhkan pendekatan konstruksi lebih matang; pada tanggal status pekerjaan fondasi terlambat dari baseline.',
                'objectives' => "Fondasi & basement selesai tanpa insiden\nIntegrasi dengan mal eksisting tanpa penutupan\nPembukaan sebelum musim liburan 2027"],
            ['code' => 'PWN-KK4', 'portfolio' => 'PWN-SUP', 'unit' => 'properti', 'type' => 'tower', 'pm' => 'pm.jkt', 'start' => '2025-01-15', 'finish' => '2028-06-30', 'budget' => 128e6, 'spi' => 0.92, 'cpi' => 0.96, 'status' => 'aktif', 'phase' => 'eksekusi', 'priority' => 'tinggi',
                'name' => 'Kota Kasablanka Phase 4', 'location' => 'Superblok Kota Kasablanka, Jakarta Selatan', 'ptype' => 'Menara kondominium & komersial',
                'desc' => 'Pengembangan tahap 4 Superblok Kota Kasablanka di CBD Jakarta, melanjutkan serah terima Phase 2 (Angelo & Bella) dan Phase 3 (Chianti).',
                'objectives' => "Topping off menara 2027\nPenjualan unit ≥ 60% sebelum topping off\nSerah terima unit 2028"],
            ['code' => 'PWN-IKN', 'portfolio' => 'PWN-SUP', 'unit' => 'properti', 'type' => 'hotel', 'pm' => 'pm.ikn', 'start' => '2024-10-01', 'finish' => '2027-12-31', 'budget' => 82e6, 'spi' => 0.86, 'cpi' => 0.92, 'status' => 'aktif', 'phase' => 'pengendalian', 'priority' => 'sedang',
                'name' => 'Superblok Pakuwon Nusantara & Hotel Four Points IKN', 'location' => 'Ibu Kota Nusantara, Kalimantan Timur', 'ptype' => 'Superblok & hotel',
                'desc' => 'Groundbreaking Pakuwon Nusantara dan Hotel Four Points di IKN (PT Pakuwon Nusantara Abadi). Ritme pekerjaan disesuaikan dengan perkembangan kawasan inti pemerintahan.',
                'objectives' => "Hotel tahap 1 beroperasi 2027\nLogistik material ke lokasi terkendali\nKepatuhan rencana tata ruang IKN"],
            ['code' => 'PWN-FFB', 'portfolio' => 'PWN-HTL', 'unit' => 'perhotelan', 'type' => 'hotel', 'pm' => 'pm.hotel', 'start' => '2023-09-01', 'finish' => '2025-04-18', 'budget' => 27e6, 'spi' => 1, 'cpi' => 0.98, 'status' => 'selesai', 'phase' => 'penutupan', 'priority' => 'sedang',
                'name' => 'Hotel Fairfield Bekasi', 'location' => 'Superblok Pakuwon Mall Bekasi', 'ptype' => 'Pembangunan & pra-pembukaan hotel',
                'desc' => 'Hotel Marriott pertama di Bekasi dengan 166 kamar, bagian dari Pakuwon Mall Bekasi; diresmikan 18 April 2025 (PT Grama Pramesi Siddhi).',
                'objectives' => "Pembukaan 18 April 2025\nSertifikat laik fungsi terbit\nOkupansi tahun pertama sesuai rencana"],
            ['code' => 'PWN-ALO', 'portfolio' => 'PWN-HTL', 'unit' => 'perhotelan', 'type' => 'hotel', 'pm' => 'pm.hotel', 'start' => '2023-11-01', 'finish' => '2025-08-18', 'budget' => 33e6, 'spi' => 1, 'cpi' => 0.97, 'status' => 'selesai', 'phase' => 'penutupan', 'priority' => 'sedang',
                'name' => 'Hotel Aloft Surabaya Pakuwon City', 'location' => 'Superblok Pakuwon City Mall, Surabaya Timur', 'ptype' => 'Pembangunan & pra-pembukaan hotel',
                'desc' => 'Hotel Marriott International pertama di Surabaya Timur dengan 233 kamar dan fasilitas pertemuan, bagian dari Superblok Pakuwon City Mall; dibuka 18 Agustus 2025.',
                'objectives' => "Pembukaan Agustus 2025\nIntegrasi akses dengan Pakuwon City Mall\nBrand standard Marriott terpenuhi"],
            ['code' => 'PWN-FPB', 'portfolio' => 'PWN-HTL', 'unit' => 'perhotelan', 'type' => 'hotel', 'pm' => 'pm.jkt', 'start' => '2023-12-01', 'finish' => '2025-09-22', 'budget' => 38e6, 'spi' => 1, 'cpi' => 0.95, 'status' => 'selesai', 'phase' => 'penutupan', 'priority' => 'sedang',
                'name' => 'Hotel Four Points Bekasi', 'location' => 'Superblok Pakuwon Mall Bekasi', 'ptype' => 'Pembangunan & pra-pembukaan hotel',
                'desc' => 'Hotel 242 kamar dan suite, 2 ballroom dan 10 ruang pertemuan sebagai bagian dari Superblock Pakuwon Mall Bekasi; diluncurkan 22 September 2025.',
                'objectives' => "Pembukaan September 2025\nBallroom siap untuk pasar MICE\nBiaya FF&E dalam anggaran"],
            ['code' => 'PWN-PRB', 'portfolio' => 'PWN-RES', 'unit' => 'properti', 'type' => 'tower', 'pm' => 'pm.jkt', 'start' => '2025-02-01', 'finish' => '2027-12-31', 'budget' => 58e6, 'spi' => 0.97, 'cpi' => 1.01, 'status' => 'aktif', 'phase' => 'eksekusi', 'priority' => 'sedang',
                'name' => 'Menara Pakuwon Residence Bekasi', 'location' => 'Superblok Pakuwon Mall Bekasi', 'ptype' => 'Menara apartemen',
                'desc' => 'Pengembangan menara apartemen Pakuwon Residence Bekasi di kawasan terpadu Pakuwon Mall Bekasi, dengan skema pembayaran fleksibel dan insentif PPN DTP.',
                'objectives' => "Topping off 2027\nPenjualan unit ≥ 65%\nSerah terima tepat waktu"],
            ['code' => 'PWN-GPT', 'portfolio' => 'PWN-RES', 'unit' => 'properti', 'type' => 'township', 'pm' => 'pm.mall', 'start' => '2025-05-01', 'finish' => '2027-04-30', 'budget' => 41e6, 'spi' => 0.95, 'cpi' => 0.99, 'status' => 'aktif', 'phase' => 'eksekusi', 'priority' => 'sedang',
                'name' => 'Klaster Hunian Baru Grand Pakuwon Township', 'location' => 'Grand Pakuwon, Surabaya Barat', 'ptype' => 'Kawasan perumahan (township)',
                'desc' => 'Pengembangan klaster rumah tapak baru di Grand Pakuwon Township, Surabaya Barat, termasuk infrastruktur kawasan dan fasilitas umum.',
                'objectives' => "Infrastruktur klaster selesai 2026\nSerah terima rumah bertahap 2026–2027\nKepuasan pembeli ≥ 4/5"],
            ['code' => 'PWN-PSH', 'portfolio' => 'PWN-OPT', 'unit' => 'ritel', 'type' => 'renov', 'pm' => 'pm.ritel', 'start' => '2024-11-01', 'finish' => '2025-10-31', 'budget' => 8.5e6, 'spi' => 1, 'cpi' => 1.02, 'status' => 'selesai', 'phase' => 'penutupan', 'priority' => 'sedang',
                'name' => 'Park Shanghai & Revitalisasi Food Festival', 'location' => 'Superblok Pakuwon City Mall, Surabaya', 'ptype' => 'Aktivasi kawasan & fit-out ritel',
                'desc' => 'Pembukaan destinasi kuliner tematik Park Shanghai yang terintegrasi dengan Superblock Pakuwon City Mall serta revitalisasi Food Festival dengan konsep di dalam ruang.',
                'objectives' => "Pembukaan sesuai jadwal\nTraffic pengunjung naik ≥ 10%\nOkupansi tenant F&B ≥ 95%"],
            ['code' => 'PWN-UPG', 'portfolio' => 'PWN-OPT', 'unit' => 'ritel', 'type' => 'renov', 'pm' => 'pm.ritel', 'start' => '2025-09-01', 'finish' => '2026-12-31', 'budget' => 17e6, 'spi' => 0.88, 'cpi' => 0.94, 'status' => 'aktif', 'phase' => 'pengendalian', 'priority' => 'tinggi', 'slip' => 30,
                'name' => 'Upgrade Mal & Tenant Mix Experiential Tunjungan Plaza', 'location' => 'Superblok Tunjungan City, Surabaya', 'ptype' => 'Peremajaan aset ritel',
                'desc' => 'Upgrade mal dan penyesuaian tenant mix yang lebih experiential serta penambahan tenant pendorong pembelian impulsif, sesuai fokus prospek usaha 2026; pekerjaan dilakukan bertahap tanpa menutup mal.',
                'objectives' => "Renovasi zona bertahap tanpa menutup mal\nTenant experiential ≥ 25% NLA zona\nPendapatan sewa zona naik ≥ 8%"],
            ['code' => 'PWN-DIG', 'portfolio' => 'PWN-OPT', 'unit' => 'korporat', 'type' => 'program', 'pm' => 'pm.digital', 'start' => '2025-04-01', 'finish' => '2026-12-31', 'budget' => 6.2e6, 'spi' => 0.96, 'cpi' => 1.0, 'status' => 'aktif', 'phase' => 'eksekusi', 'priority' => 'sedang',
                'name' => 'Modernisasi Aset & Digitalisasi Operasional', 'location' => 'Kantor pusat & seluruh superblok', 'ptype' => 'Program transformasi digital',
                'desc' => 'Percepatan modernisasi aset dan pemanfaatan teknologi digital untuk efisiensi operasional (utilitas, building management) dan pengalaman pelanggan (aplikasi loyalitas mal & hotel).',
                'objectives' => "Building management system terpusat di 5 superblok\nAplikasi loyalitas terpadu mal & hotel\nBiaya utilitas turun 8%"],
        ];
    }

    protected function charter(array $s): array
    {
        return [
            'justification' => 'Selaras dengan strategi Pakuwon Jati: pengembangan kawasan terpadu (superblok) yang memperkuat pendapatan berulang, ekspansi selektif, dan optimalisasi aset operasional.',
            'success_criteria' => "Selesai sesuai baseline jadwal (SPI ≥ 0,95)\nBiaya dalam anggaran (CPI ≥ 0,95)\nNihil kecelakaan kerja fatal\nSertifikat laik fungsi & serah terima tepat waktu",
            'scope_statement' => 'Lingkup mencakup seluruh deliverable pada WBS level 1 dan paket kerja level 2, dari perizinan dan desain hingga serah terima ke pengelola gedung.',
            'out_of_scope' => 'Operasional gedung setelah masa pemeliharaan, fit-out internal tenant, dan pengembangan tahap berikutnya.',
            'assumptions' => "Perizinan (PBG, AMDAL, andalalin) terbit sesuai jadwal\nPendanaan internal & pra-penjualan sesuai rencana\nHarga material utama stabil",
            'constraints' => "Peraturan bangunan gedung & tata ruang daerah\nAktivitas mal eksisting tidak boleh terganggu\nJam kerja konstruksi dibatasi di kawasan padat",
        ];
    }

    protected function wbsOwners(): array
    {
        return ['Bella Anggraini', 'Ignatius Wibowo', 'Robert Siahaan', 'Hendro Saputro', 'Bambang Irawan', 'Eko Wahyudi', 'Taufan Hakim', 'Albert Tanuwijaya', 'Dewi Kartika', 'Cynthia Liman', 'Benny Lukito', 'Stevanus Wijaya'];
    }

    protected function wbsTemplate(string $type): array
    {
        return match ($type) {
            'mall' => [
                ['Manajemen Proyek & Perizinan', 0.05, 0.00, 1.00, 'Rencana proyek & izin lengkap', [
                    ['Piagam & baseline proyek', 0.20, 0.00, 0.04], ['AMDAL, andalalin & PBG', 0.40, 0.00, 0.14], ['PBG terbit', 0.0, 0.14, 0.14, 'ms' => true], ['Pengendalian proyek & pelaporan', 0.40, 0.04, 1.00]]],
                ['Desain & Rekayasa', 0.07, 0.00, 0.30, 'Gambar kerja disetujui', [
                    ['Desain skematik & pengembangan desain', 0.45, 0.00, 0.12], ['Gambar kerja struktur, arsitektur & MEP', 0.55, 0.08, 0.30]]],
                ['Pekerjaan Tanah & Fondasi', 0.16, 0.06, 0.34, 'Fondasi & basement selesai', [
                    ['Pematangan lahan & dewatering', 0.25, 0.06, 0.16], ['Bore pile & pile cap', 0.45, 0.12, 0.28], ['Basement & pelat dasar', 0.30, 0.22, 0.34]]],
                ['Struktur Bangunan', 0.26, 0.28, 0.64, 'Struktur atas selesai', [
                    ['Struktur lantai dasar–lantai 3', 0.55, 0.28, 0.50], ['Struktur atap & rangka baja', 0.25, 0.46, 0.62], ['Topping off', 0.0, 0.62, 0.62, 'ms' => true], ['Struktur piazza & convention hall', 0.20, 0.50, 0.64]]],
                ['Fasad, Arsitektur & MEP', 0.30, 0.50, 0.90, 'Bangunan siap uji', [
                    ['Fasad & curtain wall', 0.25, 0.54, 0.80], ['MEP: listrik, HVAC, plumbing, fire', 0.45, 0.52, 0.88], ['Lift & eskalator', 0.15, 0.66, 0.86], ['Finishing area publik', 0.15, 0.70, 0.90]]],
                ['Leasing, Uji Fungsi & Pembukaan', 0.16, 0.30, 1.00, 'Mal beroperasi', [
                    ['Pra-sewa & tenant mix', 0.35, 0.30, 0.92], ['Testing & commissioning', 0.20, 0.86, 0.95], ['Sertifikat laik fungsi (SLF)', 0.0, 0.95, 0.95, 'ms' => true], ['Fit-out tenant & pembukaan', 0.45, 0.88, 1.00]]],
            ],
            'tower' => [
                ['Manajemen Proyek & Perizinan', 0.05, 0.00, 1.00, 'Rencana proyek', [
                    ['Piagam, PBG & persetujuan lingkungan', 0.35, 0.00, 0.10], ['Pengendalian proyek & pelaporan', 0.65, 0.06, 1.00]]],
                ['Desain & Pemasaran', 0.10, 0.00, 0.30, 'Desain final & peluncuran penjualan', [
                    ['Desain arsitektur & unit tipikal', 0.40, 0.00, 0.14], ['Show unit & materi pemasaran', 0.25, 0.10, 0.22], ['Peluncuran penjualan', 0.0, 0.22, 0.22, 'ms' => true], ['Gambar kerja & tender', 0.35, 0.12, 0.30]]],
                ['Fondasi & Basement', 0.15, 0.20, 0.42, 'Basement selesai', [
                    ['Bore pile & dinding penahan tanah', 0.55, 0.20, 0.34], ['Galian & struktur basement', 0.45, 0.30, 0.42]]],
                ['Struktur Menara', 0.30, 0.38, 0.75, 'Topping off', [
                    ['Struktur podium', 0.30, 0.38, 0.52], ['Struktur menara tipikal', 0.70, 0.48, 0.74], ['Topping off', 0.0, 0.75, 0.75, 'ms' => true]]],
                ['Arsitektur, Fasad & MEP', 0.30, 0.55, 0.95, 'Unit siap serah terima', [
                    ['Fasad & jendela', 0.25, 0.60, 0.85], ['MEP & sistem gedung', 0.40, 0.58, 0.92], ['Finishing unit & area bersama', 0.35, 0.70, 0.95]]],
                ['Serah Terima', 0.10, 0.88, 1.00, 'Unit diserahterimakan', [
                    ['Inspeksi unit bersama pembeli', 0.40, 0.88, 0.97], ['Sertifikat laik fungsi (SLF)', 0.0, 0.92, 0.92, 'ms' => true], ['Serah terima bertahap & masa pemeliharaan', 0.60, 0.92, 1.00]]],
            ],
            'hotel' => [
                ['Manajemen Proyek & Perizinan', 0.05, 0.00, 1.00, 'Rencana proyek', [
                    ['Piagam & perjanjian operator hotel', 0.30, 0.00, 0.06], ['PBG & perizinan usaha hotel', 0.30, 0.02, 0.16], ['Pengendalian proyek & pelaporan', 0.40, 0.06, 1.00]]],
                ['Desain & Brand Standard', 0.08, 0.00, 0.25, 'Desain disetujui operator', [
                    ['Desain arsitektur & interior', 0.60, 0.00, 0.20], ['Persetujuan brand standard operator', 0.0, 0.22, 0.22, 'ms' => true], ['Gambar kerja & tender', 0.40, 0.12, 0.25]]],
                ['Struktur', 0.25, 0.18, 0.58, 'Topping off', [
                    ['Fondasi & basement', 0.40, 0.18, 0.34], ['Struktur atas', 0.60, 0.30, 0.56], ['Topping off', 0.0, 0.58, 0.58, 'ms' => true]]],
                ['MEP, Fasad & Interior', 0.37, 0.45, 0.90, 'Kamar & fasilitas siap', [
                    ['Fasad & atap', 0.15, 0.48, 0.70], ['MEP, sistem hotel & IT', 0.35, 0.46, 0.86], ['Interior kamar, ballroom & F&B', 0.35, 0.60, 0.90], ['Pengadaan & pemasangan FF&E/OS&E', 0.15, 0.72, 0.90]]],
                ['Pra-Pembukaan', 0.15, 0.70, 1.00, 'Hotel dibuka', [
                    ['Rekrutmen & pelatihan staf', 0.35, 0.70, 0.96], ['Testing, commissioning & SLF', 0.30, 0.86, 0.95], ['Soft opening', 0.0, 0.96, 0.96, 'ms' => true], ['Pemasaran pra-pembukaan', 0.35, 0.76, 1.00]]],
                ['Penutupan & Masa Pemeliharaan', 0.10, 0.92, 1.00, 'Serah terima ke operator', [
                    ['Daftar cacat (punch list)', 0.60, 0.92, 1.00], ['Serah terima ke operator', 0.40, 0.96, 1.00]]],
            ],
            'township' => [
                ['Manajemen Proyek & Perizinan', 0.06, 0.00, 1.00, 'Rencana proyek', [
                    ['Masterplan & site plan klaster', 0.40, 0.00, 0.10], ['Pengesahan site plan & PBG', 0.0, 0.10, 0.10, 'ms' => true], ['Pengendalian proyek', 0.60, 0.06, 1.00]]],
                ['Infrastruktur Kawasan', 0.30, 0.06, 0.55, 'Infrastruktur siap', [
                    ['Pematangan lahan & drainase', 0.40, 0.06, 0.28], ['Jalan, utilitas & PJU', 0.45, 0.22, 0.50], ['Lanskap & gerbang klaster', 0.15, 0.40, 0.55]]],
                ['Pembangunan Rumah', 0.40, 0.20, 0.90, 'Rumah siap huni', [
                    ['Rumah contoh', 0.08, 0.20, 0.30], ['Pembangunan rumah batch 1', 0.46, 0.28, 0.65], ['Pembangunan rumah batch 2', 0.46, 0.55, 0.90]]],
                ['Fasilitas Umum', 0.12, 0.40, 0.85, 'Fasum beroperasi', [
                    ['Clubhouse & taman', 0.60, 0.40, 0.80], ['Pos keamanan & sistem CCTV', 0.40, 0.60, 0.85]]],
                ['Penjualan & Serah Terima', 0.12, 0.10, 1.00, 'Rumah diserahterimakan', [
                    ['Pemasaran & akad KPR', 0.50, 0.10, 0.85], ['Serah terima bertahap', 0.50, 0.65, 1.00]]],
            ],
            'renov' => [
                ['Manajemen Proyek', 0.06, 0.00, 1.00, 'Rencana proyek', [
                    ['Piagam & studi konsep', 0.30, 0.00, 0.10], ['Pengendalian proyek & koordinasi tenant', 0.70, 0.05, 1.00]]],
                ['Konsep & Tenant Mix', 0.14, 0.00, 0.35, 'Tenant mix disepakati', [
                    ['Riset pengunjung & konsep experiential', 0.40, 0.00, 0.15], ['Negosiasi tenant & LOI', 0.60, 0.10, 0.35], ['Persetujuan konsep & tenant mix', 0.0, 0.35, 0.35, 'ms' => true]]],
                ['Desain', 0.10, 0.10, 0.35, 'Gambar kerja', [
                    ['Desain interior & wayfinding', 0.60, 0.10, 0.28], ['Gambar kerja & tender', 0.40, 0.22, 0.35]]],
                ['Pekerjaan Renovasi Bertahap', 0.45, 0.32, 0.88, 'Zona selesai direnovasi', [
                    ['Zona 1: bongkar & MEP', 0.35, 0.32, 0.55], ['Zona 2: interior & atrium', 0.40, 0.50, 0.78], ['Zona 3: area kuliner & akses', 0.25, 0.70, 0.88]]],
                ['Fit-out Tenant & Pembukaan', 0.25, 0.70, 1.00, 'Zona dibuka', [
                    ['Fit-out tenant', 0.60, 0.70, 0.94], ['Grand opening & event aktivasi', 0.0, 0.95, 0.95, 'ms' => true], ['Kampanye pemasaran & aktivasi', 0.40, 0.88, 1.00]]],
            ],
            default => [
                ['Mobilisasi & Tata Kelola Program', 0.10, 0.00, 1.00, 'Piagam program', [
                    ['Piagam program & roadmap digital', 0.35, 0.00, 0.08], ['PMO & pelaporan ke Direksi', 0.65, 0.06, 1.00]]],
                ['Building Management System Terpusat', 0.32, 0.05, 0.80, 'BMS terpusat beroperasi', [
                    ['Survei sistem gedung eksisting', 0.25, 0.05, 0.20], ['Integrasi BMS & meter utilitas', 0.55, 0.18, 0.70], ['Dasbor energi terpusat aktif', 0.0, 0.72, 0.72, 'ms' => true], ['Optimasi pemakaian energi', 0.20, 0.65, 0.80]]],
                ['Aplikasi Loyalitas Mal & Hotel', 0.32, 0.10, 0.85, 'Aplikasi rilis', [
                    ['Desain produk & UX', 0.25, 0.10, 0.28], ['Pengembangan aplikasi (sprint)', 0.55, 0.25, 0.70], ['Rilis aplikasi', 0.0, 0.72, 0.72, 'ms' => true], ['Integrasi tenant & hotel', 0.20, 0.60, 0.85]]],
                ['Data & Analitik Pengunjung', 0.14, 0.30, 0.90, 'Dasbor traffic & penjualan', [
                    ['People counting & data tenant', 0.55, 0.30, 0.70], ['Dasbor analitik manajemen', 0.45, 0.60, 0.90]]],
                ['Adopsi & Manajemen Perubahan', 0.12, 0.40, 1.00, 'Pengguna terlatih', [
                    ['Pelatihan pengelola gedung', 0.60, 0.40, 0.90], ['Evaluasi manfaat & serah terima', 0.40, 0.88, 1.00]]],
            ],
        };
    }

    protected function riskPool(string $type): array
    {
        $common = [
            ['Kenaikan harga material (baja, semen)', 'keuangan', 'Inflasi & kurs Rupiah', 3, 3, 'mitigasi', 'Kontrak pasokan harga tetap & eskalasi terbatas'],
            ['Keterlambatan perizinan bangunan', 'regulasi', 'Antrian proses PBG/SLF di daerah', 3, 4, 'mitigasi', 'Konsultasi awal dengan dinas & dokumen lengkap'],
            ['Kecelakaan kerja di ketinggian', 'hsse', 'Pekerjaan struktur & fasad', 2, 5, 'mitigasi', 'Izin kerja, safety net, inspeksi K3 harian'],
            ['Kinerja subkontraktor di bawah target', 'pengadaan', 'Kapasitas subkontraktor lokal', 3, 3, 'alihkan', 'Klausul denda keterlambatan & subkon cadangan'],
            ['Curah hujan tinggi menghambat pekerjaan', 'eksternal', 'Musim hujan', 4, 2, 'terima', 'Bufer jadwal & pekerjaan beratap didahulukan'],
            ['Permintaan pasar melemah', 'komersial', 'Suku bunga tinggi & kehati-hatian konsumen', 3, 4, 'mitigasi', 'Skema pembayaran fleksibel & insentif PPN DTP'],
        ];
        $specific = match ($type) {
            'mall' => [
                ['Kondisi tanah lunak memerlukan perkuatan', 'teknis', 'Hasil penyelidikan tanah', 4, 4, 'mitigasi', 'Uji beban tiang tambahan & desain fondasi ulang'],
                ['Pra-sewa tenant utama tertunda', 'komersial', 'Keputusan ekspansi tenant', 3, 4, 'mitigasi', 'Insentif fit-out & anchor tenant didahulukan'],
                ['Gangguan operasional mal eksisting', 'komersial', 'Pekerjaan di samping mal yang beroperasi', 3, 3, 'mitigasi', 'Pekerjaan bising malam hari & pagar pengaman'],
                ['Peluang permintaan MICE di convention hall', 'komersial', 'Pertumbuhan event regional', 3, 3, 'eksploitasi', 'Pra-jual paket event sebelum pembukaan'],
            ],
            'tower' => [
                ['Target penjualan unit tidak tercapai', 'komersial', 'Pasar residensial selektif', 3, 4, 'mitigasi', 'Produk relevan & cicilan bertahap'],
                ['Keterlambatan pasokan lift & fasad impor', 'pengadaan', 'Waktu produksi pabrikan', 3, 3, 'mitigasi', 'Pesan lebih awal & inspeksi pabrik'],
                ['Komplain pembeli saat serah terima', 'komersial', 'Mutu finishing unit', 3, 3, 'mitigasi', 'Inspeksi bersama & tim purnajual'],
            ],
            'hotel' => [
                ['Persetujuan brand standard operator berulang', 'teknis', 'Revisi desain oleh operator', 3, 3, 'mitigasi', 'Workshop desain bersama operator sejak awal'],
                ['FF&E impor terlambat', 'pengadaan', 'Pengapalan internasional', 3, 4, 'mitigasi', 'Jadwal pengadaan mundur dari tanggal soft opening'],
                ['Okupansi awal rendah', 'komersial', 'Pembatasan kegiatan pemerintah di hotel', 3, 3, 'mitigasi', 'Fokus segmen korporasi & MICE'],
                ['Logistik material ke lokasi terpencil', 'pengadaan', 'Akses jalan & pelabuhan', 3, 4, 'mitigasi', 'Gudang antara & jadwal pengiriman terkonsolidasi'],
            ],
            'township' => [
                ['Banjir saat pematangan lahan', 'eksternal', 'Elevasi rendah & drainase', 3, 3, 'mitigasi', 'Kolam retensi & drainase dibangun lebih dulu'],
                ['Pembatalan akad KPR', 'keuangan', 'Persetujuan bank', 3, 2, 'terima', 'Kerja sama dengan beberapa bank'],
            ],
            'renov' => [
                ['Tenant menolak relokasi sementara', 'komersial', 'Kontrak sewa berjalan', 3, 3, 'mitigasi', 'Kompensasi & area sementara'],
                ['Penurunan traffic selama renovasi', 'komersial', 'Area tertutup & kebisingan', 3, 3, 'mitigasi', 'Renovasi bertahap per zona & event pengalih'],
                ['Peluang tenant experiential baru', 'komersial', 'Tren ritel experiential', 3, 3, 'tingkatkan', 'Kurasi tenant & aktivasi event'],
            ],
            default => [
                ['Integrasi sistem gedung lama sulit', 'teknis', 'Protokol BMS berbeda antar gedung', 3, 3, 'mitigasi', 'Gateway protokol & uji per gedung'],
                ['Kebocoran data pelanggan aplikasi', 'siber', 'Data pribadi anggota loyalitas', 2, 5, 'mitigasi', 'Uji penetrasi & kepatuhan UU PDP'],
            ],
        };

        return array_merge($specific, $common);
    }

    protected function teamRoles(string $unit): array
    {
        return match ($unit) {
            'perhotelan' => [['Perencanaan & penjadwalan', 'Planner'], ['Pengembangan & pra-pembukaan hotel', 'Pre-opening Lead'], ['Desain interior & FF&E', 'Interior & FF&E'], ['Rekayasa MEP', 'MEP Engineer'], ['Manajer lapangan konstruksi', 'Site Manager'], ['Pengawas mutu (QA/QC)', 'QA/QC'], ['K3 konstruksi', 'HSE Officer'], ['Pengadaan & kontrak', 'Procurement']],
            'ritel' => [['Perencanaan & penjadwalan', 'Planner'], ['Leasing & tenant mix', 'Leasing Lead'], ['Arsitektur & desain', 'Design Lead'], ['Manajer lapangan konstruksi', 'Site Manager'], ['Building management & fasilitas', 'Facility Manager'], ['K3 konstruksi', 'HSE Officer']],
            'korporat' => [['Perencanaan & penjadwalan', 'PMO Analyst'], ['Sistem informasi & data', 'Data & Systems'], ['Building management & fasilitas', 'Facility Lead'], ['Analis keuangan proyek', 'Benefit Tracking']],
            default => [['Perencanaan & penjadwalan', 'Planner'], ['Quantity surveyor & biaya', 'Cost Controller'], ['Arsitektur & desain', 'Design Manager'], ['Rekayasa struktur & geoteknik', 'Structural Engineer'], ['Rekayasa MEP', 'MEP Engineer'], ['Manajer lapangan konstruksi', 'Site Manager'], ['Pengawas mutu (QA/QC)', 'QA/QC'], ['K3 konstruksi', 'HSE Officer'], ['Perizinan & legal', 'Permit & Legal'], ['Pemasaran & penjualan properti', 'Sales & Marketing']],
        };
    }

    protected function issueList(): array
    {
        return [
            ['Gambar kerja MEP belum terkoordinasi dengan struktur', 'sedang'], ['Akses truk material dibatasi jam operasional mal', 'sedang'],
            ['Hasil uji beton beberapa kolom di bawah spesifikasi', 'tinggi'], ['Pembayaran termin subkontraktor tertunda verifikasi', 'rendah'],
        ];
    }

    protected function changeList(): array
    {
        return [
            ['Perkuatan fondasi karena kondisi tanah', 'lingkup', 0.03, 35],
            ['Penyesuaian tata letak untuk tenant utama', 'lingkup', 0.012, 14],
            ['Peningkatan spesifikasi fasad hemat energi', 'mutu', 0.01, 10],
            ['Penyesuaian jadwal pembukaan ke musim liburan', 'jadwal', 0.0, 28],
            ['Penambahan konsultan biaya (QS) independen', 'pengadaan', 0.005, 0],
        ];
    }

    protected function qualityList(): array
    {
        return [
            ['Uji beban tiang (PDA/static loading test)', 'uji', 'SNI 8460:2017 geoteknik', 'Daya dukung ≥ desain'],
            ['Uji kuat tekan beton silinder', 'uji', 'SNI 2847:2019', 'f\'c sesuai spesifikasi'],
            ['Inspeksi pekerjaan struktur & pembesian', 'inspeksi', 'Gambar kerja & RKS', 'Tidak ada ketidaksesuaian mayor'],
            ['Inspeksi pemasangan fasad & uji kebocoran air', 'inspeksi', 'Spesifikasi fasad', 'Tidak ada kebocoran'],
            ['Testing & commissioning MEP', 'uji', 'Rencana T&C', 'Seluruh sistem lulus uji fungsi'],
            ['Audit kesiapan SLF', 'audit', 'Persyaratan laik fungsi', 'Seluruh dokumen & uji lengkap'],
        ];
    }

    protected function inspectors(): array
    {
        return ['Taufan Hakim', 'Ria Novitasari'];
    }

    protected function stakeholderList(string $unit): array
    {
        $common = [
            ['Direksi Pakuwon Jati', 'Direksi', 'PT Pakuwon Jati Tbk', 'internal', 5, 5, 'pendukung', 'Laporan bulanan & rapat pengembangan'],
            ['Pemerintah daerah (Dinas PUPR & DPMPTSP)', 'Kepala Dinas', 'Pemerintah daerah', 'regulator', 5, 3, 'netral', 'Konsultasi perizinan PBG & SLF'],
            ['Kontraktor utama', 'Project Director', 'Mitra konstruksi', 'pemasok', 3, 5, 'pendukung', 'Rapat koordinasi mingguan'],
            ['Warga sekitar proyek', 'Ketua RW', 'Masyarakat', 'masyarakat', 2, 4, 'netral', 'Sosialisasi & penanganan keluhan'],
        ];
        $specific = match ($unit) {
            'perhotelan' => [['Operator hotel (Marriott International)', 'Area Director', 'Operator hotel', 'mitra', 4, 5, 'pendukung', 'Persetujuan desain & rencana pra-pembukaan']],
            'ritel' => [['Tenant utama & anchor tenant', 'Leasing Manager', 'Tenant', 'pelanggan', 4, 5, 'netral', 'Koordinasi relokasi & jadwal fit-out'], ['Pengunjung mal', 'Komunitas pengunjung', 'Masyarakat', 'pelanggan', 2, 4, 'pendukung', 'Informasi renovasi & event']],
            'korporat' => [['Pengelola superblok', 'Building Manager', 'Pakuwon Jati', 'internal', 3, 5, 'pendukung', 'Uji coba & umpan balik sistem']],
            default => [['Pembeli unit & calon tenant', 'Perwakilan pembeli', 'Pelanggan', 'pelanggan', 3, 5, 'pendukung', 'Pembaruan progres pembangunan'], ['Bank mitra KPR/KPA', 'Kepala cabang', 'Perbankan', 'mitra', 3, 3, 'pendukung', 'Kerja sama pembiayaan pembeli']],
        };

        return [$common, $specific];
    }

    protected function contractSets(): array
    {
        return [
            'mall' => [['KUT', 'Kontrak utama struktur & arsitektur', 'lump_sum', 0.38], ['FDS', 'Pekerjaan bore pile & dewatering', 'unit_price', 0.12], ['MEP', 'Pekerjaan MEP', 'lump_sum', 0.18], ['FAS', 'Fasad & curtain wall', 'lump_sum', 0.06], ['MKO', 'Jasa manajemen konstruksi', 'tm', 0.03]],
            'tower' => [['KUT', 'Kontrak utama menara', 'lump_sum', 0.40], ['FDS', 'Fondasi & dinding penahan tanah', 'unit_price', 0.10], ['MEP', 'Pekerjaan MEP', 'lump_sum', 0.16], ['LFT', 'Pasokan & pemasangan lift', 'lump_sum', 0.05]],
            'hotel' => [['KUT', 'Kontrak utama gedung hotel', 'lump_sum', 0.36], ['MEP', 'MEP & sistem hotel', 'lump_sum', 0.18], ['INT', 'Interior & FF&E', 'lump_sum', 0.16], ['ARS', 'Konsultan arsitektur & interior', 'tm', 0.03]],
            'township' => [['LSK', 'Infrastruktur kawasan & lanskap', 'unit_price', 0.30], ['KUT', 'Pembangunan rumah', 'unit_price', 0.45], ['ENV', 'Dokumen lingkungan & andalalin', 'lump_sum', 0.01]],
            'renov' => [['KUT', 'Pekerjaan renovasi bertahap', 'unit_price', 0.45], ['MEP', 'Penyesuaian MEP', 'lump_sum', 0.15], ['ARS', 'Desain interior & wayfinding', 'lump_sum', 0.06]],
            'program' => [['DGT', 'Platform BMS & aplikasi loyalitas', 'lump_sum', 0.45], ['QS', 'Konsultan analisis manfaat', 'tm', 0.06]],
        ];
    }

    protected function reportAchievement(): string
    {
        return 'Pekerjaan paket kerja berjalan sesuai rencana kerja bulanan; tidak ada kecelakaan kerja fatal.';
    }

    protected function lessonList(string $code): array
    {
        return match ($code) {
            'PWN-FFB', 'PWN-FPB', 'PWN-ALO' => [
                ['pengadaan', 'positif', 'Pengadaan FF&E terkonsolidasi untuk beberapa hotel', 'Pembelian bersama Fairfield & Four Points Bekasi menurunkan harga satuan dan menyamakan jadwal kirim.', 'Konsolidasikan pengadaan FF&E untuk hotel berikutnya.'],
                ['waktu', 'perbaikan', 'Persetujuan brand standard perlu bufer', 'Revisi desain kamar oleh operator menambah waktu tender interior.', 'Kunci desain kamar contoh (mock-up room) sebelum tender.'],
            ],
            'PWN-PSH' => [
                ['komunikasi', 'positif', 'Aktivasi kawasan bersama tenant', 'Event pembukaan bersama tenant F&B menaikkan traffic sejak minggu pertama.', 'Libatkan tenant dalam kalender event sejak desain.'],
            ],
            default => [
                ['mutu', 'perbaikan', 'Penyelidikan tanah lebih rapat', 'Data tanah awal tidak cukup sehingga desain fondasi direvisi.', 'Tambah titik bor dan uji beban sebelum desain final.'],
                ['komunikasi', 'positif', 'Dasbor tunggal untuk rapat pengembangan', 'Satu sumber data (Promise) mempercepat keputusan Direksi.', 'Wajibkan pembaruan progres paket kerja sebelum rapat.'],
                ['sdm', 'perbaikan', 'Ketersediaan pengawas K3 di beberapa proyek', 'Pengawas K3 dibagi ke beberapa lokasi.', 'Tetapkan rasio pengawas K3 per jumlah pekerja.'],
            ],
        };
    }
}
