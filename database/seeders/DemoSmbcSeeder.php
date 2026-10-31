<?php

namespace Database\Seeders;

use App\Models;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Portofolio contoh kedua: PT Bank SMBC Indonesia Tbk (DATA DEMO).
 * Inisiatif disarikan dari Laporan Tahunan 2025 (pengembangan TI, Jenius, TOUCHBIZ, PIKK,
 * keuangan berkelanjutan, transformasi HR); anggaran (setara US$), jadwal rinci, progres,
 * risiko, kontrak, vendor, personel, dan dokumen adalah simulasi.
 */
class DemoSmbcSeeder extends DemoMedcoSeeder
{
    public const MARKER = 'SMB-CBS';

    protected const SEED = 2026;

    protected const PREFIX = 'smbc.';

    public function run(): void
    {
        if (Models\Project::where('code', static::MARKER)->exists()) {
            return;
        }
        mt_srand(static::SEED);
        $this->statusDate = Carbon::parse(self::STATUS_DATE);

        DB::transaction(function () {
            $this->people();
            $this->vendors();
            $portfolios = $this->portfolios();
            foreach ($this->projects() as $spec) {
                $this->project($spec, $portfolios[$spec['portfolio']]);
            }
        });
    }

    protected function people(): void
    {
        $users = [
            ['pmo', 'Wahyuni Kartikasari', 'pmo', 'Head of Enterprise PMO'],
            ['itsp', 'Benny Hartanto', 'pmo', 'IT Strategic Planning & Operations Control Head'],
            ['direksi', 'Taufan Wijaya', 'eksekutif', 'Direktur Operasional & Teknologi (contoh)'],
            ['pm.core', 'Gilang Ramadhan', 'manajer', 'Program Manager Core Banking'],
            ['pm.digital', 'Clara Natalia', 'manajer', 'Project Manager Digital Banking'],
            ['pm.infra', 'Hadi Susanto', 'manajer', 'Project Manager Infrastruktur TI'],
            ['pm.data', 'Laras Ayuningtyas', 'manajer', 'Project Manager Data Management'],
            ['pm.korp', 'Reza Firmansyah', 'manajer', 'Project Manager Corporate Banking'],
            ['pm.esg', 'Anindya Paramita', 'manajer', 'Sustainability Program Lead'],
            ['pm.hr', 'Yohanes Siregar', 'manajer', 'HR Technology Lead'],
            ['planner', 'Sekar Arum', 'anggota', 'PMO Analyst'],
            ['cost', 'Dimas Prakoso', 'anggota', 'IT Budget Controller'],
        ];
        foreach ($users as [$key, $name, $role, $title]) {
            $this->users[$key] = Models\User::forceCreate([
                'name' => $name, 'email' => static::PREFIX.$key.'@contoh.local', 'password' => self::PASSWORD,
                'job_title' => $title, 'role' => $role, 'is_active' => true,
            ]);
        }

        $pool = [
            // [nama, disiplin, fungsi, tarif US$/jam (setara), user]
            ['Gilang Ramadhan', 'Manajemen program TI', 'Project Management', 95, 'pm.core'],
            ['Clara Natalia', 'Manajemen proyek digital', 'Project Management', 90, 'pm.digital'],
            ['Hadi Susanto', 'Manajemen proyek infrastruktur', 'Project Management', 88, 'pm.infra'],
            ['Laras Ayuningtyas', 'Manajemen proyek data', 'Project Management', 88, 'pm.data'],
            ['Reza Firmansyah', 'Manajemen proyek perbankan korporasi', 'Project Management', 92, 'pm.korp'],
            ['Anindya Paramita', 'Keuangan berkelanjutan', 'Sustainability', 85, 'pm.esg'],
            ['Yohanes Siregar', 'Teknologi SDM', 'Human Capital', 80, 'pm.hr'],
            ['Sekar Arum', 'Perencanaan & penjadwalan', 'PMO', 55, 'planner'],
            ['Dimas Prakoso', 'Pengendalian biaya', 'PMO', 55, null],
            ['Fikri Alamsyah', 'Analis bisnis', 'IT Retail Banking Enablement', 60, null],
            ['Melati Sukma', 'Analis bisnis', 'IT Corporate Banking Enablement', 60, null],
            ['Kevin Hartono', 'Arsitek solusi', 'IT Digital Service Enablement', 85, null],
            ['Putu Wiradana', 'Pengembang core & payment', 'IT Digital Service Enablement', 65, null],
            ['Nur Aini', 'Pengembang aplikasi mobile', 'Digital Banking Solution', 65, null],
            ['Bagus Pranata', 'Pengembang aplikasi web', 'Digital Banking Solution', 62, null],
            ['Hana Pertiwi', 'Desain UX', 'Digital Banking Solution', 60, null],
            ['Johan Saputra', 'Rekayasa data', 'IT Data Management', 70, null],
            ['Ratih Purnamasari', 'Tata kelola data', 'IT Data Management', 68, null],
            ['Stefanus Lim', 'Infrastruktur & cloud', 'IT Transaction Management', 75, null],
            ['Agung Wicaksono', 'Jaringan & pusat data', 'IT Transaction Management', 70, null],
            ['Vina Oktaviani', 'Keamanan siber', 'IT Governance Management', 80, null],
            ['Arif Budiman', 'Penguji perangkat lunak', 'IT Digital Service Enablement', 50, null],
            ['Lestari Handayani', 'Penguji perangkat lunak', 'Digital Banking Solution', 50, null],
            ['Teddy Kurniawan', 'Manajemen risiko TI', 'IT Governance Management', 75, null],
            ['Maria Gunawan', 'Kepatuhan & APU-PPT', 'Compliance', 70, null],
            ['Siti Rahmawati', 'Manajemen risiko terintegrasi', 'Integrated Risk', 75, null],
            ['Dedi Kurnia', 'Pengadaan & manajemen vendor', 'Procurement', 55, null],
            ['Ayu Lestari', 'Manajemen perubahan & pelatihan', 'Human Capital', 55, null],
            ['Rudy Hermawan', 'Operasional cabang', 'Branch Operations', 50, null],
            ['Intan Permatasari', 'Pemasaran produk digital', 'Jenius Business', 60, null],
            ['Bayu Nugraha', 'DevOps & otomasi', 'IT Transaction Management', 70, null],
            ['Yudi Setiadi', 'Konsultan implementasi', 'Mitra implementasi', 90, null],
            ['Grace Tanoto', 'Konsultan implementasi', 'Mitra implementasi', 90, null],
        ];
        foreach ($pool as $i => [$name, $disc, $dept, $rate, $user]) {
            $this->resources[] = Models\Resource::create([
                'code' => sprintf('SMBC-SDM-%03d', $i + 1), 'name' => $name, 'discipline' => $disc, 'department' => $dept,
                'type' => $i >= 31 ? 'kontraktor' : 'tenaga_kerja', 'cost_rate' => $rate, 'capacity_hours' => 40,
                'email' => static::PREFIX.strtolower(str_replace(' ', '.', $name)).'@contoh.local',
                'user_id' => $user ? $this->users[$user]->id : null,
            ]);
        }
        foreach ([['Lingkungan uji kinerja (sewa)', 'Infrastruktur uji'], ['Kapasitas cloud non-produksi', 'Infrastruktur cloud']] as $i => [$n, $d]) {
            Models\Resource::create(['code' => sprintf('SMBC-ALT-%02d', $i + 1), 'name' => $n, 'discipline' => $d, 'department' => 'Peralatan', 'type' => 'peralatan', 'cost_rate' => 0, 'capacity_hours' => 168]);
        }
    }

