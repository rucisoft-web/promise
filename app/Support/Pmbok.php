<?php

namespace App\Support;

/**
 * Kosakata Promise yang mengacu pada PMBOK Guide (PMI): kelompok proses, area pengetahuan,
 * dan pilihan isian yang dipakai di seluruh modul.
 */
class Pmbok
{
    /** Kelompok proses PMBOK, dipakai sebagai fase siklus hidup proyek. */
    public const PHASES = [
        'inisiasi' => 'Inisiasi',
        'perencanaan' => 'Perencanaan',
        'eksekusi' => 'Eksekusi',
        'pengendalian' => 'Pemantauan & Pengendalian',
        'penutupan' => 'Penutupan',
    ];

    /** Sembilan area pengetahuan (PMBOK edisi 4 & 5 tanpa Pemangku Kepentingan sebagai area terpisah). */
    public const KNOWLEDGE_AREAS = [
        'integrasi' => 'Integrasi',
        'lingkup' => 'Ruang Lingkup',
        'waktu' => 'Waktu',
        'biaya' => 'Biaya',
        'mutu' => 'Mutu',
        'sdm' => 'Sumber Daya Manusia',
        'komunikasi' => 'Komunikasi',
        'risiko' => 'Risiko',
        'pengadaan' => 'Pengadaan',
    ];

    /** Jenis keterkaitan Precedence Diagramming Method. */
    public const DEPENDENCY_TYPES = [
        'FS' => 'FS · Selesai–Mulai (Finish-to-Start)',
        'SS' => 'SS · Mulai–Mulai (Start-to-Start)',
        'FF' => 'FF · Selesai–Selesai (Finish-to-Finish)',
        'SF' => 'SF · Mulai–Selesai (Start-to-Finish)',
    ];

    public const PROJECT_STATUSES = [
        'usulan' => 'Usulan',
        'aktif' => 'Aktif',
        'ditunda' => 'Ditunda',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];

    public const PRIORITIES = ['tinggi' => 'Tinggi', 'sedang' => 'Sedang', 'rendah' => 'Rendah'];

    public const BUSINESS_UNITS = [
        'hulu_migas' => 'Hulu Minyak & Gas',
        'ketenagalistrikan' => 'Ketenagalistrikan',
        'tambang' => 'Tambang Tembaga & Emas',
        'perbankan' => 'Perbankan Digital',
        'teknologi' => 'Operasional & Teknologi',
        'keberlanjutan' => 'Keuangan Berkelanjutan',
        'properti' => 'Pengembangan Properti',
        'ritel' => 'Ritel & Mal',
        'perhotelan' => 'Perhotelan',
        'korporat' => 'Korporat & Pendukung',
    ];

    public const RAG = ['hijau' => 'Hijau', 'kuning' => 'Kuning', 'merah' => 'Merah'];

    public const RAG_COLORS = ['hijau' => 'success', 'kuning' => 'warning', 'merah' => 'danger'];

    public const RISK_CATEGORIES = [
        'teknis' => 'Teknis', 'hsse' => 'HSSE', 'komersial' => 'Komersial', 'regulasi' => 'Regulasi & Perizinan',
        'pengadaan' => 'Pengadaan & Rantai Pasok', 'eksternal' => 'Eksternal/Alam', 'organisasi' => 'Organisasi & SDM',
        'keuangan' => 'Keuangan', 'siber' => 'Keamanan Siber & TI',
    ];

    /** Strategi respons risiko PMBOK: ancaman dan peluang. */
    public const RISK_RESPONSES = [
        'hindari' => 'Hindari (avoid)', 'alihkan' => 'Alihkan (transfer)', 'mitigasi' => 'Mitigasi (mitigate)',
        'terima' => 'Terima (accept)', 'eskalasi' => 'Eskalasi (escalate)', 'eksploitasi' => 'Eksploitasi (peluang)',
        'tingkatkan' => 'Tingkatkan (peluang)',
    ];

