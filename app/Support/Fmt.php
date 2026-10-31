<?php

namespace App\Support;

class Fmt
{
    /** Mata uang tunggal Promise: Rupiah (IDR). */
    public const CURRENCY = 'Rp';

    /** Pembagi & satuan sumbu grafik nilai uang (Rp miliar). */
    public const CHART_DIVISOR = 1e9;

    public const CHART_UNIT = 'Rp miliar';

    /** Uang ringkas: Rp 6,93 T / Rp 12,5 M / Rp 350 jt / Rp 275 rb. */
    public static function money(float|int|string|null $value, bool $short = true): string
    {
        $v = (float) $value;
        $cur = self::CURRENCY;
        if (! $short) {
            return $cur.' '.number_format($v, 0, ',', '.');
        }
        $abs = abs($v);

        return $cur.' '.match (true) {
            $abs >= 1e12 => number_format($v / 1e12, 2, ',', '.').' T',
            $abs >= 1e9 => number_format($v / 1e9, 1, ',', '.').' M',
            $abs >= 1e6 => number_format($v / 1e6, 0, ',', '.').' jt',
            $abs >= 1e3 => number_format($v / 1e3, 0, ',', '.').' rb',
            default => number_format($v, 0, ',', '.'),
        };
    }

    /** Nilai untuk grafik dalam Rp miliar. */
    public static function chart(float|int|string|null $value): float
    {
        return round((float) $value / self::CHART_DIVISOR, 2);
    }

    public static function num(float|int|string|null $value, int $dec = 0): string
    {
        return number_format((float) $value, $dec, ',', '.');
    }

    public static function pct(float|int|string|null $value, int $dec = 1): string
    {
        return number_format((float) $value, $dec, ',', '.').'%';
    }

    public static function index(?float $value): string
    {
        return $value === null ? '–' : number_format($value, 2, ',', '.');
    }
}