    protected function vendors(): void
    {
        $list = [
            ['CBS', 'Core Banking Solutions Asia Pte. Ltd. (fiktif)', 'Perangkat lunak core banking', 'Singapura'],
            ['SIN', 'PT Sinergi Integrasi Nusantara (fiktif)', 'System integrator', 'Indonesia'],
            ['AWN', 'PT Awan Data Indonesia (fiktif)', 'Layanan cloud & pusat data', 'Indonesia'],
            ['SBR', 'PT Siber Benteng Digital (fiktif)', 'Keamanan siber (XDR, WAF)', 'Indonesia'],
            ['GAI', 'Generative AI Labs Ltd. (fiktif)', 'Platform AI generatif', 'Singapura'],
            ['DTM', 'PT Data Mutu Analitika (fiktif)', 'Tata kelola & kualitas data', 'Indonesia'],
            ['MOB', 'PT Aplikasi Bergerak Kreatif (fiktif)', 'Pengembangan aplikasi mobile', 'Indonesia'],
            ['TEL', 'PT Telefoni Kontak Terpadu (fiktif)', 'Sistem contact center', 'Indonesia'],
            ['AML', 'RegTech Compliance Systems (fiktif)', 'Sistem APU-PPT', 'Singapura'],
            ['HRS', 'PT Talenta Sistem Digital (fiktif)', 'HRIS & otomasi SDM', 'Indonesia'],
            ['ESG', 'Sustainable Finance Advisory (fiktif)', 'Konsultan keuangan berkelanjutan', 'Singapura'],
            ['GRC', 'PT Tata Kelola Risiko Konsultan (fiktif)', 'Konsultan GRC', 'Indonesia'],
            ['QAT', 'PT Uji Mutu Perangkat Lunak (fiktif)', 'Pengujian independen', 'Indonesia'],
        ];
        foreach ($list as [$code, $name, $cat, $country]) {
            $this->vendors[$code] = Models\Vendor::create([
                'code' => 'VB-'.$code, 'name' => $name, 'category' => $cat, 'country' => $country, 'rating' => mt_rand(3, 5),
                'contact_name' => 'Account Manager', 'email' => strtolower($code).'@vendor-bank.contoh',
            ]);
        }
    }

    /** @return array<string,Models\Portfolio> */
    protected function portfolios(): array
    {
        $out = [];
        foreach ([
            ['SMBC-TI', 'Portofolio Rencana Strategis TI (ITSP) SMBC Indonesia', 'Lima area strategis TI 2025: kapabilitas strategis (AI generatif & cloud), keamanan siber, konsolidasi aplikasi, peningkatan core banking, dan kualitas data laporan.', '#00875A', 'Direktur Operasional & Teknologi', 'portofolio'],
            ['SMBC-DIG', 'Portofolio Perbankan Digital (Jenius & TOUCHBIZ)', 'Memperkuat Jenius sebagai platform dan wealth management serta layanan digital nasabah korporasi.', '#1F8FB5', 'Direktur Retail & Digital Banking', 'portofolio'],
            ['SMBC-KK', 'Program Konglomerasi Keuangan & Keberlanjutan', 'Menjalankan peran PIKK, tata kelola terintegrasi, keuangan berkelanjutan, dan pengembangan talenta.', '#7A5AA6', 'Direktur Utama', 'program'],
        ] as [$code, $name, $obj, $color, $owner, $type]) {
            $out[$code] = Models\Portfolio::create(['code' => $code, 'name' => $name, 'strategic_objective' => $obj, 'color' => $color, 'owner_name' => $owner, 'type' => $type]);
        }

        return $out;
    }

