<?php

namespace App\Support;

use App\Models;

/** Pilihan record tujuan lampiran untuk satu proyek (dipakai form Pusat Dokumen). */
class DocumentTargets
{
    /** @return array<string,array<string,string>> */
    public static function options(int $projectId): array
    {
        if (! $projectId) {
            return [];
        }
        $out = [];
        $add = function (string $group, string $class, $rows, callable $label) use (&$out) {
            foreach ($rows as $r) {
                $out[$group][$class.'#'.$r->id] = $label($r);
            }
        };
        $add('Tugas WBS', Models\WbsItem::class, Models\WbsItem::where('project_id', $projectId)->orderBy('sort')->get(), fn ($r) => $r->code.' '.$r->name);
        $add('Risiko', Models\Risk::class, Models\Risk::where('project_id', $projectId)->get(), fn ($r) => $r->code.' '.$r->title);
        $add('Isu', Models\Issue::class, Models\Issue::where('project_id', $projectId)->get(), fn ($r) => $r->code.' '.$r->title);
        $add('Permintaan perubahan', Models\ChangeRequest::class, Models\ChangeRequest::where('project_id', $projectId)->get(), fn ($r) => $r->code.' '.$r->title);
        $add('Pemeriksaan mutu', Models\QualityCheck::class, Models\QualityCheck::where('project_id', $projectId)->get(), fn ($r) => $r->code.' '.$r->title);
        $add('Kontrak', Models\Contract::class, Models\Contract::where('project_id', $projectId)->get(), fn ($r) => $r->number.' '.$r->title);
        $add('Laporan status', Models\StatusReport::class, Models\StatusReport::where('project_id', $projectId)->orderBy('period_date')->get(), fn ($r) => 'Laporan '.$r->period_date->translatedFormat('F Y'));

        return $out;
    }
}
