<?php

namespace Database\Seeders;

use App\Models;
use Carbon\Carbon;
use Database\Seeders\Demo\DemoFiles;
use Database\Seeders\Demo\Templates;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Portofolio contoh PT Medco Energi Internasional Tbk (DATA DEMO).
 * Nama proyek, lokasi, kapasitas dan tonggak utama disarikan dari Laporan Tahunan 2025;
 * anggaran, jadwal rinci, progres, risiko, kontrak, personel dan dokumen adalah simulasi.
 */
class DemoMedcoSeeder extends Seeder
{
    public const STATUS_DATE = '2026-10-31';

    public const PASSWORD = 'Promise-2026!';

    /** Kode proyek penanda demo sudah terpasang */
    public const MARKER = 'NTN-FRL';

    protected const SEED = 2025;

    /** Awalan kode sumber daya & email agar beberapa demo bisa dipasang bersamaan */
    protected const PREFIX = '';

    protected Carbon $statusDate;

    /** @var array<string,Models\User> */
    protected array $users = [];

    /** @var array<int,Models\Resource> */
    protected array $resources = [];

    /** @var array<string,Models\Vendor> */
    protected array $vendors = [];

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

    protected function rnd(float $min, float $max): float
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }

    protected function people(): void
    {
        $users = [
            ['pmo', 'Ratna Kusumawardhani', 'pmo', 'VP Project Management Office'],
            ['pmo2', 'Hendra Wiratama', 'pmo', 'Senior Manager Portfolio Control'],
            ['direksi', 'Budi Santoso Hadiwijaya', 'eksekutif', 'Direktur Operasi (contoh)'],
            ['pm.natuna', 'Arief Rahmanto', 'manajer', 'Project Manager Natuna'],
            ['pm.senoro', 'Dewi Lestari Putri', 'manajer', 'Project Manager Sulawesi'],
            ['pm.sumatra', 'Fajar Nugroho', 'manajer', 'Project Manager Sumatra'],
            ['pm.intl', 'Michael Tanoto', 'manajer', 'Project Manager Internasional'],
            ['pm.panasbumi', 'Siti Nurhaliza Rahman', 'manajer', 'Project Manager Panas Bumi'],
            ['pm.power', 'Yoga Pratama', 'manajer', 'Project Manager Pembangkit'],
            ['pm.amnt', 'Bambang Sutrisno', 'manajer', 'Project Director Ekspansi'],
            ['pm.elang', 'Lina Marlina', 'manajer', 'Study Manager Elang'],
            ['pm.trf', 'Andini Paramitha', 'manajer', 'Transformation Lead'],
            ['planner', 'Rizky Maulana', 'anggota', 'Planner & Scheduler'],
            ['cost', 'Nadia Safitri', 'anggota', 'Cost Control Engineer'],
        ];
        foreach ($users as [$key, $name, $role, $title]) {
            $this->users[$key] = Models\User::forceCreate([
                'name' => $name, 'email' => static::PREFIX.$key.'@contoh.local', 'password' => self::PASSWORD,
                'job_title' => $title, 'role' => $role, 'is_active' => true,
            ]);
        }

        $pool = [
            // [nama, disiplin, fungsi, tarif US$/jam, user]
            ['Arief Rahmanto', 'Manajemen proyek offshore', 'Project Management', 145, 'pm.natuna'],
            ['Dewi Lestari Putri', 'Manajemen proyek gas', 'Project Management', 140, 'pm.senoro'],
            ['Fajar Nugroho', 'Manajemen proyek onshore', 'Project Management', 135, 'pm.sumatra'],
            ['Michael Tanoto', 'Manajemen proyek internasional', 'Project Management', 160, 'pm.intl'],
            ['Siti Nurhaliza Rahman', 'Manajemen proyek panas bumi', 'Project Management', 140, 'pm.panasbumi'],
            ['Yoga Pratama', 'Manajemen proyek pembangkit', 'Project Management', 130, 'pm.power'],
            ['Bambang Sutrisno', 'Manajemen proyek tambang', 'Project Management', 170, 'pm.amnt'],
            ['Lina Marlina', 'Studi kelayakan tambang', 'Project Management', 150, 'pm.elang'],
            ['Andini Paramitha', 'Transformasi bisnis', 'Strategy & Transformation', 125, 'pm.trf'],
            ['Rizky Maulana', 'Perencanaan & penjadwalan', 'Project Control', 85, 'planner'],
            ['Nadia Safitri', 'Pengendalian biaya', 'Project Control', 85, 'cost'],
            ['Agus Setiawan', 'Perencanaan & penjadwalan', 'Project Control', 80, null],
            ['Maya Anggraini', 'Pengendalian biaya', 'Project Control', 80, null],
            ['Teguh Prasetyo', 'Rekayasa struktur offshore', 'Engineering', 110, null],
            ['Indra Gunawan', 'Rekayasa proses', 'Engineering', 110, null],
            ['Wulan Sari', 'Rekayasa perpipaan', 'Engineering', 100, null],
            ['Hari Kurniawan', 'Rekayasa listrik & instrumen', 'Engineering', 100, null],
            ['Joko Susilo', 'Rekayasa pengeboran', 'Drilling & Wells', 130, null],
            ['Ahmad Fauzi', 'Supervisor pengeboran', 'Drilling & Wells', 120, null],
            ['Putri Ramadhani', 'Geologi & geofisika', 'Subsurface', 115, null],
            ['Dimas Aditya', 'Teknik reservoir', 'Subsurface', 120, null],
            ['Rina Wijayanti', 'Pengadaan & kontrak', 'Supply Chain', 90, null],
            ['Eko Purnomo', 'Logistik & ekspedisi', 'Supply Chain', 75, null],
            ['Sri Handayani', 'QA/QC', 'Quality', 85, null],
            ['Taufik Hidayat', 'Inspeksi las & NDT', 'Quality', 80, null],
            ['Kartika Dewi', 'HSSE', 'HSSE', 90, null],
            ['Rudi Hartono', 'Keselamatan konstruksi', 'HSSE', 85, null],
            ['Fitri Amalia', 'Lingkungan & AMDAL', 'HSSE', 90, null],
            ['Bayu Saputra', 'Konstruksi & site manager', 'Construction', 115, null],
            ['Lukman Hakim', 'Commissioning', 'Construction', 120, null],
            ['Gita Permata', 'Hubungan pemerintah & perizinan', 'External Affairs', 95, null],
            ['Yusuf Maulana', 'Hubungan masyarakat & PPM', 'External Affairs', 80, null],
            ['Citra Lestari', 'Teknik panas bumi', 'Engineering', 115, null],
            ['Samuel Simanjuntak', 'Metalurgi & pengolahan mineral', 'Engineering', 125, null],
            ['Novi Kristanti', 'Analis keuangan proyek', 'Finance', 95, null],
            ['Irfan Syahputra', 'Sistem informasi & data', 'Digital', 90, null],
        ];
        foreach ($pool as $i => [$name, $disc, $dept, $rate, $user]) {
            $this->resources[] = Models\Resource::create([
                'code' => sprintf('%sSDM-%03d', static::PREFIX ? strtoupper(rtrim(static::PREFIX, '.')).'-' : '', $i + 1), 'name' => $name, 'discipline' => $disc, 'department' => $dept,
                'type' => $i >= 33 ? 'kontraktor' : 'tenaga_kerja', 'cost_rate' => $rate, 'capacity_hours' => 40,
                'email' => static::PREFIX.strtolower(str_replace(' ', '.', $name)).'@contoh.local',
                'user_id' => $user ? $this->users[$user]->id : null,
            ]);
        }
        foreach ([['Rig jack-up (sewa)', 'Peralatan pengeboran'], ['Kapal konstruksi DP2', 'Peralatan marine'], ['Crane 750 ton', 'Peralatan angkat']] as $i => [$n, $d]) {
            Models\Resource::create(['code' => sprintf('ALT-%02d', $i + 1), 'name' => $n, 'discipline' => $d, 'department' => 'Peralatan', 'type' => 'peralatan', 'cost_rate' => 0, 'capacity_hours' => 168]);
        }
    }

    protected function resource(string $name): Models\Resource
    {
        foreach ($this->resources as $r) {
            if ($r->name === $name) {
                return $r;
            }
        }

        return $this->resources[0];
    }

    protected function vendors(): void
    {
        $list = [
            ['NRL', 'PT Nusantara Rekayasa Lepas Pantai (fiktif)', 'EPCI offshore'],
            ['BFY', 'PT Batam Fabrikasi Yard (fiktif)', 'Fabrikasi jacket & topside'],
            ['SDS', 'PT Samudera Drilling Services (fiktif)', 'Jasa pengeboran'],
            ['KPI', 'PT Katup & Pipa Indonesia (fiktif)', 'Material perpipaan'],
            ['GEO', 'PT Geotermal Rekayasa Nusantara (fiktif)', 'EPC panas bumi'],
            ['TRB', 'Turbin Global Energy Ltd. (fiktif)', 'Turbin & generator'],
            ['SLR', 'PT Surya Energi Teknik (fiktif)', 'EPC PLTS'],
            ['TRF', 'PT Trafo Listrik Prima (fiktif)', 'Trafo & switchgear'],
            ['MIN', 'Mineral Process Equipment Co. (fiktif)', 'Peralatan pengolahan mineral'],
            ['KSB', 'PT Konstruksi Sipil Bersama (fiktif)', 'Konstruksi sipil'],
            ['ENV', 'PT Lingkungan Lestari Konsultan (fiktif)', 'Konsultan AMDAL'],
            ['MGT', 'Global Strategy Advisors (fiktif)', 'Konsultan manajemen'],
            ['LOG', 'PT Logistik Energi Terpadu (fiktif)', 'Logistik & marine'],
            ['ELK', 'PT Elektrikal Kontrol Indonesia (fiktif)', 'Instrumentasi & kontrol'],
        ];
        foreach ($list as [$code, $name, $cat]) {
            $this->vendors[$code] = Models\Vendor::create([
                'code' => 'V-'.$code, 'name' => $name, 'category' => $cat,
                'country' => str_contains($name, 'PT ') ? 'Indonesia' : 'Singapura', 'rating' => mt_rand(3, 5),
                'contact_name' => 'Bagian Komersial', 'email' => strtolower($code).'@vendor.contoh',
            ]);
        }
    }

    /** @return array<string,Models\Portfolio> */
    protected function portfolios(): array
    {
        $out = [];
        foreach ([
            ['HULU', 'Portofolio Hulu Minyak & Gas', 'Mempertahankan dan menumbuhkan produksi migas melalui pengembangan lapangan yang disiplin dan berimbal hasil tinggi.', '#0F5E9C', 'Direktur Operasi Hulu'],
            ['POWER', 'Portofolio Ketenagalistrikan & Energi Terbarukan', 'Menambah kapasitas pembangkit rendah karbon: panas bumi, surya, dan gas siklus kombinasi.', '#2E7D4F', 'Direktur Utama Medco Power'],
            ['MINING', 'Portofolio Tembaga & Emas (AMNT)', 'Ekspansi kapasitas pengolahan dan pengembangan Elang untuk pertumbuhan jangka panjang.', '#D98E04', 'Direktur Proyek AMNT'],
            ['KORP', 'Program Transformasi & Keunggulan Operasional', 'Disiplin eksekusi, efisiensi biaya, dan tata kelola berbasis KPI.', '#6B4FA0', 'Chief Executive Officer'],
        ] as [$code, $name, $obj, $color, $owner]) {
            $out[$code] = Models\Portfolio::create(['code' => $code, 'name' => $name, 'strategic_objective' => $obj, 'color' => $color, 'owner_name' => $owner, 'type' => $code === 'KORP' ? 'program' : 'portofolio']);
        }

        return $out;
    }

    protected function projects(): array
    {
        // spi/cpi = faktor kinerja simulasi pada tanggal status 31 Oktober 2026
        return [
            ['code' => 'NTN-FRL', 'portfolio' => 'HULU', 'unit' => 'hulu_migas', 'type' => 'offshore', 'pm' => 'pm.natuna', 'start' => '2023-04-01', 'finish' => '2025-06-30', 'budget' => 318e6, 'spi' => 1, 'cpi' => 0.97, 'status' => 'selesai', 'phase' => 'penutupan', 'priority' => 'tinggi',
                'name' => 'Pengembangan Lapangan Forel-Bronang & FPSO Marlin Natuna', 'location' => 'South Natuna Sea Block B, Kepulauan Riau', 'ptype' => 'Pengembangan lapangan offshore',
                'desc' => 'Pengembangan dua platform dan tujuh sumur dengan FPSO Marlin Natuna, konversi kapal tanker menjadi fasilitas produksi terapung pertama di Indonesia, kapasitas hingga 10.000 bopd bruto. Onstream 12 Mei 2025.',
                'objectives' => "Produksi minyak bruto hingga 10.000 bopd\nOnstream semester I 2025\nNol LTI selama eksekusi"],
            ['code' => 'NTN-TRB', 'portfolio' => 'HULU', 'unit' => 'hulu_migas', 'type' => 'offshore', 'pm' => 'pm.natuna', 'start' => '2023-08-01', 'finish' => '2025-08-31', 'budget' => 186e6, 'spi' => 1, 'cpi' => 1.09, 'status' => 'selesai', 'phase' => 'penutupan', 'priority' => 'tinggi',
                'name' => 'Pengembangan Lapangan Terubuk (2 WHP, 6 sumur)', 'location' => 'South Natuna Sea Block B, Kepulauan Riau', 'ptype' => 'Pengembangan lapangan marginal offshore',
                'desc' => 'Dua platform kepala sumur tanpa awak bertenaga surya dan enam sumur, target produksi bruto 6.654 bopd dan 60 mmscfd. Mulai berproduksi April dan Juli 2025, lebih cepat dari jadwal FID dan 9% di bawah anggaran FID.',
                'objectives' => "Produksi bruto 6.654 bopd & 60 mmscfd\nRealisasi biaya ≤ anggaran FID\nPenurunan intensitas emisi GRK"],
            ['code' => 'NTN-WBH', 'portfolio' => 'HULU', 'unit' => 'hulu_migas', 'type' => 'drilling', 'pm' => 'pm.natuna', 'start' => '2026-02-01', 'finish' => '2027-01-31', 'budget' => 38.5e6, 'spi' => 0.97, 'cpi' => 1.02, 'status' => 'aktif', 'phase' => 'eksekusi', 'priority' => 'sedang',
                'name' => 'Sumur Pengembangan West Belut H-04', 'location' => 'South Natuna Sea Block B, Kepulauan Riau', 'ptype' => 'Pengeboran sumur pengembangan',
                'desc' => 'Pengeboran sumur pengembangan West Belut H-04 untuk menjaga kesinambungan penyaluran gas dari South Natuna Sea Block B.',
                'objectives' => "Sumur onstream Januari 2027\nMenjaga plateau penyaluran gas\nBiaya ≤ AFE"],
            ['code' => 'SEN-SSD', 'portfolio' => 'HULU', 'unit' => 'hulu_migas', 'type' => 'onshore', 'pm' => 'pm.senoro', 'start' => '2024-02-01', 'finish' => '2026-12-31', 'budget' => 262e6, 'spi' => 0.89, 'cpi' => 0.93, 'status' => 'aktif', 'phase' => 'pengendalian', 'priority' => 'tinggi',
                'name' => 'Pengembangan South Senoro (Senoro Fase 2A)', 'location' => 'Blok Senoro-Toili, Sulawesi Tengah', 'ptype' => 'Pengembangan lapangan gas onshore',
                'desc' => 'Pengeboran pengembangan South Senoro dan fasilitas alir ke Central Processing Plant di North Senoro untuk tambahan pasokan gas ke DSLNG, Panca Amara Utama, PLN, dan PGN.',
                'objectives' => "Commissioning penuh Senoro Fase 2A\nTambahan pasokan gas sesuai komitmen penjualan\nNol LTI"],
            ['code' => 'COR-SMB', 'portfolio' => 'HULU', 'unit' => 'hulu_migas', 'type' => 'onshore', 'pm' => 'pm.sumatra', 'start' => '2025-05-01', 'finish' => '2027-06-30', 'budget' => 148e6, 'spi' => 1.01, 'cpi' => 0.98, 'status' => 'aktif', 'phase' => 'eksekusi', 'priority' => 'tinggi',
                'name' => 'Pengembangan Lapangan Sambar, PSC Corridor', 'location' => 'PSC Corridor, Sumatra Selatan', 'ptype' => 'Pengembangan lapangan gas onshore',
                'desc' => 'Pengembangan lapangan Sambar di PSC Corridor yang dijadwalkan mulai beroperasi pada 2027.',
                'objectives' => "First gas 2027\nBiaya ≤ anggaran POD\nTKDN sesuai ketentuan"],
            ['code' => 'SKK-DEV', 'portfolio' => 'HULU', 'unit' => 'hulu_migas', 'type' => 'onshore', 'pm' => 'pm.sumatra', 'start' => '2025-03-01', 'finish' => '2027-09-30', 'budget' => 205e6, 'spi' => 0.92, 'cpi' => 0.96, 'status' => 'aktif', 'phase' => 'eksekusi', 'priority' => 'tinggi',
                'name' => 'Pengembangan Lapangan Sakakemang', 'location' => 'Blok Sakakemang, Sumatra Selatan', 'ptype' => 'Pengembangan lapangan gas onshore',
                'desc' => 'Pengembangan lapangan gas Sakakemang beserta fasilitas pengolahan dan pipa, dijadwalkan beroperasi pada 2027.',
                'objectives' => "First gas 2027\nKapasitas fasilitas sesuai POD\nPengelolaan CO₂ sesuai desain"],
            ['code' => 'OMN-B60', 'portfolio' => 'HULU', 'unit' => 'hulu_migas', 'type' => 'onshore', 'pm' => 'pm.intl', 'start' => '2025-07-01', 'finish' => '2027-06-30', 'budget' => 172e6, 'spi' => 1.04, 'cpi' => 1.05, 'status' => 'aktif', 'phase' => 'eksekusi', 'priority' => 'tinggi',
                'name' => 'Ekspansi Bisat-E & Pengeboran Infill Blok 60', 'location' => 'Blok 60, Oman', 'ptype' => 'Peningkatan produksi minyak & gas',
                'desc' => 'Lanjutan pengeboran infill di lapangan Bisat dan ABB, debottlenecking dan kapasitas pengolahan air tambahan melalui Proyek Bisat-E serta injeksi air perifer.',
                'objectives' => "Mempertahankan produksi bruto > 70 mboepd\nKapasitas pengolahan air tambahan\nBiaya per sumur turun 5%"],
            ['code' => 'THA-BL1', 'portfolio' => 'HULU', 'unit' => 'hulu_migas', 'type' => 'offshore', 'pm' => 'pm.intl', 'start' => '2024-09-01', 'finish' => '2026-06-30', 'budget' => 124e6, 'spi' => 0.81, 'cpi' => 0.84, 'status' => 'aktif', 'phase' => 'pengendalian', 'priority' => 'tinggi', 'slip' => 75,
                'name' => 'Bualuang Fase I', 'location' => 'Lapangan Minyak Bualuang, Teluk Thailand', 'ptype' => 'Pengembangan lapangan offshore',
                'desc' => 'Proyek pengembangan Bualuang Fase I di Thailand yang ditargetkan beroperasi pada kuartal dua 2026. Pada tanggal status, proyek terlambat dari baseline dan sedang dalam pemulihan jadwal.',
                'objectives' => "Onstream kuartal II 2026 (baseline)\nMenahan laju penurunan produksi Bualuang\nNol LTI"],
            ['code' => 'PWR-IJN', 'portfolio' => 'POWER', 'unit' => 'ketenagalistrikan', 'type' => 'geothermal', 'pm' => 'pm.panasbumi', 'start' => '2022-03-01', 'finish' => '2025-02-28', 'budget' => 168e6, 'spi' => 1, 'cpi' => 0.95, 'status' => 'selesai', 'phase' => 'penutupan', 'priority' => 'tinggi',
                'name' => 'PLTP Ijen Unit 1 (35 MW)', 'location' => 'Bondowoso & Banyuwangi, Jawa Timur', 'ptype' => 'Pembangkit listrik panas bumi',
                'desc' => 'Pembangkit panas bumi pertama di Jawa Timur, unit pertama 35 MW dari rencana 110 MW, PPA 30 tahun, 83 menara transmisi dan jaringan 150 kV. COD Februari 2025.',
                'objectives' => "COD unit 1 (35 MW)\nPasokan listrik ±85.000 rumah tangga\nHindari emisi hingga 210.000 tCO₂e/tahun (total fasilitas)"],
            ['code' => 'PWR-BLT', 'portfolio' => 'POWER', 'unit' => 'ketenagalistrikan', 'type' => 'solar', 'pm' => 'pm.power', 'start' => '2024-01-01', 'finish' => '2025-07-31', 'budget' => 27.5e6, 'spi' => 1, 'cpi' => 1.03, 'status' => 'selesai', 'phase' => 'penutupan', 'priority' => 'sedang',
                'name' => 'PLTS Bali Timur (25 MWp)', 'location' => 'Karangasem, Bali', 'ptype' => 'Pembangkit listrik tenaga surya',
                'desc' => 'PLTS 25 MWp di Karangasem, Bali Timur, COD Juli 2025, sekitar 50 GWh per tahun (±42.000 rumah tangga) dan menghindari lebih dari 800.000 ton CO₂ selama masa operasi.',
                'objectives' => "COD Juli 2025\nProduksi ±50 GWh/tahun\nBiaya ≤ anggaran"],
            ['code' => 'PWR-ELB', 'portfolio' => 'POWER', 'unit' => 'ketenagalistrikan', 'type' => 'ccgt', 'pm' => 'pm.power', 'start' => '2024-02-01', 'finish' => '2025-11-30', 'budget' => 54e6, 'spi' => 1, 'cpi' => 0.98, 'status' => 'selesai', 'phase' => 'penutupan', 'priority' => 'sedang',
                'name' => 'ELB Expansion 39 MW (PLTGU Batam 109 MW)', 'location' => 'Batam, Kepulauan Riau', 'ptype' => 'Ekspansi siklus kombinasi',
                'desc' => 'Penambahan kapasitas 39 MW memanfaatkan panas buang turbin gas melalui turbin uap, total PLTGU menjadi 109 MW dan intensitas karbon turun dari 0,8 menjadi 0,5 tCO₂/MWh. Selesai November 2025.',
                'objectives' => "Tambahan kapasitas 39 MW\nIntensitas karbon 0,5 tCO₂/MWh\nTanpa gangguan pasokan selama tie-in"],
            ['code' => 'PWR-BNJ', 'portfolio' => 'POWER', 'unit' => 'ketenagalistrikan', 'type' => 'exploration', 'pm' => 'pm.panasbumi', 'start' => '2024-06-01', 'finish' => '2026-12-31', 'budget' => 33.8e6, 'spi' => 0.94, 'cpi' => 1.01, 'status' => 'aktif', 'phase' => 'pengendalian', 'priority' => 'sedang',
                'name' => 'Eksplorasi Panas Bumi Bonjol (PSPE)', 'location' => 'Pasaman, Sumatra Barat', 'ptype' => 'Eksplorasi panas bumi',
                'desc' => 'Preliminary Survey and Exploration (PSPE) dengan dua sumur eksplorasi pertama; potensi hingga 60 MW dan termasuk Proyek Strategis Nasional dalam RUPTL 2025–2034. Analisis hasil pengeboran sedang berjalan.',
                'objectives' => "Dua sumur eksplorasi selesai\nEstimasi potensi reservoir\nRekomendasi kelanjutan perizinan"],
            ['code' => 'PWR-IJ2', 'portfolio' => 'POWER', 'unit' => 'ketenagalistrikan', 'type' => 'geothermal', 'pm' => 'pm.panasbumi', 'start' => '2026-04-01', 'finish' => '2029-06-30', 'budget' => 305e6, 'spi' => 1.0, 'cpi' => 1.0, 'status' => 'aktif', 'phase' => 'perencanaan', 'priority' => 'sedang',
                'name' => 'PLTP Ijen Tahap 2 (menuju 110 MW)', 'location' => 'Bondowoso & Banyuwangi, Jawa Timur', 'ptype' => 'Pembangkit listrik panas bumi',
                'desc' => 'Pengembangan unit lanjutan PLTP Ijen menuju total kapasitas terpasang 110 MW (rencana; data demo).',
                'objectives' => "Financial close 2026\nCOD tahap 2 tahun 2029\nTambahan pasokan listrik rendah karbon untuk Jawa-Bali"],
            ['code' => 'AMN-CNX', 'portfolio' => 'MINING', 'unit' => 'tambang', 'type' => 'mining', 'pm' => 'pm.amnt', 'start' => '2021-07-01', 'finish' => '2026-12-31', 'budget' => 1.45e9, 'spi' => 0.91, 'cpi' => 0.88, 'status' => 'aktif', 'phase' => 'eksekusi', 'priority' => 'tinggi',
                'name' => 'Ekspansi Pabrik Konsentrator 85 Mtpa', 'location' => 'Batu Hijau, Sumbawa Barat, NTB', 'ptype' => 'Ekspansi pabrik pengolahan mineral',
                'desc' => 'Ekspansi pabrik konsentrator AMNT untuk menaikkan kapasitas input menjadi sekitar 85 juta ton bijih per tahun (dari 35–40 juta ton), mendukung Fase 8 Batu Hijau dan tambang Elang. Target mechanical completion dan commissioning 2026.',
                'objectives' => "Kapasitas input ±85 Mtpa\nMechanical completion & commissioning 2026\nNol fatality"],
            ['code' => 'AMN-PLG', 'portfolio' => 'MINING', 'unit' => 'tambang', 'type' => 'ccgt', 'pm' => 'pm.amnt', 'start' => '2023-01-01', 'finish' => '2026-06-30', 'budget' => 780e6, 'spi' => 0.95, 'cpi' => 0.92, 'status' => 'aktif', 'phase' => 'pengendalian', 'priority' => 'tinggi', 'slip' => 45,
                'name' => 'PLTGU 450 MW & Terminal LNG Teluk Benete', 'location' => 'Teluk Benete, Sumbawa Barat, NTB', 'ptype' => 'Pembangkit siklus kombinasi & regasifikasi',
                'desc' => 'PLTGU 450 MW yang didukung terminal LNG dan fasilitas regasifikasi di Teluk Benete untuk memasok smelter, ekspansi konsentrator, dan proyek Elang. Kargo LNG pertama diterima September 2025; commissioning bertahap.',
                'objectives' => "Pasokan listrik andal 450 MW\nMenggantikan PLTU batu bara 112 MW\nCommissioning bertahap sesuai kebutuhan daya"],
            ['code' => 'AMN-ELG', 'portfolio' => 'MINING', 'unit' => 'tambang', 'type' => 'study', 'pm' => 'pm.elang', 'start' => '2025-10-01', 'finish' => '2027-09-30', 'budget' => 92e6, 'spi' => 1.02, 'cpi' => 0.99, 'status' => 'aktif', 'phase' => 'perencanaan', 'priority' => 'tinggi',
                'name' => 'Front-End Engineering Tambang Elang', 'location' => 'Elang, Sumbawa, NTB', 'ptype' => 'Studi & FEED tambang',
                'desc' => 'Front-end engineering rinci setelah studi kelayakan definitif selesai akhir 2025; cadangan bijih Elang (JORC 2024) 2,5 miliar ton dengan 17,8 miliar pon tembaga dan 26,4 juta ons emas.',
                'objectives' => "Paket FEED & estimasi biaya kelas 3\nAMDAL disetujui\nDokumen keputusan investasi"],
            ['code' => 'KOR-TRF', 'portfolio' => 'KORP', 'unit' => 'korporat', 'type' => 'transformation', 'pm' => 'pm.trf', 'start' => '2025-03-01', 'finish' => '2026-12-31', 'budget' => 11.5e6, 'spi' => 0.86, 'cpi' => 1.04, 'status' => 'aktif', 'phase' => 'eksekusi', 'priority' => 'sedang',
                'name' => 'Inisiatif Transformasi Medco Power', 'location' => 'Jakarta & seluruh aset Medco Power', 'ptype' => 'Program transformasi',
                'desc' => 'Program untuk memperkuat disiplin eksekusi dan kinerja keuangan dengan fokus keandalan operasional, efisiensi biaya, dan tata kelola berbasis KPI; berlanjut pada 2026 dengan percepatan implementasi.',
                'objectives' => "Keandalan pembangkit (EAF) naik\nEfisiensi biaya O&M\nPemantauan kinerja berbasis KPI"],
        ];
    }

    protected function project(array $s, Models\Portfolio $portfolio): void
    {
        $start = Carbon::parse($s['start']);
        $finish = Carbon::parse($s['finish']);
        $done = $s['status'] === 'selesai';
        $statusDate = $done ? $finish->copy() : $this->statusDate->copy();
        $slip = (int) ($s['slip'] ?? ($done ? 0 : round(max(0, 1 - $s['spi']) * 0.35 * $start->diffInDays($finish))));

        $p = Models\Project::create([
            'portfolio_id' => $portfolio->id, 'code' => $s['code'], 'name' => $s['name'], 'business_unit' => $s['unit'],
            'project_type' => $s['ptype'], 'location' => $s['location'], 'manager_id' => $this->users[$s['pm']]->id,
            'sponsor' => $portfolio->owner_name, 'phase' => $s['phase'], 'status' => $s['status'], 'priority' => $s['priority'],
            'strategic_score' => $s['priority'] === 'tinggi' ? 5 : 3,
            'start_date' => $start, 'finish_date' => $finish->copy()->addDays($slip), 'baseline_start' => $start, 'baseline_finish' => $finish,
            'status_date' => $this->statusDate, 'budget' => $s['budget'], 'contingency' => round($s['budget'] * 0.08, -4),
            'description' => $s['desc'], 'objectives' => $s['objectives'],
            ...$this->charter($s),
        ]);

        $wp = $this->wbs($p, $s, $start, $finish, $statusDate, $slip);
        if (! in_array($s['status'], ['selesai'], true)) {
            $p->update(['finish_date' => max($finish, Carbon::parse(Models\WbsItem::where('project_id', $p->id)->max('finish_date')))]);
        }
        $this->dependencies($p);
        $this->team($p, $s, $start, $finish->copy()->addDays($slip));
        $this->risksAndIssues($p, $s);
        $this->changes($p, $s);
        $this->quality($p, $s, $wp);
        $this->stakeholders($p, $s);
        $this->contracts($p, $s, $start, $finish);
        $this->reports($p, $s, $start, $done ? $finish : $this->statusDate);
        if ($done || $s['phase'] === 'pengendalian') {
            $this->lessons($p, $s);
        }
        DemoFiles::forProject($p);
    }

    /** Isian piagam yang sama untuk semua proyek demo ini */
    protected function charter(array $s): array
    {
        return [
            'justification' => 'Selaras dengan tema strategis MedcoEnergi 2025 "Nilai. Fokus. Pertumbuhan.": alokasi modal yang disiplin, keunggulan operasional, dan investasi selektif untuk meningkatkan skala.',
            'success_criteria' => "Selesai sesuai baseline jadwal (SPI ≥ 0,95)\nBiaya dalam anggaran (CPI ≥ 0,95)\nNol LTI & kepatuhan HSSE\nDeliverable diterima pemilik aset",
            'scope_statement' => 'Lingkup mencakup seluruh deliverable pada WBS level 1 dan paket kerja level 2, dari perencanaan hingga serah terima ke operasi.',
            'out_of_scope' => 'Operasi rutin setelah serah terima, pemeliharaan jangka panjang, dan kegiatan pemasaran produk.',
            'assumptions' => "Persetujuan regulator sesuai jadwal\nHarga material sesuai estimasi kelas 3\nPendanaan proyek tersedia",
            'constraints' => "Jendela cuaca/musim untuk pekerjaan lapangan\nKetentuan TKDN\nBatas anggaran FID",
        ];
    }

    /** Penanggung jawab paket kerja WBS, bergiliran */
    protected function wbsOwners(): array
    {
        return ['Rizky Maulana', 'Agus Setiawan', 'Teguh Prasetyo', 'Indra Gunawan', 'Wulan Sari', 'Hari Kurniawan', 'Joko Susilo', 'Rina Wijayanti', 'Bayu Saputra', 'Lukman Hakim', 'Sri Handayani', 'Kartika Dewi'];
    }

    /** @return array<int,Models\WbsItem> paket kerja */
    protected function wbs(Models\Project $p, array $s, Carbon $start, Carbon $finish, Carbon $statusDate, int $slip): array
    {
        $days = $start->diffInDays($finish);
        $at = fn (float $f) => $start->copy()->addDays((int) round($f * $days));
        $resources = $this->wbsOwners();
        $sort = 0;
        $tops = [];
        $items = [];
        foreach ($this->wbsTemplate($s['type']) as $n => [$name, $weight, $fs, $fe, $deliverable, $children]) {
            $tops[$n] = Models\WbsItem::create([
                'project_id' => $p->id, 'code' => (string) ($n + 1), 'name' => $name, 'deliverable' => $deliverable,
                'sort' => $sort++, 'start_date' => $at($fs), 'finish_date' => $at($fe),
                'description' => 'Deliverable level 1: '.$deliverable.'.',
            ]);
            $totalW = array_sum(array_map(fn ($c) => $c[1], $children)) ?: 1;
            foreach ($children as $m => $c) {
                [$cname, $cw, $cs, $ce] = $c;
                $ms = ! empty($c['ms']);
                $items[] = [$n, new Models\WbsItem([
                    'project_id' => $p->id, 'parent_id' => $tops[$n]->id, 'code' => ($n + 1).'.'.($m + 1), 'name' => $cname,
                    'sort' => $sort++, 'is_milestone' => $ms, 'planned_cost' => $ms ? 0 : round($s['budget'] * $weight * $cw / $totalW, -3),
                    'baseline_start' => $at($cs), 'baseline_finish' => $at($ce),
                    'resource_id' => $this->resource($resources[($n * 3 + $m) % count($resources)])->id,
                    'deliverable' => $ms ? 'Tonggak: '.$cname : null,
                ]), $ce];
            }
        }

        // Progres "tergeser waktu": pekerjaan berjalan D hari di belakang (atau di depan) baseline,
        // D dicari agar EV ÷ PV mendekati SPI simulasi proyek.
        $done = $s['status'] === 'selesai';
        $pvAt = fn ($d) => array_sum(array_map(fn ($x) => $x[1]->planned_cost * $x[1]->plannedPercentAt($d) / 100, $items));
        $pv = $pvAt($statusDate);
        $delay = 0;
        if (! $done && $pv > 0 && abs($s['spi'] - 1) > 0.005) {
            [$lo, $hi] = $s['spi'] < 1 ? [0, $days] : [-$days, 0];
            for ($k = 0; $k < 40; $k++) {
                $mid = ($lo + $hi) / 2;
                $ratio = $pvAt($statusDate->copy()->subDays((int) round($mid))) / $pv;
                $ratio > $s['spi'] ? $lo = $mid : $hi = $mid;
            }
            $delay = (int) round(($lo + $hi) / 2);
        }
        $effective = $statusDate->copy()->subDays($delay);
        $leaves = [];
        foreach ($items as [$n, $item, $ce]) {
            $bs = $item->baseline_start;
            $bf = $item->baseline_finish;
            $pct = $done ? 100 : $item->plannedPercentAt($effective);
            if (! $done && $pct > 0 && $pct < 100) {
                $pct = min(99, max(1, $pct * $this->rnd(0.95, 1.05)));
            }
            if ($item->is_milestone) {
                $pct = $pct >= 100 ? 100 : 0;
            }
            $pct = round($pct, $pct >= 100 ? 0 : 1);
            // tanggal aktual: yang sudah selesai bergeser sebagian; yang berjalan/belum mulai bergeser penuh (≥ slip proyek)
            $shift = $done ? (int) round($slip * $ce) : ($pct >= 100 ? (int) round(max(0, $delay) * 0.5) : max($delay, (int) round($slip * $ce)));
            $item->start_date = $pct > 0 ? $bs->copy()->addDays(min($shift, (int) round(max(0, $delay) * 0.5))) : $bs->copy()->addDays($shift);
            $item->finish_date = $bf->copy()->addDays($shift);
            if ($item->finish_date->lt($item->start_date)) {
                $item->finish_date = $item->start_date->copy();
            }
            if ($pct > 0 && $pct < 100 && $item->finish_date->lte($statusDate)) {
                $item->finish_date = $statusDate->copy()->addDays(14 + (int) round((100 - $pct) / 100 * $bs->diffInDays($bf)));
            }
            if ($item->is_milestone) {
                $item->start_date = $item->finish_date;
            }
            $item->percent_complete = $pct;
            $ev = $item->planned_cost * $pct / 100;
            $item->actual_cost = $pct > 0 ? round($ev / ($s['cpi'] * $this->rnd(0.96, 1.04)), -3) : 0;
            $item->save();
            if (! $item->is_milestone) {
                $leaves[] = $item;
            }
        }

        return $leaves;
    }

    /** Keterkaitan: FS berurutan dalam satu deliverable, lalu SS/FF/SF dan lag lintas deliverable. */
    protected function dependencies(Models\Project $p): void
    {
        $items = Models\WbsItem::where('project_id', $p->id)->orderBy('sort')->get();
        $l1 = $items->where('level', 1)->values();
        $kids = fn ($top) => $items->where('parent_id', $top->id)->values();
        $add = function ($a, $b, string $type, int $lag = 0) use ($p) {
            if ($a && $b && $a->id !== $b->id) {
                Models\TaskDependency::firstOrCreate(['predecessor_id' => $a->id, 'successor_id' => $b->id], ['project_id' => $p->id, 'type' => $type, 'lag_days' => $lag]);
            }
        };
        foreach ($l1 as $top) {
            $k = $kids($top);
            for ($i = 1; $i < $k->count(); $i++) {
                if ($top->code === '1' && $i >= 2) {
                    continue; // pengendalian proyek berjalan paralel
                }
                $add($k[$i - 1], $k[$i], $k[$i]->baseline_start->lt($k[$i - 1]->baseline_finish) ? 'SS' : 'FS',
                    $k[$i]->baseline_start->lt($k[$i - 1]->baseline_finish) ? (int) $k[$i - 1]->baseline_start->diffInDays($k[$i]->baseline_start) : 0);
            }
        }
        for ($n = 1; $n + 1 < $l1->count(); $n++) {
            $a = $kids($l1[$n])->last();
            $b = $kids($l1[$n + 1])->first();
            if (! $a || ! $b) {
                continue;
            }
            if ($b->baseline_start->gte($a->baseline_finish)) {
                $add($a, $b, 'FS', (int) min(30, $a->baseline_finish->diffInDays($b->baseline_start)));
            } else {
                $add($kids($l1[$n])->first(), $b, 'SS', (int) $kids($l1[$n])->first()->baseline_start->diffInDays($b->baseline_start));
            }
        }
        // FF: deliverable terakhir selesai bersama pengendalian proyek
        $last = $l1->last();
        $ctrl = $kids($l1->first());
        if ($last && $ctrl->count() >= 3) {
            $add($kids($last)->where('is_milestone', false)->last(), $ctrl[2], 'FF', 0);
        }
        // SF: penutupan/demobilisasi tidak boleh selesai sebelum commissioning dimulai
        if ($last && $ctrl->count() >= 4) {
            $add($kids($last)->first(), $ctrl[3], 'SF', 0);
        }
        // FS dari persetujuan (milestone level 1.x) ke rekayasa
        $gate = $ctrl->firstWhere('is_milestone', true);
        if ($gate && $l1->count() > 2) {
            $add($gate, $kids($l1[2])->first(), 'FS', 0);
        }
    }

    protected function team(Models\Project $p, array $s, Carbon $start, Carbon $finish): void
    {
        $roles = $this->teamRoles($s['unit']);
        $pm = Models\Resource::where('user_id', $p->manager_id)->first();
        $team = [[$pm, 'Manajer Proyek', 100]];
        $used = [];
        foreach ($roles as [$disc, $role]) {
            $cands = array_values(array_filter($this->resources, fn ($r) => $r->discipline === $disc && ! in_array($r->id, $used)));
            if (! $cands) {
                $cands = array_values(array_filter($this->resources, fn ($r) => $r->discipline === $disc));
            }
            if ($cands) {
                $r = $cands[mt_rand(0, count($cands) - 1)];
                $used[] = $r->id;
                $team[] = [$r, $role, [25, 40, 50, 50, 60, 75, 100][mt_rand(0, 6)]];
            }
        }
        $weeks = max(1, $start->diffInWeeks($finish));
        $leaves = Models\WbsItem::where('project_id', $p->id)->where('level', 2)->where('is_milestone', false)->get();
        foreach ($team as $i => [$r, $role, $alloc]) {
            if (! $r) {
                continue;
            }
            $planned = round($weeks * 40 * $alloc / 100);
            $elapsed = $start->lt($this->statusDate) ? min(1, $start->diffInDays(min($finish, $this->statusDate)) / max(1, $start->diffInDays($finish))) : 0;
            $a = Models\Assignment::create([
                'project_id' => $p->id, 'resource_id' => $r->id, 'project_role' => $role, 'allocation' => $alloc,
                'wbs_item_id' => $i > 0 && $leaves->isNotEmpty() ? $leaves[($i * 3) % $leaves->count()]->id : null,
                'start_date' => $start, 'finish_date' => $finish, 'planned_hours' => $planned,
                'actual_hours' => round($planned * $elapsed * $this->rnd(0.9, 1.08)),
            ]);
            // lembar waktu 12 minggu terakhir untuk proyek aktif
            if ($p->status === 'aktif' && $finish->gte($this->statusDate->copy()->subWeeks(12))) {
                for ($w = 11; $w >= 0; $w--) {
                    $week = $this->statusDate->copy()->startOfWeek()->subWeeks($w);
                    if ($week->lt($start) || $week->gt($finish)) {
                        continue;
                    }
                    Models\Timesheet::create([
                        'resource_id' => $r->id, 'project_id' => $p->id, 'wbs_item_id' => $a->wbs_item_id,
                        'week_start' => $week, 'hours' => round(40 * $alloc / 100 * $this->rnd(0.8, 1.08) * 2) / 2,
                        'description' => $role.': '.($a->wbsItem?->name ?? 'koordinasi & pelaporan proyek'),
                        'status' => $w >= 2 ? 'disetujui' : ($w === 1 ? 'diajukan' : 'draf'),
                        'approved_by' => $w >= 2 ? $p->manager_id : null,
                        'approved_at' => $w >= 2 ? $week->copy()->addDays(9) : null,
                    ]);
                }
            }
        }
    }

    protected function risksAndIssues(Models\Project $p, array $s): void
    {
        $pool = $this->riskPool($s['type']);
        $n = $s['status'] === 'selesai' ? 5 : mt_rand(7, 9);
        $owners = Models\Assignment::where('project_id', $p->id)->with('resource')->get()->pluck('resource.name')->filter()->values();
        $stress = $s['spi'] < 0.9 || $s['cpi'] < 0.9;
        foreach (array_slice($pool, 0, $n) as $i => [$title, $cat, $cause, $prob, $imp, $resp, $plan]) {
            $prob = max(1, min(5, $prob + ($stress && $i < 3 ? 1 : 0) + mt_rand(-1, 1) * ($i % 2)));
            $imp = max(1, min(5, $imp));
            $status = $s['status'] === 'selesai' ? 'ditutup' : ($i < 2 && $stress ? 'terjadi' : ($prob * $imp >= 12 ? 'dipantau' : ($i % 3 === 0 ? 'ditutup' : 'terbuka')));
            $risk = Models\Risk::create([
                'project_id' => $p->id, 'code' => 'R-'.str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT), 'title' => $title, 'category' => $cat,
                'cause' => $cause, 'effect' => 'Keterlambatan jadwal dan/atau kenaikan biaya pada paket kerja terkait.',
                'probability' => $prob, 'impact' => $imp, 'response_strategy' => $resp, 'response_plan' => $plan,
                'owner' => $owners[$i % max(1, $owners->count())] ?? null, 'status' => $status,
                'cost_exposure' => round($s['budget'] * $prob * $imp / 25 * $this->rnd(0.005, 0.02), -4),
                'identified_at' => Carbon::parse($s['start'])->addDays(mt_rand(10, 120)),
                'due_date' => $this->statusDate->copy()->addDays(mt_rand(-20, 90)),
            ]);
            if ($status === 'terjadi') {
                Models\Issue::create([
                    'project_id' => $p->id, 'risk_id' => $risk->id, 'code' => 'I-0'.($i + 1), 'title' => $title.' (terjadi)',
                    'description' => 'Risiko '.$risk->code.' terealisasi dan berdampak pada jalur kritis.', 'priority' => 'tinggi',
                    'status' => 'ditangani', 'owner' => $risk->owner, 'raised_at' => $this->statusDate->copy()->subDays(mt_rand(20, 70)),
                    'due_date' => $this->statusDate->copy()->addDays(mt_rand(5, 40)), 'resolution' => 'Rencana pemulihan: '.$plan,
                ]);
            }
        }
        $issues = $this->issueList();
        foreach (array_slice($issues, 0, mt_rand(2, 4)) as $j => [$t, $prio]) {
            $closed = $s['status'] === 'selesai' || $j % 2 === 1;
            Models\Issue::create([
                'project_id' => $p->id, 'code' => 'I-1'.($j + 1), 'title' => $t, 'priority' => $prio, 'status' => $closed ? 'selesai' : 'terbuka',
                'owner' => $owners[($j + 2) % max(1, $owners->count())] ?? null,
                'raised_at' => ($s['status'] === 'selesai' ? Carbon::parse($s['finish']) : $this->statusDate)->copy()->subDays(mt_rand(15, 120)),
                'due_date' => ($s['status'] === 'selesai' ? Carbon::parse($s['finish']) : $this->statusDate)->copy()->addDays(mt_rand(-10, 30)),
                'resolution' => $closed ? 'Diselesaikan melalui rapat koordinasi mingguan.' : null,
            ]);
        }
    }

    protected function changes(Models\Project $p, array $s): void
    {
        $list = $this->changeList();
        $base = $s['status'] === 'selesai' ? Carbon::parse($s['finish'])->subMonths(8) : $this->statusDate->copy()->subMonths(6);
        foreach (array_slice($list, 0, mt_rand(2, 4)) as $i => [$t, $cat, $frac, $days]) {
            $st = $s['status'] === 'selesai' ? ($i === 2 ? 'ditolak' : 'disetujui') : ['disetujui', 'dianalisis', 'diajukan', 'ditolak'][$i];
            Models\ChangeRequest::create([
                'project_id' => $p->id, 'code' => 'CR-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT), 'title' => $t, 'category' => $cat,
                'description' => 'Diajukan melalui proses pengendalian perubahan terpadu; analisis dampak terhadap baseline lingkup, jadwal, dan biaya terlampir.',
                'cost_impact' => round($s['budget'] * $frac, -4), 'schedule_impact_days' => $days, 'status' => $st,
                'requested_by' => Models\User::find($p->manager_id)?->name, 'requested_at' => $base->copy()->addDays($i * 35),
                'decided_by' => in_array($st, ['disetujui', 'ditolak']) ? 'Change Control Board' : null,
                'decided_at' => in_array($st, ['disetujui', 'ditolak']) ? $base->copy()->addDays($i * 35 + 14) : null,
                'decision_note' => $st === 'ditolak' ? 'Ditolak: manfaat tidak sebanding dengan dampak biaya.' : ($st === 'disetujui' ? 'Disetujui dengan pembiayaan dari cadangan kontinjensi.' : null),
            ]);
        }
    }

    /** @param array<int,Models\WbsItem> $wp */
    protected function quality(Models\Project $p, array $s, array $wp): void
    {
        $checks = $this->qualityList();
        foreach ($checks as $i => [$t, $type, $std, $crit]) {
            $item = $wp[min(count($wp) - 1, (int) floor($i / count($checks) * count($wp)))] ?? null;
            $date = $item?->baseline_finish ?? $this->statusDate;
            $past = $date->lt($s['status'] === 'selesai' ? Carbon::parse($s['finish'])->addDay() : $this->statusDate);
            $result = ! $past ? 'terjadwal' : (['lulus', 'lulus', 'bersyarat', 'lulus', 'gagal', 'lulus'][($i + mt_rand(0, 2)) % 6]);
            if ($s['status'] === 'selesai' && $result === 'gagal') {
                $result = 'bersyarat';
            }
            Models\QualityCheck::create([
                'project_id' => $p->id, 'wbs_item_id' => $item?->id, 'code' => 'QC-'.str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT),
                'title' => $t, 'check_type' => $type, 'standard' => $std, 'acceptance_criteria' => $crit,
                'planned_date' => $date, 'actual_date' => $past ? $date->copy()->addDays(mt_rand(0, 6)) : null,
                'result' => $result, 'defects' => $result === 'lulus' ? mt_rand(0, 2) : ($result === 'terjadwal' ? 0 : mt_rand(3, 9)),
                'inspector' => $this->inspectors()[$i % 2],
                'notes' => $result === 'gagal' ? 'Perbaikan dan uji ulang dijadwalkan; NCR diterbitkan.' : null,
            ]);
        }
    }

    protected function stakeholders(Models\Project $p, array $s): void
    {
        [$common, $specific] = $this->stakeholderList($s['unit']);
        foreach (array_merge($specific, $common) as [$name, $pos, $org, $cat, $pow, $int, $att, $strat]) {
            Models\Stakeholder::create(['project_id' => $p->id, 'name' => $name, 'position' => $pos, 'organization' => $org, 'category' => $cat, 'power' => $pow, 'interest' => $int, 'attitude' => $att, 'engagement_strategy' => $strat]);
        }
        foreach ([
            ['Rapat kemajuan proyek', 'rapat', 'mingguan', 'Tim proyek & kontraktor', 'MS Teams / ruang rapat lokasi'],
            ['Laporan status bulanan', 'laporan', 'bulanan', 'Direksi, PMO, mitra', 'Promise & email'],
            ['Tinjauan gerbang (stage gate)', 'presentasi', 'milestone', 'Komite investasi', 'Rapat direksi'],
            ['Laporan ke regulator', 'surat', 'triwulanan', 'Regulator', 'Surat resmi & portal regulator'],
            ['Dasbor kinerja proyek', 'dasbor', 'harian', 'Seluruh pemangku kepentingan internal', 'Dasbor Promise'],
        ] as $i => [$t, $type, $freq, $aud, $ch]) {
            Models\CommunicationPlan::create(['project_id' => $p->id, 'title' => $t, 'comm_type' => $type, 'frequency' => $freq, 'audience' => $aud, 'channel' => $ch,
                'owner' => Models\User::find($p->manager_id)?->name, 'next_date' => $p->status === 'selesai' ? null : $this->statusDate->copy()->addDays([4, 30, 45, 61, 1][$i])]);
        }
    }

    protected function contracts(Models\Project $p, array $s, Carbon $start, Carbon $finish): void
    {
        $sets = $this->contractSets()[$s['type']];
        $days = $start->diffInDays($finish);
        foreach ($sets as $i => [$v, $title, $type, $frac]) {
            $value = round($s['budget'] * $frac, -4);
            $cs = $start->copy()->addDays((int) ($days * [0.12, 0.2, 0.18, 0.1][$i % 4]));
            $ce = $finish->copy()->subDays((int) ($days * [0.0, 0.08, 0.3, 0.0][$i % 4]));
            $progress = $s['status'] === 'selesai' ? 100 : (int) max(0, min(100, round($cs->lt($this->statusDate) ? $cs->diffInDays(min($ce, $this->statusDate)) / max(1, $cs->diffInDays($ce)) * 100 * $s['spi'] : 0)));
            $status = $s['status'] === 'selesai' ? 'selesai' : ($progress >= 100 ? 'selesai' : ($cs->gt($this->statusDate) ? ($cs->diffInDays($this->statusDate) > 120 ? 'perencanaan' : 'tender') : 'berjalan'));
            if ($s['phase'] === 'perencanaan' && $i > 0) {
                $status = $i === 1 ? 'evaluasi' : 'tender';
                $progress = 0;
            }
            Models\Contract::create([
                'project_id' => $p->id, 'vendor_id' => $this->vendors[$v]->id,
                'number' => sprintf('%s/CTR/%s/%02d', $p->code, $cs->format('Y'), $i + 1), 'title' => $title, 'contract_type' => $type,
                'procurement_method' => ['tender_terbuka', 'tender_terbatas', 'seleksi_langsung', 'tender_terbuka'][$i % 4],
                'value' => $value, 'paid_amount' => round($value * $progress / 100 * $this->rnd(0.85, 0.98), -3), 'progress' => $progress,
                'start_date' => $cs, 'end_date' => $ce, 'status' => $status,
            ]);
        }
    }

    /** Laporan status bulanan dengan snapshot EVM kumulatif (bahan kurva S). */
    protected function reports(Models\Project $p, array $s, Carbon $start, Carbon $until): void
    {
        $p->load('wbsItems');
        $final = $p->evm($until);
        $months = [];
        for ($m = $start->copy()->endOfMonth(); $m->lte($until->copy()->endOfMonth()); $m = $m->copy()->addDay()->endOfMonth()) {
            $months[] = $m->copy()->startOfDay();
        }
        $n = count($months);
        if ($n === 0) {
            return;
        }
        $pvs = array_map(fn ($d) => $p->evm($d->gt($until) ? $until : $d)['pv'], $months);
        $raw = [];
        foreach ($months as $i => $d) {
            $t = $n > 1 ? $i / ($n - 1) : 1;
            $spi = 1 + ($s['spi'] - 1) * pow($t, 0.7);
            $raw[] = $pvs[$i] * $spi;
        }
        $scaleEv = end($raw) > 0 ? $final['ev'] / end($raw) : 0;
        $cpiFinal = $final['ac'] > 0 ? $final['ev'] / $final['ac'] : 1;
        $prevEv = 0;
        foreach ($months as $i => $d) {
            $t = $n > 1 ? $i / ($n - 1) : 1;
            $ev = max($prevEv, min($final['bac'], $raw[$i] * $scaleEv));
            $prevEv = $ev;
            $cpi = 1 + ($cpiFinal - 1) * pow($t, 0.6);
            $ac = $i === $n - 1 ? $final['ac'] : $ev / $cpi;
            $ev = $i === $n - 1 ? $final['ev'] : $ev;
            $spiNow = $pvs[$i] > 0 ? $ev / $pvs[$i] : 1;
            $cpiNow = $ac > 0 ? $ev / $ac : 1;
            $rag = fn ($v) => $v >= 0.95 ? 'hijau' : ($v >= 0.85 ? 'kuning' : 'merah');
            Models\StatusReport::create([
                'project_id' => $p->id, 'period_date' => $d->gt($until) ? $until : $d,
                'overall_status' => $rag(min($spiNow, $cpiNow)), 'schedule_status' => $rag($spiNow), 'cost_status' => $rag($cpiNow), 'scope_status' => 'hijau',
                'planned_percent' => round($pvs[$i] / max(1, $final['bac']) * 100, 2), 'actual_percent' => round($ev / max(1, $final['bac']) * 100, 2),
                'pv' => round($pvs[$i], 2), 'ev' => round($ev, 2), 'ac' => round($ac, 2),
                'summary' => sprintf('Progres %s%% (rencana %s%%), SPI %s, CPI %s.', number_format($ev / max(1, $final['bac']) * 100, 1, ',', '.'), number_format($pvs[$i] / max(1, $final['bac']) * 100, 1, ',', '.'), number_format($spiNow, 2, ',', '.'), number_format($cpiNow, 2, ',', '.')),
                'achievements' => $this->reportAchievement(),
                'next_steps' => 'Lanjutkan paket kerja jalur kritis dan tindak lanjuti risiko teratas.',
                'prepared_by' => $this->wbsOwners()[0],
            ]);
        }
    }

    protected function lessons(Models\Project $p, array $s): void
    {
        $list = $this->lessonList($s['code']);
        foreach ($list as [$area, $kind, $title, $desc, $rec]) {
            Models\LessonLearned::create(['project_id' => $p->id, 'knowledge_area' => $area, 'kind' => $kind, 'title' => $title, 'description' => $desc, 'recommendation' => $rec,
                'recorded_at' => $s['status'] === 'selesai' ? Carbon::parse($s['finish'])->addDays(20) : $this->statusDate->copy()->subDays(30)]);
        }
    }

    // ---- Data khas demo; ditimpa oleh demo lain (mis. DemoSmbcSeeder)

    protected function wbsTemplate(string $type): array
    {
        return Templates::wbs($type);
    }

    protected function riskPool(string $type): array
    {
        return Templates::risks($type);
    }

    protected function teamRoles(string $unit): array
    {
        return match ($unit) {
            'hulu_migas' => [['Perencanaan & penjadwalan', 'Planner'], ['Pengendalian biaya', 'Cost Controller'], ['Rekayasa proses', 'Lead Process Engineer'], ['Rekayasa pengeboran', 'Drilling Engineer'], ['Pengadaan & kontrak', 'Procurement Lead'], ['QA/QC', 'QA/QC Lead'], ['HSSE', 'HSSE Lead'], ['Geologi & geofisika', 'Subsurface Lead']],
            'ketenagalistrikan' => [['Perencanaan & penjadwalan', 'Planner'], ['Pengendalian biaya', 'Cost Controller'], ['Teknik panas bumi', 'Lead Engineer'], ['Rekayasa listrik & instrumen', 'E&I Engineer'], ['Konstruksi & site manager', 'Site Manager'], ['Commissioning', 'Commissioning Lead'], ['Lingkungan & AMDAL', 'Environmental Specialist'], ['Hubungan pemerintah & perizinan', 'Permitting Lead']],
            'tambang' => [['Perencanaan & penjadwalan', 'Planner'], ['Pengendalian biaya', 'Cost Controller'], ['Metalurgi & pengolahan mineral', 'Process Lead'], ['Konstruksi & site manager', 'Construction Manager'], ['Keselamatan konstruksi', 'HSE Lead'], ['Pengadaan & kontrak', 'Contracts Lead'], ['Lingkungan & AMDAL', 'Environmental Lead'], ['Analis keuangan proyek', 'Project Finance']],
            default => [['Perencanaan & penjadwalan', 'PMO Analyst'], ['Analis keuangan proyek', 'Value Tracking'], ['Sistem informasi & data', 'Data & Dashboard Lead'], ['Hubungan masyarakat & PPM', 'Change Management']],
        };
    }

    protected function issueList(): array
    {
        return [
            ['Persetujuan desain dari pemilik aset tertunda', 'sedang'], ['Kekurangan izin kerja panas (hot work permit) di lokasi', 'rendah'],
            ['Perbedaan kuantitas material antara BOQ dan gambar IFC', 'sedang'], ['Akses data dari kontraktor belum lengkap', 'rendah'],
        ];
    }

    protected function changeList(): array
    {
        return [
            ['Penambahan lingkup sistem pemantauan emisi daring', 'lingkup', 0.006, 14],
            ['Percepatan jadwal untuk jendela cuaca', 'jadwal', 0.004, -21],
            ['Penggantian spesifikasi material ke duplex stainless steel', 'mutu', 0.009, 10],
            ['Penyesuaian nilai kontrak akibat eskalasi harga', 'biaya', 0.012, 0],
            ['Penambahan paket pengadaan suku cadang commissioning', 'pengadaan', 0.003, 0],
        ];
    }

    protected function qualityList(): array
    {
        return [
            ['Tinjauan desain 60% & 90%', 'tinjauan', 'Standar desain perusahaan', 'Semua komentar kategori A ditutup'],
            ['Inspeksi las & NDT', 'inspeksi', 'AWS D1.1 / ASME IX', 'Tingkat penolakan las < 2%'],
            ['Factory Acceptance Test peralatan utama', 'fat', 'Spesifikasi pembelian', 'Lulus seluruh parameter uji'],
            ['Uji hidrostatik sistem perpipaan', 'uji', 'ASME B31.3', 'Tidak ada kebocoran selama 4 jam'],
            ['Audit sistem mutu kontraktor', 'audit', 'ISO 9001:2015', 'Tidak ada temuan mayor'],
            ['Uji fungsi sistem kontrol (SAT)', 'uji', 'IEC 61511', 'Seluruh loop berfungsi'],
        ];
    }

    protected function inspectors(): array
    {
        return ['Sri Handayani', 'Taufik Hidayat'];
    }

    /** @return array{0: array, 1: array} [umum, khusus unit] */
    protected function stakeholderList(string $unit): array
    {
        $common = [
            ['Direksi MedcoEnergi', 'Direksi', 'PT Medco Energi Internasional Tbk', 'internal', 5, 5, 'pendukung', 'Laporan bulanan & tinjauan gerbang'],
            ['Tim Operasi Aset', 'Asset Manager', 'MedcoEnergi', 'internal', 3, 5, 'pendukung', 'Keterlibatan sejak desain untuk kesiapan operasi'],
            ['Masyarakat sekitar lokasi', 'Tokoh masyarakat', 'Desa/kecamatan sekitar', 'masyarakat', 2, 4, 'netral', 'Sosialisasi, PPM, rekrutmen tenaga lokal'],
            ['Kontraktor utama', 'Project Director', 'Kontraktor EPC/EPCI', 'pemasok', 3, 4, 'pendukung', 'Rapat kemajuan mingguan'],
        ];
        $specific = match ($unit) {
            'hulu_migas' => [['SKK Migas', 'Kepala Divisi Proyek', 'SKK Migas', 'regulator', 5, 4, 'netral', 'Pelaporan rutin, persetujuan AFE & POD'], ['Mitra PSC', 'Wakil mitra', 'Mitra hak partisipasi', 'mitra', 4, 4, 'pendukung', 'Operating committee triwulanan'], ['Pembeli gas/minyak', 'Commercial', 'Offtaker', 'pelanggan', 3, 5, 'pendukung', 'Informasi jadwal first gas/oil']],
            'ketenagalistrikan' => [['PT PLN (Persero)', 'Divisi EBT', 'PLN', 'pelanggan', 5, 5, 'pendukung', 'Koordinasi PPA, interkoneksi & COD'], ['Ditjen EBTKE', 'Direktorat Panas Bumi', 'Kementerian ESDM', 'regulator', 4, 3, 'netral', 'Pelaporan perizinan'], ['Lender proyek', 'Agent bank', 'Sindikasi perbankan', 'mitra', 4, 4, 'netral', 'Laporan lender engineer']],
            'tambang' => [['Kementerian ESDM (Minerba)', 'Ditjen Minerba', 'Kementerian ESDM', 'regulator', 5, 4, 'netral', 'Pelaporan RKAB & hilirisasi'], ['Pemda Sumbawa Barat', 'Bupati', 'Pemerintah Kabupaten', 'regulator', 4, 4, 'pendukung', 'Koordinasi tenaga kerja & infrastruktur'], ['PT Amman Mineral Internasional Tbk', 'Direksi', 'AMMN', 'internal', 5, 5, 'pendukung', 'Tinjauan proyek bulanan']],
            default => [['Manajemen aset Medco Power', 'GM Aset', 'Medco Power', 'internal', 4, 5, 'netral', 'Lokakarya inisiatif & coaching'], ['Konsultan transformasi', 'Partner', 'Konsultan internasional', 'pemasok', 3, 4, 'pendukung', 'Rapat PMO mingguan']],
        };

        return [$common, $specific];
    }

    protected function contractSets(): array
    {
        return [
            'offshore' => [['NRL', 'Kontrak EPCI fasilitas lepas pantai', 'epci', 0.42], ['SDS', 'Jasa pengeboran & rig jack-up', 'unit_price', 0.22], ['KPI', 'Pengadaan pipa, katup & wellhead', 'lump_sum', 0.10], ['LOG', 'Logistik marine & kapal pendukung', 'tm', 0.05]],
            'drilling' => [['SDS', 'Sewa rig & jasa pengeboran terpadu', 'unit_price', 0.55], ['KPI', 'Casing, tubing & wellhead', 'lump_sum', 0.18], ['LOG', 'Logistik & supply vessel', 'tm', 0.06]],
            'onshore' => [['KSB', 'EPC fasilitas produksi gas', 'lump_sum', 0.38], ['SDS', 'Jasa pengeboran sumur pengembangan', 'unit_price', 0.25], ['KPI', 'Pipa transmisi & material', 'lump_sum', 0.12], ['ENV', 'Studi AMDAL & pemantauan lingkungan', 'lump_sum', 0.01]],
            'geothermal' => [['GEO', 'EPC pembangkit & SAGS', 'epci', 0.45], ['SDS', 'Pengeboran sumur produksi & reinjeksi', 'unit_price', 0.22], ['TRF', 'Gardu induk & SUTT 150 kV', 'lump_sum', 0.07]],
            'ccgt' => [['TRB', 'Pasokan HRSG & turbin uap', 'lump_sum', 0.40], ['KSB', 'Konstruksi sipil & instalasi', 'unit_price', 0.20], ['ELK', 'Integrasi DCS & instrumentasi', 'lump_sum', 0.06]],
            'solar' => [['SLR', 'EPC PLTS 25 MWp', 'epci', 0.70], ['TRF', 'Trafo & gardu hubung', 'lump_sum', 0.08]],
            'exploration' => [['SDS', 'Pengeboran eksplorasi panas bumi', 'unit_price', 0.45], ['KSB', 'Jalan akses & wellpad', 'unit_price', 0.15], ['ENV', 'UKL-UPL & survei geosains', 'lump_sum', 0.06]],
            'mining' => [['MIN', 'Pasokan SAG mill, ball mill & flotasi', 'lump_sum', 0.28], ['KSB', 'Pekerjaan tanah, fondasi & struktur', 'unit_price', 0.22], ['ELK', 'Kelistrikan & sistem kontrol pabrik', 'lump_sum', 0.10], ['LOG', 'Logistik heavy-lift', 'tm', 0.03]],
            'study' => [['MIN', 'Konsultan FEED pabrik pengolahan', 'cost_plus', 0.30], ['ENV', 'Studi ANDAL & konsultasi publik', 'lump_sum', 0.08], ['SDS', 'Pengeboran inti konfirmasi', 'unit_price', 0.25]],
            'transformation' => [['MGT', 'Konsultan transformasi operasional', 'tm', 0.55], ['ELK', 'Platform dasbor KPI', 'lump_sum', 0.10]],
        ];
    }

    protected function reportAchievement(): string
    {
        return 'Pekerjaan paket kerja berjalan sesuai rencana kerja bulanan; tidak ada insiden LTI.';
    }

    protected function lessonList(string $code): array
    {
        return match ($code) {
            'NTN-TRB' => [
                ['integrasi', 'positif', 'Model EPCI terpadu dari FEED hingga Ready For Start Up', 'Strategi Rekayasa, Pengadaan, Konstruksi dan Pemasangan terpadu mempercepat pengembangan lapangan marginal.', 'Gunakan model yang sama untuk WHP tanpa awak berikutnya.'],
                ['biaya', 'positif', 'Penghematan 20% pengadaan & fabrikasi jacket', 'Standarisasi desain jacket dan negosiasi paket menghasilkan penghematan rata-rata 20% dan 13% pada barang long lead.', 'Jadikan desain jacket standar sebagai referensi estimasi.'],
                ['mutu', 'positif', 'Platform bertenaga surya menurunkan intensitas emisi 11%', 'Operasi WHP tanpa awak dengan tenaga surya menurunkan intensitas emisi GRK.', 'Masukkan opsi tenaga surya pada FEED platform berikutnya.'],
            ],
            'NTN-FRL' => [
                ['pengadaan', 'positif', 'Konversi tanker menjadi FPSO', 'Konversi kapal tanker menjadi FPSO memberi alternatif lebih cepat dibanding pembangunan baru.', 'Evaluasi konversi untuk lapangan dengan umur produksi menengah.'],
                ['waktu', 'perbaikan', 'Jendela cuaca instalasi perlu bufer lebih besar', 'Instalasi offshore sempat tertahan gelombang tinggi musim angin utara.', 'Tambahkan bufer 3–4 minggu pada aktivitas marine Nov–Feb.'],
            ],
            default => [
                ['risiko', 'perbaikan', 'Identifikasi risiko pengadaan lebih awal', 'Keterlambatan barang long lead menjadi penyebab utama penyimpangan jadwal.', 'Lakukan expediting sejak tahap FEED dan inspeksi di pabrik.'],
                ['komunikasi', 'positif', 'Rapat kemajuan mingguan dengan dasbor tunggal', 'Satu sumber data (Promise) mempercepat keputusan lintas fungsi.', 'Wajibkan pembaruan progres paket kerja sebelum rapat.'],
                ['sdm', 'perbaikan', 'Rencana suksesi tenaga ahli commissioning', 'Persaingan SDM antarproyek menunda mobilisasi tim commissioning.', 'Kontrak vendor representatif lebih awal.'],
            ],
        };
    }
}