    protected function projects(): array
    {
        return [
            ['code' => 'SMB-CBS', 'portfolio' => 'SMBC-TI', 'unit' => 'teknologi', 'type' => 'core', 'pm' => 'pm.core', 'start' => '2025-04-01', 'finish' => '2027-12-31', 'budget' => 72e6, 'spi' => 0.93, 'cpi' => 0.97, 'status' => 'aktif', 'phase' => 'eksekusi', 'priority' => 'tinggi',
                'name' => 'Peningkatan Kinerja Core Banking System', 'location' => 'Menara SMBC, Jakarta & pusat data', 'ptype' => 'Implementasi sistem inti perbankan',
                'desc' => 'Strategi peningkatan kinerja Core Banking System yang telah siap memasuki fase implementasi pada 2026, sebagai bagian dari persiapan Bank mendukung pertumbuhan bisnis (Laporan Tahunan 2025, Pengembangan TI butir 4).',
                'objectives' => "Waktu proses akhir hari (EOD) turun 40%\nKapasitas transaksi 2× volume 2025\nCutover tanpa gangguan layanan nasabah"],
            ['code' => 'SMB-GAI', 'portfolio' => 'SMBC-TI', 'unit' => 'teknologi', 'type' => 'app', 'pm' => 'pm.digital', 'start' => '2025-03-01', 'finish' => '2026-12-31', 'budget' => 6.8e6, 'spi' => 1.02, 'cpi' => 0.98, 'status' => 'aktif', 'phase' => 'eksekusi', 'priority' => 'tinggi',
                'name' => 'Generative AI untuk Pengetahuan Internal & Layanan Pelanggan', 'location' => 'Kantor pusat & contact center', 'ptype' => 'Implementasi AI generatif',
                'desc' => 'Pemanfaatan teknologi berbasis Generative AI untuk pengelolaan pengetahuan internal dan layanan pelanggan, bagian dari peningkatan kapabilitas strategis TI 2025.',
                'objectives' => "Asisten pengetahuan untuk 5.000 pegawai\nWaktu penanganan contact center turun 20%\nKepatuhan tata kelola AI & perlindungan data"],
            ['code' => 'SMB-CLD', 'portfolio' => 'SMBC-TI', 'unit' => 'teknologi', 'type' => 'infra', 'pm' => 'pm.infra', 'start' => '2025-01-01', 'finish' => '2027-03-31', 'budget' => 21e6, 'spi' => 0.88, 'cpi' => 0.93, 'status' => 'aktif', 'phase' => 'pengendalian', 'priority' => 'tinggi',
                'name' => 'Adopsi Komputasi Awan (Cloud Readiness)', 'location' => 'Pusat data utama & penyedia cloud', 'ptype' => 'Transformasi infrastruktur TI',
                'desc' => 'Adopsi komputasi awan untuk memastikan kesiapan infrastruktur, proses bisnis, dan organisasi dalam mendukung operasional berbasis cloud.',
                'objectives' => "Landing zone cloud sesuai POJK 11/2022\n30 aplikasi non-inti bermigrasi\nBiaya infrastruktur per transaksi turun 15%"],
            ['code' => 'SMB-XDR', 'portfolio' => 'SMBC-TI', 'unit' => 'teknologi', 'type' => 'infra', 'pm' => 'pm.infra', 'start' => '2025-01-15', 'finish' => '2025-12-15', 'budget' => 9.4e6, 'spi' => 1, 'cpi' => 1.03, 'status' => 'selesai', 'phase' => 'penutupan', 'priority' => 'tinggi',
                'name' => 'Penguatan Keamanan Siber: XDR & Web Application Firewall', 'location' => 'Security Operations Center, Jakarta', 'ptype' => 'Keamanan siber',
                'desc' => 'Implementasi Extended Detection and Response (XDR) untuk mempercepat deteksi dan respons ancaman, serta Web Application Firewall untuk melindungi aplikasi web yang diakses melalui internet.',
                'objectives' => "Cakupan XDR 100% endpoint & server\nWaktu deteksi rata-rata < 15 menit\nSeluruh aplikasi publik di belakang WAF"],
            ['code' => 'SMB-APP', 'portfolio' => 'SMBC-TI', 'unit' => 'teknologi', 'type' => 'app', 'pm' => 'pm.digital', 'start' => '2024-10-01', 'finish' => '2026-09-30', 'budget' => 12.5e6, 'spi' => 0.84, 'cpi' => 0.89, 'status' => 'aktif', 'phase' => 'pengendalian', 'priority' => 'tinggi', 'slip' => 70,
                'name' => 'Konsolidasi & Standardisasi Aplikasi', 'location' => 'Kantor pusat, contact center & cabang', 'ptype' => 'Konsolidasi aplikasi',
                'desc' => 'Menyederhanakan kompleksitas operasional melalui integrasi sistem monitoring layanan digital banking, e-form pendaftaran nasabah, integrasi sistem telefoni contact center, dan pengembangan sistem anti pencucian uang. Pada tanggal status proyek terlambat dari baseline.',
                'objectives' => "Satu dasbor monitoring layanan digital\nE-form pendaftaran di seluruh cabang\nSistem APU-PPT terbarukan"],
            ['code' => 'SMB-DQR', 'portfolio' => 'SMBC-TI', 'unit' => 'teknologi', 'type' => 'data', 'pm' => 'pm.data', 'start' => '2025-04-01', 'finish' => '2026-11-30', 'budget' => 5.2e6, 'spi' => 0.96, 'cpi' => 1.01, 'status' => 'aktif', 'phase' => 'eksekusi', 'priority' => 'sedang',
                'name' => 'Peningkatan Kualitas Data Laporan (Kamus & Silsilah Data)', 'location' => 'Data Management Office, Jakarta', 'ptype' => 'Tata kelola data',
                'desc' => 'Tinjauan menyeluruh atas proses pelaporan, implementasi kamus data, serta pengelolaan silsilah data (data lineage) untuk memperkuat tata kelola dan kepatuhan.',
                'objectives' => "Kamus data untuk 100% laporan regulator\nSilsilah data laporan prioritas terdokumentasi\nTemuan kualitas data turun 50%"],
            ['code' => 'SMB-JNS', 'portfolio' => 'SMBC-DIG', 'unit' => 'perbankan', 'type' => 'app', 'pm' => 'pm.digital', 'start' => '2024-11-01', 'finish' => '2025-12-31', 'budget' => 13.5e6, 'spi' => 1, 'cpi' => 0.97, 'status' => 'selesai', 'phase' => 'penutupan', 'priority' => 'tinggi',
                'name' => 'Fitur Baru Jenius 2025 (s-Card, Maxi Saver Valas, QRIS Cross Border)', 'location' => 'Aplikasi Jenius', 'ptype' => 'Pengembangan produk digital',
                'desc' => 'Peluncuran Kartu Kredit Tambahan (s-Card), Maxi Saver untuk USD, GBP, CNY, SGD, AUD, EUR dan Riyal Arab Saudi, QRIS Cross Border, sumber dana kartu kredit untuk QRIS, serta Jenius Bisniskit untuk UMKM. Nasabah terdaftar Jenius 6,3 juta pada 2025.',
                'objectives' => "Seluruh fitur rilis di 2025\nNasabah terdaftar tumbuh > 5% yoy\nRating aplikasi ≥ 4,5"],
            ['code' => 'SMB-TBZ', 'portfolio' => 'SMBC-DIG', 'unit' => 'perbankan', 'type' => 'app', 'pm' => 'pm.korp', 'start' => '2025-06-01', 'finish' => '2026-12-31', 'budget' => 8.6e6, 'spi' => 0.97, 'cpi' => 1.02, 'status' => 'aktif', 'phase' => 'eksekusi', 'priority' => 'sedang',
                'name' => 'Pengembangan Platform TOUCHBIZ Tahap 2', 'location' => 'Aplikasi mobile & web TOUCHBIZ', 'ptype' => 'Platform digital nasabah korporasi',
                'desc' => 'Pengembangan lanjutan platform TOUCHBIZ (aplikasi mobile dan web) untuk transaksi nasabah korporasi dan komersial; volume transaksi 2025 Rp32 triliun (+106% yoy).',
                'objectives' => "Modul manajemen kas & trade baru\nVolume transaksi tumbuh > 50%\nOnboarding nasabah korporasi digital"],
            ['code' => 'SMB-DRC', 'portfolio' => 'SMBC-TI', 'unit' => 'teknologi', 'type' => 'infra', 'pm' => 'pm.infra', 'start' => '2026-01-05', 'finish' => '2026-12-18', 'budget' => 4.6e6, 'spi' => 0.82, 'cpi' => 0.9, 'status' => 'aktif', 'phase' => 'pengendalian', 'priority' => 'tinggi', 'slip' => 40,
                'name' => 'Peningkatan Pusat Data Pemulihan & Simulasi DR 2026', 'location' => 'Pusat data utama & DRC', 'ptype' => 'Keberlanjutan layanan TI',
                'desc' => 'Peningkatan kesiapan Disaster Recovery: rencana keberlanjutan layanan TI, pusat data pemulihan, dan simulasi perpindahan pusat data tahunan.',
                'objectives' => "RTO aplikasi kritis ≤ 2 jam\nSimulasi perpindahan pusat data berhasil\nTemuan audit DR ditutup"],
            ['code' => 'SMB-PKK', 'portfolio' => 'SMBC-KK', 'unit' => 'korporat', 'type' => 'program', 'pm' => 'pm.korp', 'start' => '2025-06-01', 'finish' => '2026-12-31', 'budget' => 3.9e6, 'spi' => 0.95, 'cpi' => 1.0, 'status' => 'aktif', 'phase' => 'eksekusi', 'priority' => 'tinggi',
                'name' => 'Penguatan Peran PIKK Konglomerasi Keuangan SMBC', 'location' => 'SMBC Indonesia, BTPN Syariah, OTO & SOF', 'ptype' => 'Program tata kelola terintegrasi',
                'desc' => 'Pada pertengahan 2025 OJK menyetujui SMBC Indonesia sebagai PIKK Operasional atas KK SMBC (BTPN Syariah, BTPN Syariah Ventura, OTO, SOF). Program memperkuat tata kelola, manajemen risiko, dan kepatuhan terintegrasi serta sinergi operasional dan TI grup.',
                'objectives' => "Kebijakan tata kelola terintegrasi berlaku di seluruh entitas\nLaporan profil risiko terintegrasi otomatis\nPenghematan TI dari pembelian kolektif"],
            ['code' => 'SMB-ESG', 'portfolio' => 'SMBC-KK', 'unit' => 'keberlanjutan', 'type' => 'program', 'pm' => 'pm.esg', 'start' => '2025-02-01', 'finish' => '2026-12-31', 'budget' => 2.8e6, 'spi' => 0.99, 'cpi' => 1.04, 'status' => 'aktif', 'phase' => 'eksekusi', 'priority' => 'sedang',
                'name' => 'Penguatan Pembiayaan Berkelanjutan & Produk ESG', 'location' => 'Kantor pusat & lini bisnis', 'ptype' => 'Program keuangan berkelanjutan',
                'desc' => 'Departemen Sustainability Business Strategy mendukung LOB memperluas green loan, sustainability-linked loan, social loan, ESG Deposit, dan reksa dana ESG. Pembiayaan berkelanjutan 2025 Rp17,82 triliun (11,89% dari total pinjaman bank only).',
                'objectives' => "Porsi pembiayaan berkelanjutan ≥ 12%\nKerangka penilaian KKUB terotomasi\nPelaporan keberlanjutan tepat waktu"],
            ['code' => 'SMB-HRS', 'portfolio' => 'SMBC-KK', 'unit' => 'korporat', 'type' => 'app', 'pm' => 'pm.hr', 'start' => '2025-02-01', 'finish' => '2026-06-30', 'budget' => 3.6e6, 'spi' => 1, 'cpi' => 0.96, 'status' => 'selesai', 'phase' => 'penutupan', 'priority' => 'sedang',
                'name' => 'Transformasi Digital HR (HRIS & Otomasi)', 'location' => 'Human Capital, kantor pusat', 'ptype' => 'Transformasi digital SDM',
                'desc' => 'Pengembangan berkelanjutan fitur HRIS Bank dan inisiatif otomasi proses bisnis HR, mendukung pembelajaran lintas entitas (BTPN Syariah, OTO & SOF).',
                'objectives' => "Layanan mandiri pegawai 100% daring\nOtomasi 10 proses HR\nKepuasan pengguna ≥ 4/5"],
        ];
    }

