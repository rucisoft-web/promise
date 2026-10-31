<?php

namespace App\Support;

class Fmt
{
    /** Uang ringkas: US$ 12,5 jt / US$ 1,2 M. */
    public static function money(float|int|string|null $value, bool $short = true): string
    {
        $v = (float) $value;
        $cur = config('promise.currency', 'US$');
        if (! $short) {
            return $cur.' '.number_format($v, 0, ',', '.');
        }
        $abs = abs($v);

        return $cur.' '.match (true) {
            $abs >= 1e9 => number_format($v / 1e9, 2, ',', '.').' M',
            $abs >= 1e6 => number_format($v / 1e6, 1, ',', '.').' jt',
            $abs >= 1e3 => number_format($v / 1e3, 0, ',', '.').' rb',
            default => number_format($v, 0, ',', '.'),
        };
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
