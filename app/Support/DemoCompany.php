<?php

namespace App\Support;

use App\Models\Portfolio;
use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

/**
 * Konteks perusahaan data demo. Pengunjung memilih perusahaan (kartu di halaman login/depan) dan
 * seluruh data proyek beserta turunannya disaring ke perusahaan itu. Data tanpa label perusahaan
 * (misalnya proyek yang dibuat sendiri) tetap tampil di semua konteks.
 */
class DemoCompany
{
    public const SESSION = 'promise.company';

    public const COMPANIES = [
        'medco' => [
            'name' => 'PT Medco Energi Internasional Tbk',
            'short' => 'MedcoEnergi',
            'sector' => 'Energi & pertambangan',
            'marker' => 'NTN-FRL',
            'color' => '#0F5E9C',
            'summary' => 'Pengembangan lapangan migas, pembangkit listrik & energi terbarukan, serta tembaga & emas (AMNT).',
        ],
        'smbc' => [
            'name' => 'PT Bank SMBC Indonesia Tbk',
            'short' => 'SMBC Indonesia',
            'sector' => 'Perbankan',
            'marker' => 'SMB-CBS',
            'color' => '#2E7D4F',
            'summary' => 'Core banking, AI generatif, cloud, keamanan siber, Jenius, TOUCHBIZ, dan keuangan berkelanjutan.',
        ],
        'pakuwon' => [
            'name' => 'PT Pakuwon Jati Tbk',
            'short' => 'Pakuwon Jati',
            'sector' => 'Properti & perhotelan',
            'marker' => 'PWN-SMG',
            'color' => '#B4462B',
            'summary' => 'Superblok & mal (Pakuwon Mall Semarang, Kota Kasablanka), hotel baru, hunian, dan modernisasi aset.',
        ],
    ];

    protected static int $bypass = 0;

    public static function valid(?string $key): bool
    {
        return $key !== null && isset(self::COMPANIES[$key]);
    }

    /**
     * Perusahaan yang sedang ditampilkan. Data demo selalu tersaring ke satu perusahaan (tidak ada tampilan
     * gabungan): bila belum dipilih, dipakai perusahaan demo pertama. Null hanya bila tidak ada data demo
     * atau di luar permintaan web (konsol, antrean).
     */
    public static function current(): ?string
    {
        if (static::$bypass > 0 || ! app()->bound('request')) {
            return null;
        }
        $request = request();
        if (! $request->hasSession()) {
            return null;
        }
        $key = $request->session()->get(self::SESSION);
        $available = static::available();
        if (isset($available[$key])) {
            return $key;
        }

        return array_key_first($available);
    }

    public static function set(?string $key): void
    {
        $session = request()->session();
        static::valid($key) ? $session->put(self::SESSION, $key) : $session->forget(self::SESSION);
    }

    /** Pilihan sah dari permintaan, atau perusahaan demo pertama. */
    public static function resolve(?string $key): ?string
    {
        $available = static::available();

        return isset($available[$key]) ? $key : array_key_first($available);
    }

    public static function name(?string $key = null): ?string
    {
        $key ??= static::current();

        return self::COMPANIES[$key]['name'] ?? null;
    }

    /** Jalankan tanpa penyaringan perusahaan (statistik halaman depan, pemeriksaan data demo). */
    public static function bypass(callable $callback): mixed
    {
        static::$bypass++;
        try {
            return $callback();
        } finally {
            static::$bypass--;
        }
    }

    /** Perusahaan demo yang datanya ada di basis data, dengan jumlah proyek dan portofolio. */
    public static function available(): array
    {
        return once(fn () => static::bypass(function () {
            try {
                if (! Schema::hasTable('projects') || ! Schema::hasColumn('projects', 'company')) {
                    return [];
                }
                $out = [];
                foreach (self::COMPANIES as $key => $c) {
                    if (! Project::where('code', $c['marker'])->exists()) {
                        continue;
                    }
                    $out[$key] = $c + [
                        'key' => $key,
                        'projects' => Project::where('company', $key)->count(),
                        'portfolios' => Portfolio::where('company', $key)->count(),
                        'budget' => (float) Project::where('company', $key)->sum('budget'),
                    ];
                }

                return $out;
            } catch (\Throwable) {
                return [];
            }
        }));
    }

    /** Saring model yang memiliki kolom `company`. */
    public static function scopeOwn(Builder $query): void
    {
        $key = static::current();
        if ($key === null) {
            return;
        }
        $column = $query->getModel()->qualifyColumn('company');
        $query->where(fn (Builder $q) => $q->whereNull($column)->orWhere($column, $key));
    }

    /** Saring model turunan proyek melalui relasi `project`. */
    public static function scopeViaProject(Builder $query, bool $nullable = false): void
    {
        if (static::current() === null) {
            return;
        }
        if (! $nullable) {
            $query->whereHas('project');

            return;
        }
        $column = $query->getModel()->qualifyColumn('project_id');
        $query->where(fn (Builder $q) => $q->whereNull($column)->orWhereHas('project'));
    }
}