    protected function charter(array $s): array
    {
        return [
            'justification' => 'Selaras dengan strategi SMBC Indonesia 2025: keunggulan operasional bank-wide melalui digitalisasi dan process excellence, serta keunggulan operasional TI dan kapabilitas keamanan digital dan siber.',
            'success_criteria' => "Selesai sesuai baseline jadwal (SPI ≥ 0,95)\nBiaya dalam anggaran (CPI ≥ 0,95)\nTanpa insiden gangguan layanan nasabah\nDiterima pengguna bisnis (UAT lulus)",
            'scope_statement' => 'Lingkup mencakup seluruh deliverable pada WBS level 1 dan paket kerja level 2, dari analisis kebutuhan hingga serah terima ke operasional TI dan bisnis.',
            'out_of_scope' => 'Operasional rutin setelah masa hypercare, perubahan produk di luar kebutuhan yang disetujui, dan entitas anak di luar lingkup piagam.',
            'assumptions' => "Pengguna bisnis tersedia untuk UAT\nPersetujuan/notifikasi OJK sesuai jadwal\nAnggaran TI tahunan disetujui",
            'constraints' => "POJK 11/2022 tentang Penyelenggaraan TI oleh Bank Umum\nJendela perubahan sistem hanya akhir pekan\nPerlindungan data pribadi (UU PDP)",
        ];
    }

    protected function wbsOwners(): array
    {
        return ['Sekar Arum', 'Fikri Alamsyah', 'Kevin Hartono', 'Putu Wiradana', 'Nur Aini', 'Johan Saputra', 'Stefanus Lim', 'Arif Budiman', 'Dedi Kurnia', 'Ayu Lestari', 'Vina Oktaviani', 'Melati Sukma'];
    }

