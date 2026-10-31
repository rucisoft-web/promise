<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Promise memakai mata uang tunggal Rupiah (IDR). Data demo disusun dalam setara US$ lalu dikonversi
 * ke Rupiah dengan kurs asumsi tetap agar seluruh angka (anggaran, EVM, kontrak, risiko) konsisten.
 */
class Rupiah
{
    /** Kurs asumsi data demo: US$ 1 = Rp 16.500 */
    public const DEMO_RATE = 16500;

    /** Kolom uang per tabel yang terkait proyek */
    public const PROJECT_MONEY = [
        'wbs_items' => ['planned_cost', 'actual_cost'],
        'risks' => ['cost_exposure'],
        'change_requests' => ['cost_impact'],
        'status_reports' => ['pv', 'ev', 'ac'],
        'contracts' => ['value', 'paid_amount'],
    ];

    /** @param array<int,int>|\Illuminate\Support\Collection $projectIds */
    public static function convertProjects($projectIds, float $rate = self::DEMO_RATE): void
    {
        $ids = collect($projectIds)->values()->all();
        if ($ids === []) {
            return;
        }
        DB::table('projects')->whereIn('id', $ids)->update(self::multiplied(['budget', 'contingency'], $rate));
        foreach (self::PROJECT_MONEY as $table => $cols) {
            DB::table($table)->whereIn('project_id', $ids)->update(self::multiplied($cols, $rate));
        }
    }

    /** @param array<int,string> $cols */
    public static function multiplied(array $cols, float $rate): array
    {
        $r = (int) $rate;

        return collect($cols)->mapWithKeys(fn ($c) => [$c => DB::raw('ROUND('.$c.' * '.$r.', 0)')])->all();
    }
}
