<?php

namespace App\Support;

use App\Models\Project;
use Illuminate\Support\Facades\Schema;

/**
 * Peringatan data demo yang ditampilkan di aplikasi, halaman depan, dan dokumen contoh.
 */
class DemoData
{
    public const TITLE = 'DATA DEMO, bukan proyek yang sesungguhnya.';

    public const NOTICE = 'Seluruh proyek contoh hanya untuk keperluan pembelajaran dan peragaan fitur Promise. Nama proyek mengacu pada Laporan Tahunan 2025 PT Medco Energi Internasional Tbk, tetapi jadwal, anggaran, progres, risiko, kontrak, vendor, personel, dan dokumen adalah simulasi, bukan data resmi perusahaan.';

    public const SHORT = 'DATA DEMO - bukan proyek yang sesungguhnya, hanya untuk keperluan pembelajaran.';

    /** Kode proyek pertama dari DemoMedcoSeeder sebagai penanda data demo terpasang */
    public const MARKER_CODE = 'NTN-FRL';

    public static function present(): bool
    {
        return once(function () {
            try {
                return Schema::hasTable('projects') && Project::where('code', self::MARKER_CODE)->exists();
            } catch (\Throwable) {
                return false;
            }
        });
    }

    public static function text(): string
    {
        return self::TITLE.' '.self::NOTICE;
    }
}