    protected function wbsTemplate(string $type): array
    {
        return match ($type) {
            'core' => [
                ['Manajemen Program & Tata Kelola', 0.06, 0.00, 1.00, 'Rencana manajemen program', [
                    ['Piagam, business case & baseline', 0.25, 0.00, 0.06], ['Persetujuan Komite Pengarah TI', 0.0, 0.06, 0.06, 'ms' => true],
                    ['PMO, pelaporan & manajemen vendor', 0.55, 0.06, 0.96], ['Penutupan program', 0.20, 0.94, 1.00]]],
                ['Analisis & Desain Solusi', 0.12, 0.04, 0.30, 'Dokumen desain solusi disetujui', [
                    ['Analisis kesenjangan proses & produk', 0.40, 0.04, 0.16], ['Desain arsitektur & integrasi', 0.35, 0.12, 0.26], ['Desain migrasi data', 0.25, 0.18, 0.30]]],
                ['Konfigurasi & Pengembangan', 0.30, 0.22, 0.62, 'Sistem siap uji', [
                    ['Konfigurasi produk simpanan & pinjaman', 0.40, 0.22, 0.50], ['Pengembangan antarmuka (channel, payment, GL)', 0.45, 0.28, 0.58], ['Peningkatan kinerja batch EOD', 0.15, 0.40, 0.62]]],
                ['Migrasi Data', 0.14, 0.30, 0.86, 'Data termigrasi & terekonsiliasi', [
                    ['Pembersihan data sumber', 0.30, 0.30, 0.55], ['Mock migration 1–3', 0.50, 0.50, 0.80], ['Rekonsiliasi & sign-off data', 0.20, 0.78, 0.86]]],
                ['Pengujian', 0.20, 0.45, 0.88, 'Seluruh skenario uji lulus', [
                    ['System integration test (SIT)', 0.35, 0.45, 0.68], ['User acceptance test (UAT)', 0.30, 0.64, 0.80], ['Uji kinerja & uji DR', 0.20, 0.72, 0.86], ['UAT sign-off', 0.0, 0.80, 0.80, 'ms' => true], ['Dress rehearsal cutover', 0.15, 0.82, 0.88]]],
                ['Cutover, Go-Live & Hypercare', 0.18, 0.86, 1.00, 'Sistem produksi stabil', [
                    ['Pelatihan pengguna & kesiapan cabang', 0.30, 0.80, 0.90], ['Cutover akhir pekan', 0.25, 0.90, 0.92], ['Go-live', 0.0, 0.92, 0.92, 'ms' => true], ['Hypercare 8 minggu', 0.45, 0.92, 1.00]]],
            ],
            'infra' => [
                ['Manajemen Proyek & Tata Kelola', 0.07, 0.00, 1.00, 'Rencana manajemen proyek', [
                    ['Piagam & persetujuan Komite Pengarah TI', 0.30, 0.00, 0.08], ['Pengendalian proyek & pelaporan', 0.55, 0.08, 0.96], ['Penutupan proyek', 0.15, 0.94, 1.00]]],
                ['Penilaian & Desain', 0.13, 0.04, 0.30, 'Arsitektur target disetujui', [
                    ['Penilaian kondisi saat ini', 0.40, 0.04, 0.16], ['Desain arsitektur target & kontrol keamanan', 0.60, 0.14, 0.30], ['Persetujuan arsitektur', 0.0, 0.30, 0.30, 'ms' => true]]],
                ['Pengadaan & Kontrak', 0.25, 0.20, 0.45, 'Perangkat & layanan tersedia', [
                    ['RFP, evaluasi & negosiasi vendor', 0.30, 0.20, 0.34], ['Pengadaan perangkat & lisensi', 0.70, 0.30, 0.45]]],
                ['Implementasi', 0.35, 0.40, 0.80, 'Lingkungan produksi siap', [
                    ['Instalasi & konfigurasi', 0.45, 0.40, 0.65], ['Integrasi dengan SOC/monitoring', 0.30, 0.55, 0.75], ['Migrasi beban kerja bertahap', 0.25, 0.62, 0.80]]],
                ['Pengujian & Keamanan', 0.12, 0.65, 0.92, 'Lulus uji keamanan & ketahanan', [
                    ['Uji fungsi & failover', 0.45, 0.65, 0.84], ['Uji penetrasi & penilaian kerentanan', 0.35, 0.78, 0.90], ['Simulasi DR / tabletop', 0.20, 0.86, 0.92]]],
                ['Operasionalisasi', 0.08, 0.86, 1.00, 'Serah terima ke operasional TI', [
                    ['Runbook, SOP & pelatihan tim operasi', 0.60, 0.86, 0.97], ['Serah terima operasional', 0.0, 1.00, 1.00, 'ms' => true], ['Hypercare', 0.40, 0.94, 1.00]]],
            ],
            'data' => [
                ['Manajemen Proyek', 0.07, 0.00, 1.00, 'Rencana proyek', [
                    ['Piagam & tata kelola DMO', 0.35, 0.00, 0.08], ['Pengendalian & pelaporan', 0.65, 0.06, 1.00]]],
                ['Inventarisasi Laporan', 0.15, 0.04, 0.30, 'Daftar laporan prioritas', [
                    ['Inventaris laporan regulator & manajemen', 0.55, 0.04, 0.20], ['Penilaian kualitas data awal', 0.45, 0.15, 0.30]]],
                ['Kamus Data', 0.25, 0.20, 0.65, 'Kamus data terbit', [
                    ['Definisi elemen data kritis', 0.55, 0.20, 0.50], ['Platform katalog data', 0.45, 0.30, 0.65], ['Kamus data versi 1 disahkan', 0.0, 0.65, 0.65, 'ms' => true]]],
                ['Silsilah Data (Data Lineage)', 0.28, 0.35, 0.85, 'Lineage laporan prioritas', [
                    ['Pemetaan sumber–transformasi–laporan', 0.60, 0.35, 0.75], ['Otomasi pemindaian lineage', 0.40, 0.50, 0.85]]],
                ['Perbaikan Proses Pelaporan', 0.18, 0.40, 0.92, 'Proses pelaporan baru', [
                    ['Aturan validasi & rekonsiliasi', 0.55, 0.40, 0.80], ['Uji paralel laporan', 0.45, 0.72, 0.92]]],
                ['Adopsi & Tata Kelola', 0.07, 0.80, 1.00, 'Data steward aktif', [
                    ['Pelatihan data steward', 0.60, 0.80, 0.95], ['Serah terima ke DMO', 0.40, 0.92, 1.00]]],
            ],
            'app' => [
                ['Manajemen Proyek', 0.06, 0.00, 1.00, 'Rencana proyek', [
                    ['Piagam & persetujuan', 0.30, 0.00, 0.06], ['Pengendalian proyek & sprint review', 0.55, 0.06, 0.96], ['Penutupan proyek', 0.15, 0.94, 1.00]]],
                ['Analisis Kebutuhan & Desain', 0.14, 0.03, 0.28, 'Product backlog & desain disetujui', [
                    ['Analisis kebutuhan bisnis & regulasi', 0.45, 0.03, 0.16], ['Desain UX & arsitektur solusi', 0.55, 0.12, 0.28], ['Persetujuan desain', 0.0, 0.28, 0.28, 'ms' => true]]],
                ['Pengembangan (Sprint)', 0.38, 0.22, 0.72, 'Increment siap uji', [
                    ['Sprint 1–4: fitur inti', 0.45, 0.22, 0.48], ['Sprint 5–8: integrasi & fitur lanjutan', 0.40, 0.44, 0.68], ['Integrasi API & keamanan aplikasi', 0.15, 0.50, 0.72]]],
                ['Pengujian & Keamanan', 0.18, 0.55, 0.88, 'Lulus SIT, UAT & uji penetrasi', [
                    ['SIT & regresi otomatis', 0.40, 0.55, 0.78], ['UAT bersama bisnis', 0.35, 0.72, 0.85], ['Uji penetrasi aplikasi', 0.25, 0.78, 0.88]]],
                ['Rilis & Adopsi', 0.16, 0.84, 1.00, 'Fitur digunakan nasabah', [
                    ['Notifikasi/izin produk ke OJK', 0.20, 0.70, 0.86], ['Rilis bertahap (pilot → seluruh pengguna)', 0.45, 0.86, 0.95], ['Go-live', 0.0, 0.92, 0.92, 'ms' => true], ['Kampanye adopsi & dukungan', 0.35, 0.90, 1.00]]],
                ['Manajemen Perubahan', 0.08, 0.10, 1.00, 'Pengguna internal siap', [
                    ['Komunikasi & pelatihan pengguna', 0.60, 0.40, 0.95], ['Pembaruan SOP', 0.40, 0.70, 1.00]]],
            ],
            default => [
                ['Mobilisasi & Tata Kelola Program', 0.12, 0.00, 1.00, 'Piagam program', [
                    ['Piagam program & struktur tata kelola', 0.30, 0.00, 0.08], ['PMO & pelaporan ke Direksi', 0.70, 0.06, 1.00]]],
                ['Diagnostik & Kebijakan', 0.20, 0.04, 0.35, 'Kebijakan disetujui', [
                    ['Analisis kesenjangan regulasi & praktik', 0.50, 0.04, 0.20], ['Penyusunan kebijakan & prosedur', 0.50, 0.15, 0.35], ['Persetujuan Direksi & Dewan Komisaris', 0.0, 0.35, 0.35, 'ms' => true]]],
                ['Sistem & Data Pendukung', 0.28, 0.25, 0.85, 'Pelaporan terotomasi', [
                    ['Kebutuhan data & integrasi entitas', 0.45, 0.25, 0.55], ['Dasbor & pelaporan terintegrasi', 0.55, 0.45, 0.85]]],
                ['Implementasi di Lini Bisnis & Entitas', 0.25, 0.35, 0.92, 'Proses baru berjalan', [
                    ['Piloting di lini bisnis prioritas', 0.45, 0.35, 0.60], ['Perluasan ke seluruh entitas', 0.55, 0.55, 0.92]]],
                ['Kapabilitas & Budaya', 0.15, 0.20, 1.00, 'Pegawai kompeten', [
                    ['Pelatihan & sertifikasi', 0.60, 0.20, 0.90], ['Evaluasi & penyerahan ke unit pemilik', 0.40, 0.88, 1.00]]],
            ],
        };
    }

