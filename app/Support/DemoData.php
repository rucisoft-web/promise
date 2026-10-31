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

    public const NOTICE = 'Seluruh proyek contoh hanya untuk keperluan pembelajaran dan peragaan fitur Promise. Nama proyek mengacu pada Laporan Tahunan 2025 PT Medco Energi Internasional Tbk dan PT Bank SMBC Indonesia Tbk, tetapi jadwal, anggaran, progres, risiko, kontrak, vendor, personel, dan dokumen adalah simulasi, bukan data resmi perusahaan tersebut.';

    public const SHORT = 'DATA DEMO - bukan proyek yang sesungguhnya, hanya untuk keperluan pembelajaran.';

    /** Kode proyek penanda tiap demo (DemoMedcoSeeder, DemoSmbcSeeder) */
    public const MARKER_CODES = ['NTN-FRL', 'SMB-CBS'];

    public static function present(): bool
    {
        return once(function () {
            try {
                return Schema::hasTable('projects') && Project::whereIn('code', self::MARKER_CODES)->exists();
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