    public const RISK_STATUSES = ['terbuka' => 'Terbuka', 'dipantau' => 'Dipantau', 'terjadi' => 'Terjadi', 'ditutup' => 'Ditutup'];

    public const ISSUE_STATUSES = ['terbuka' => 'Terbuka', 'ditangani' => 'Ditangani', 'selesai' => 'Selesai'];

    public const CR_CATEGORIES = ['lingkup' => 'Ruang lingkup', 'jadwal' => 'Jadwal', 'biaya' => 'Biaya', 'mutu' => 'Mutu', 'pengadaan' => 'Pengadaan'];

    public const CR_STATUSES = ['diajukan' => 'Diajukan', 'dianalisis' => 'Dianalisis', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'];

    public const QUALITY_TYPES = ['inspeksi' => 'Inspeksi', 'uji' => 'Pengujian', 'audit' => 'Audit mutu', 'tinjauan' => 'Tinjauan desain', 'fat' => 'FAT/SAT'];

    public const QUALITY_RESULTS = ['terjadwal' => 'Terjadwal', 'lulus' => 'Lulus', 'bersyarat' => 'Lulus bersyarat', 'gagal' => 'Gagal'];

    public const STAKEHOLDER_CATEGORIES = ['internal' => 'Internal', 'mitra' => 'Mitra/JV', 'regulator' => 'Regulator/Pemerintah', 'pelanggan' => 'Pelanggan/Offtaker', 'masyarakat' => 'Masyarakat', 'pemasok' => 'Pemasok/Kontraktor'];

    public const ATTITUDES = ['pendukung' => 'Pendukung', 'netral' => 'Netral', 'penentang' => 'Penentang'];

    public const COMM_TYPES = ['rapat' => 'Rapat', 'laporan' => 'Laporan', 'presentasi' => 'Presentasi', 'surat' => 'Surat resmi', 'dasbor' => 'Dasbor daring'];

    public const FREQUENCIES = ['harian' => 'Harian', 'mingguan' => 'Mingguan', 'dua_mingguan' => 'Dua mingguan', 'bulanan' => 'Bulanan', 'triwulanan' => 'Triwulanan', 'milestone' => 'Saat milestone'];

    public const RESOURCE_TYPES = ['tenaga_kerja' => 'Tenaga kerja', 'kontraktor' => 'Tenaga kontrak', 'peralatan' => 'Peralatan'];

    public const TIMESHEET_STATUSES = ['draf' => 'Draf', 'diajukan' => 'Diajukan', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'];

    public const CONTRACT_TYPES = ['lump_sum' => 'Lump sum', 'unit_price' => 'Harga satuan', 'cost_plus' => 'Cost plus fee', 'epci' => 'EPCI terima jadi', 'tm' => 'Time & material'];

    public const PROCUREMENT_METHODS = ['tender_terbuka' => 'Tender terbuka', 'tender_terbatas' => 'Tender terbatas', 'seleksi_langsung' => 'Seleksi langsung', 'penunjukan_langsung' => 'Penunjukan langsung'];

    public const CONTRACT_STATUSES = ['perencanaan' => 'Perencanaan', 'tender' => 'Proses tender', 'evaluasi' => 'Evaluasi', 'berjalan' => 'Berjalan', 'selesai' => 'Selesai', 'terminasi' => 'Terminasi'];

    public const ROLES = [
        'admin' => 'Administrator',
        'pmo' => 'PMO / Portofolio',
        'manajer' => 'Manajer Proyek',
        'anggota' => 'Anggota Tim',
        'eksekutif' => 'Eksekutif (lihat)',
        'tamu' => 'Tamu (demo)',
    ];

    /** Skor risiko = probabilitas × dampak (1–25). */
    public static function riskLevel(int $score): string
    {
        return match (true) {
            $score >= 15 => 'tinggi',
            $score >= 8 => 'sedang',
            default => 'rendah',
        };
    }

    public static function riskColor(int $score): string
    {
        return ['tinggi' => 'danger', 'sedang' => 'warning', 'rendah' => 'success'][self::riskLevel($score)];
    }
}