    protected function riskPool(string $type): array
    {
        $common = [
            ['Pengguna bisnis tidak tersedia untuk UAT', 'organisasi', 'Beban operasional lini bisnis', 3, 3, 'mitigasi', 'Jadwal UAT disepakati di awal, backfill personel'],
            ['Keterlambatan persetujuan/notifikasi regulator', 'regulasi', 'Antrian proses di OJK', 2, 4, 'mitigasi', 'Konsultasi awal & dokumen lengkap sebelum pengajuan'],
            ['Pergantian personel kunci vendor', 'pengadaan', 'Persaingan talenta TI', 3, 3, 'alihkan', 'Klausul personel kunci & penalti di kontrak'],
            ['Kenaikan biaya lisensi karena kurs', 'keuangan', 'Lisensi dalam US$ dan SGD', 3, 2, 'terima', 'Pembayaran di muka saat kurs menguntungkan'],
            ['Kerentanan keamanan pada komponen pihak ketiga', 'siber', 'Pustaka open source & API vendor', 3, 4, 'mitigasi', 'Software composition analysis & patch bulanan'],
            ['Resistensi pengguna terhadap proses baru', 'organisasi', 'Kebiasaan kerja lama', 3, 2, 'mitigasi', 'Change champion di tiap unit, pelatihan bertahap'],
        ];
        $specific = match ($type) {
            'core' => [
                ['Kegagalan rekonsiliasi saat migrasi data', 'teknis', 'Kualitas data historis & aturan konversi', 3, 5, 'mitigasi', 'Tiga kali mock migration, rekonsiliasi otomatis per produk'],
                ['Kinerja EOD tidak mencapai target', 'teknis', 'Volume transaksi tumbuh lebih cepat', 3, 4, 'mitigasi', 'Uji kinerja dengan volume 2× dan tuning batch'],
                ['Cutover melampaui jendela akhir pekan', 'teknis', 'Langkah cutover terlalu banyak', 2, 5, 'mitigasi', 'Dress rehearsal 2 kali, rencana rollback tervalidasi'],
                ['Gangguan layanan nasabah pascago-live', 'komersial', 'Defect yang lolos pengujian', 2, 5, 'mitigasi', 'Hypercare 24/7, war room, komunikasi nasabah'],
            ],
            'infra' => [
                ['Kapasitas pusat data cadangan tidak memadai', 'teknis', 'Pertumbuhan beban kerja', 3, 4, 'mitigasi', 'Perencanaan kapasitas & ekspansi rak'],
                ['Simulasi DR gagal mencapai RTO', 'teknis', 'Ketergantungan aplikasi tidak terpetakan', 3, 4, 'mitigasi', 'Pemetaan dependensi & uji per kelompok aplikasi'],
                ['Keterlambatan pengiriman perangkat', 'pengadaan', 'Rantai pasok global perangkat keras', 3, 3, 'mitigasi', 'Pesan lebih awal, opsi sewa sementara'],
                ['Serangan siber selama masa transisi', 'siber', 'Konfigurasi ganda lama dan baru', 2, 5, 'mitigasi', 'Pemantauan SOC 24/7 & hardening'],
            ],
            'data' => [
                ['Definisi data berbeda antar unit', 'organisasi', 'Kepemilikan data belum jelas', 4, 3, 'mitigasi', 'Penetapan data owner & forum tata kelola data'],
                ['Sistem sumber tidak menyediakan metadata', 'teknis', 'Aplikasi lama', 3, 3, 'terima', 'Dokumentasi manual untuk sistem lama'],
                ['Temuan data saat uji paralel laporan', 'regulasi', 'Perbedaan logika lama dan baru', 3, 4, 'mitigasi', 'Uji paralel dua periode sebelum beralih'],
            ],
            'app' => [
                ['Lonjakan trafik saat peluncuran fitur', 'teknis', 'Kampanye pemasaran', 3, 4, 'mitigasi', 'Uji beban & autoscaling, rilis bertahap'],
                ['Perubahan kebutuhan di tengah sprint', 'organisasi', 'Prioritas bisnis bergeser', 4, 2, 'terima', 'Backlog refinement & kontrol perubahan per rilis'],
                ['Halusinasi/jawaban keliru dari model AI', 'teknis', 'Keterbatasan model generatif', 3, 4, 'mitigasi', 'Grounding ke basis pengetahuan, human-in-the-loop, evaluasi berkala'],
                ['Peluang perluasan fitur ke entitas anak', 'komersial', 'Kebutuhan serupa di BTPN Syariah & OTO', 2, 3, 'eksploitasi', 'Desain multi-entitas sejak awal'],
            ],
            default => [
                ['Perbedaan kesiapan antar entitas anak', 'organisasi', 'Skala dan sistem yang berbeda', 4, 3, 'mitigasi', 'Pendampingan per entitas & tahapan implementasi'],
                ['Perubahan ketentuan OJK di tengah program', 'regulasi', 'Regulasi konglomerasi & keberlanjutan berkembang', 3, 3, 'terima', 'Pemantauan regulasi bulanan'],
                ['Data pendukung pelaporan tidak lengkap', 'teknis', 'Sumber data tersebar', 3, 3, 'mitigasi', 'Integrasi data bertahap & validasi manual sementara'],
            ],
        };

        return array_merge($specific, $common);
    }

    protected function teamRoles(string $unit): array
    {
        return match ($unit) {
            'teknologi' => [['Perencanaan & penjadwalan', 'PMO Analyst'], ['Arsitek solusi', 'Solution Architect'], ['Analis bisnis', 'Business Analyst'], ['Pengembang core & payment', 'Lead Developer'], ['Infrastruktur & cloud', 'Infra Lead'], ['Penguji perangkat lunak', 'Test Lead'], ['Keamanan siber', 'Security Lead'], ['Manajemen risiko TI', 'IT Risk']],
            'perbankan' => [['Perencanaan & penjadwalan', 'PMO Analyst'], ['Analis bisnis', 'Product Owner'], ['Pengembang aplikasi mobile', 'Mobile Lead'], ['Pengembang aplikasi web', 'Web Lead'], ['Desain UX', 'UX Designer'], ['Penguji perangkat lunak', 'QA Lead'], ['Pemasaran produk digital', 'Product Marketing'], ['Kepatuhan & APU-PPT', 'Compliance']],
            'keberlanjutan' => [['Perencanaan & penjadwalan', 'PMO Analyst'], ['Manajemen risiko terintegrasi', 'ESG Risk'], ['Rekayasa data', 'Data Engineer'], ['Manajemen perubahan & pelatihan', 'Change Management']],
            default => [['Perencanaan & penjadwalan', 'PMO Analyst'], ['Manajemen risiko terintegrasi', 'Integrated Risk Lead'], ['Kepatuhan & APU-PPT', 'Compliance Lead'], ['Tata kelola data', 'Data Governance'], ['Manajemen perubahan & pelatihan', 'Change Management']],
        };
    }

    protected function issueList(): array
    {
        return [
            ['Lingkungan uji SIT tidak stabil', 'sedang'], ['Akses data produksi untuk mock migration belum disetujui', 'sedang'],
            ['Defect kritis dari regresi belum ditutup', 'tinggi'], ['Dokumen kebutuhan belum ditandatangani pemilik bisnis', 'rendah'],
        ];
    }

    protected function changeList(): array
    {
        return [
            ['Penambahan kebutuhan pelaporan regulator baru', 'lingkup', 0.015, 21],
            ['Penyesuaian jadwal go-live menghindari periode tutup buku', 'jadwal', 0.0, 28],
            ['Peningkatan standar enkripsi data sesuai kebijakan keamanan', 'mutu', 0.01, 10],
            ['Penambahan lisensi akibat pertumbuhan pengguna', 'biaya', 0.02, 0],
            ['Penambahan vendor pengujian independen', 'pengadaan', 0.008, 0],
        ];
    }

    protected function qualityList(): array
    {
        return [
            ['Tinjauan desain solusi & arsitektur', 'tinjauan', 'Standar arsitektur TI Bank', 'Seluruh komentar kritis ditutup'],
            ['Code review & analisis kode statis', 'inspeksi', 'Secure coding OWASP ASVS', 'Tidak ada temuan kritis/tinggi'],
            ['System integration test (SIT)', 'uji', 'Rencana uji SIT', '≥ 98% skenario lulus, 0 defect kritis'],
            ['User acceptance test (UAT)', 'uji', 'Kriteria penerimaan bisnis', 'Sign-off pemilik bisnis'],
            ['Uji penetrasi independen', 'uji', 'POJK 11/2022 & kebijakan keamanan siber', 'Semua temuan tinggi diperbaiki'],
            ['Audit kesiapan go-live', 'audit', 'Checklist kesiapan produksi', 'Seluruh butir go/no-go terpenuhi'],
        ];
    }

    protected function inspectors(): array
    {
        return ['Arif Budiman', 'Teddy Kurniawan'];
    }

    protected function stakeholderList(string $unit): array
    {
        $common = [
            ['Direksi SMBC Indonesia', 'Direksi', 'PT Bank SMBC Indonesia Tbk', 'internal', 5, 5, 'pendukung', 'Laporan bulanan & Komite Pengarah TI'],
            ['Pemilik bisnis (Line of Business)', 'Business Head', 'SMBC Indonesia', 'internal', 4, 5, 'pendukung', 'Keterlibatan sejak analisis kebutuhan & UAT'],
            ['Satuan Kerja Audit Internal', 'Head of Internal Audit', 'SMBC Indonesia', 'internal', 4, 3, 'netral', 'Tinjauan pengendalian & kesiapan go-live'],
            ['Vendor & system integrator', 'Project Director', 'Mitra implementasi', 'pemasok', 3, 4, 'pendukung', 'Rapat kemajuan mingguan'],
        ];
        $specific = match ($unit) {
            'teknologi' => [['Otoritas Jasa Keuangan (OJK)', 'Pengawas Bank', 'OJK', 'regulator', 5, 4, 'netral', 'Laporan rencana strategis TI & notifikasi perubahan'], ['Grup SMBC (induk)', 'Regional IT', 'Sumitomo Mitsui Banking Corporation', 'mitra', 4, 4, 'pendukung', 'Penyelarasan standar TI grup'], ['Nasabah ritel & korporasi', 'Perwakilan nasabah', 'Nasabah', 'pelanggan', 3, 5, 'netral', 'Komunikasi jadwal pemeliharaan sistem']],
            'perbankan' => [['Nasabah Jenius / TOUCHBIZ', 'Komunitas pengguna', 'Nasabah', 'pelanggan', 3, 5, 'pendukung', 'Beta tester & survei kepuasan'], ['Otoritas Jasa Keuangan (OJK)', 'Pengawas Bank', 'OJK', 'regulator', 5, 4, 'netral', 'Pelaporan produk & layanan digital baru'], ['Bank Indonesia (QRIS)', 'Departemen Sistem Pembayaran', 'Bank Indonesia', 'regulator', 4, 3, 'netral', 'Kepatuhan standar QRIS']],
            'keberlanjutan' => [['Otoritas Jasa Keuangan (OJK)', 'Pengawas Keuangan Berkelanjutan', 'OJK', 'regulator', 5, 4, 'netral', 'Rencana Aksi Keuangan Berkelanjutan'], ['Nasabah korporasi', 'CFO', 'Debitur', 'pelanggan', 3, 4, 'pendukung', 'Edukasi green loan & SLL']],
            default => [['Otoritas Jasa Keuangan (OJK)', 'Pengawas Konglomerasi', 'OJK', 'regulator', 5, 4, 'netral', 'Laporan tata kelola & profil risiko terintegrasi'], ['Entitas anak (BTPN Syariah, OTO, SOF)', 'Direksi entitas', 'KK SMBC', 'mitra', 4, 5, 'pendukung', 'Forum koordinasi bulanan']],
        };

        return [$common, $specific];
    }

    protected function contractSets(): array
    {
        return [
            'core' => [['CBS', 'Lisensi & implementasi core banking', 'lump_sum', 0.38], ['SIN', 'Integrasi sistem & migrasi data', 'tm', 0.22], ['QAT', 'Pengujian independen & uji kinerja', 'unit_price', 0.06], ['AWN', 'Infrastruktur lingkungan uji', 'unit_price', 0.04]],
            'infra' => [['AWN', 'Layanan cloud & pusat data', 'unit_price', 0.40], ['SBR', 'Platform XDR, WAF & layanan SOC', 'lump_sum', 0.28], ['SIN', 'Jasa implementasi & migrasi', 'tm', 0.15]],
            'data' => [['DTM', 'Platform katalog data & lineage', 'lump_sum', 0.35], ['SIN', 'Jasa implementasi tata kelola data', 'tm', 0.25]],
            'app' => [['MOB', 'Pengembangan aplikasi (tim agile)', 'tm', 0.40], ['GAI', 'Platform & lisensi pendukung', 'lump_sum', 0.18], ['QAT', 'Pengujian independen & uji penetrasi', 'unit_price', 0.06], ['TEL', 'Integrasi contact center', 'lump_sum', 0.06]],
            'program' => [['GRC', 'Konsultan tata kelola & risiko terintegrasi', 'tm', 0.35], ['ESG', 'Konsultan keuangan berkelanjutan', 'tm', 0.20], ['DTM', 'Dasbor pelaporan terintegrasi', 'lump_sum', 0.15]],
        ];
    }

    protected function reportAchievement(): string
    {
        return 'Paket kerja berjalan sesuai rencana sprint/tahapan; tidak ada insiden gangguan layanan nasabah.';
    }

    protected function lessonList(string $code): array
    {
        return match ($code) {
            'SMB-XDR' => [
                ['risiko', 'positif', 'Onboarding XDR bertahap per kelompok aset', 'Penerapan bertahap (server kritis dulu) mencegah gangguan kinerja dan mempercepat tuning aturan deteksi.', 'Gunakan pola bertahap untuk kontrol keamanan baru berikutnya.'],
                ['komunikasi', 'positif', 'Laporan ke Komite Pengarah TI per triwulan', 'Ringkasan risiko dan capaian per triwulan mempercepat keputusan anggaran tambahan.', 'Pertahankan format laporan triwulanan.'],
            ],
            'SMB-JNS' => [
                ['lingkup', 'positif', 'Rilis fitur bertahap dengan pilot pengguna', 'Pilot s-Card ke sebagian nasabah menemukan isu batas kredit sebelum rilis penuh.', 'Wajibkan pilot untuk fitur keuangan baru.'],
                ['waktu', 'perbaikan', 'Notifikasi produk ke regulator perlu bufer', 'Penyesuaian dokumen produk valas menambah waktu proses.', 'Ajukan dokumen produk paling lambat 8 minggu sebelum rilis.'],
            ],
            default => [
                ['mutu', 'perbaikan', 'Otomasi uji regresi lebih awal', 'Regresi manual menjadi hambatan menjelang rilis.', 'Bangun suite regresi otomatis sejak sprint pertama.'],
                ['komunikasi', 'positif', 'Dasbor tunggal untuk Komite Pengarah TI', 'Satu sumber data (Promise) mempercepat keputusan lintas direktorat.', 'Wajibkan pembaruan progres paket kerja sebelum rapat.'],
                ['sdm', 'perbaikan', 'Ketersediaan pengguna bisnis untuk UAT', 'UAT tertunda karena pengguna kunci menangani tutup buku.', 'Sepakati kalender UAT dengan lini bisnis di awal proyek.'],
            ],
        };
    }
}
