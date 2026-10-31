<?php

namespace Database\Seeders\Demo;

use App\Models;
use App\Support\DemoData;
use App\Support\Fmt;
use Illuminate\Support\Facades\Storage;

/**
 * Berkas contoh untuk Pusat Dokumen: PDF (piagam, laporan status, ringkasan kontrak),
 * XLSX (baseline jadwal & biaya WBS), DOCX (notulen), JPG (foto/ilustrasi lapangan).
 * Semua isi adalah DATA DEMO.
 */
class DemoFiles
{
    public static function forProject(Models\Project $p): void
    {
        $dir = Models\Document::DIRECTORY.'/demo/'.strtolower($p->code);
        $disk = Storage::disk(Models\Document::DISK);
        $wbs = Models\WbsItem::where('project_id', $p->id)->orderBy('sort')->get();
        $mgr = $p->manager?->name ?? '-';

        $charter = array_merge([
            'PIAGAM PROYEK (PROJECT CHARTER)', $p->code.' - '.$p->name, '',
            'Lokasi: '.$p->location, 'Sponsor: '.$p->sponsor, 'Manajer proyek: '.$mgr,
            'Jadwal baseline: '.$p->baseline_start?->format('d/m/Y').' s.d. '.$p->baseline_finish?->format('d/m/Y'),
            'Anggaran (BAC): '.Fmt::money($p->budget, false), '', 'Deskripsi:',
        ], self::wrap($p->description), ['', 'Tujuan terukur:'], explode("\n", (string) $p->objectives), ['', 'Kriteria keberhasilan:'], explode("\n", (string) $p->success_criteria),
            ['', 'Deliverable WBS level 1:'], $wbs->where('level', 1)->map(fn ($w) => $w->code.'  '.$w->name)->all(),
            ['', DemoData::SHORT, 'Disusun untuk peragaan perangkat lunak Promise, bukan dokumen resmi perusahaan.']);
        self::store($p, $p, $dir.'/piagam-proyek.pdf', self::pdf($charter), 'Piagam Proyek '.$p->code, 'laporan', 'PC-'.$p->code.'-001', '1');

        $rows = [['Kode WBS', 'Elemen', 'Level', 'Mulai baseline', 'Selesai baseline', 'Anggaran (US$)', 'Milestone']];
        foreach ($wbs as $w) {
            $rows[] = [$w->code, $w->name, $w->level, $w->baseline_start?->format('Y-m-d'), $w->baseline_finish?->format('Y-m-d'), (float) $w->planned_cost, $w->is_milestone ? 'Ya' : ''];
        }
        $rows[] = [];
        $rows[] = [DemoData::SHORT];
        if ($xlsx = self::xlsx($rows)) {
            self::store($p, $p, $dir.'/baseline-jadwal-biaya.xlsx', $xlsx, 'Baseline jadwal & biaya WBS', 'spesifikasi', 'PB-'.$p->code.'-002', '0');
        }

        $minutes = ['Notulen Rapat Kick-off '.$p->code, 'Proyek: '.$p->name, 'Tanggal: '.$p->start_date?->translatedFormat('d F Y'), 'Pimpinan rapat: '.$mgr, '',
            'Agenda: piagam proyek, organisasi tim, baseline jadwal, rencana komunikasi, risiko awal.',
            'Keputusan: baseline WBS level 1-2 disetujui; laporan status bulanan memakai Promise; rapat kemajuan setiap minggu.',
            'Tindak lanjut: PM menyiapkan register risiko awal; Planner menerbitkan jadwal baseline; Procurement menyusun rencana pengadaan.',
            '', DemoData::SHORT];
        if ($docx = self::docx($minutes)) {
            self::store($p, $p, $dir.'/notulen-kick-off.docx', $docx, 'Notulen rapat kick-off', 'notulen', 'MOM-'.$p->code.'-001', '0');
        }

        $site = $wbs->first(fn ($w) => $w->level === 2 && ! $w->is_milestone && preg_match('/konstruksi|fabrikasi|pengeboran|instalasi|sipil/i', $w->name))
            ?? $wbs->firstWhere('level', 2);
        if ($site && ($jpg = self::jpg($p->code.' · '.$site->name, $site->percent_complete, $p->portfolio?->color ?? '#0F5E9C'))) {
            self::store($p, $site, $dir.'/foto-progres-'.str_replace('.', '-', $site->code).'.jpg', $jpg, 'Foto progres '.$site->code, 'foto', null, '0');
        }

        if ($report = Models\StatusReport::where('project_id', $p->id)->latest('period_date')->first()) {
            $lines = ['LAPORAN STATUS PROYEK', $p->code.' - '.$p->name, 'Periode: '.$report->period_date->translatedFormat('F Y'), '',
                'Status umum: '.strtoupper($report->overall_status).'  |  Jadwal: '.$report->schedule_status.'  |  Biaya: '.$report->cost_status,
                'Progres aktual: '.Fmt::pct($report->actual_percent).'  (rencana '.Fmt::pct($report->planned_percent).')',
                'PV: '.Fmt::money($report->pv).'   EV: '.Fmt::money($report->ev).'   AC: '.Fmt::money($report->ac), '', $report->summary, '',
                'Capaian: '.$report->achievements, 'Rencana berikutnya: '.$report->next_steps, '', DemoData::SHORT];
            self::store($p, $report, $dir.'/laporan-status-'.$report->period_date->format('Y-m').'.pdf', self::pdf($lines), 'Laporan status '.$report->period_date->translatedFormat('F Y'), 'laporan', 'SR-'.$p->code.'-'.$report->period_date->format('ym'), '0');
        }

        if ($contract = Models\Contract::with('vendor')->where('project_id', $p->id)->orderByDesc('value')->first()) {
            $lines = ['RINGKASAN KONTRAK', $contract->number, $contract->title, '', 'Vendor: '.$contract->vendor?->name, 'Nilai: '.Fmt::money($contract->value, false),
                'Jangka waktu: '.$contract->start_date?->format('d/m/Y').' s.d. '.$contract->end_date?->format('d/m/Y'), 'Status: '.$contract->status, '', DemoData::SHORT, 'Vendor fiktif.'];
            self::store($p, $contract, $dir.'/ringkasan-kontrak.pdf', self::pdf($lines), 'Ringkasan kontrak '.$contract->number, 'kontrak', $contract->number, 'A');
        }

        if ($risk = Models\Risk::where('project_id', $p->id)->orderByRaw('probability * impact desc')->first()) {
            $lines = ['LEMBAR ANALISIS RISIKO', $risk->code.' '.$risk->title, '', 'Penyebab: '.$risk->cause, 'Probabilitas x Dampak: '.$risk->probability.' x '.$risk->impact.' = '.$risk->score,
                'Strategi respons: '.$risk->response_strategy, 'Rencana: '.$risk->response_plan, 'Pemilik: '.$risk->owner, '', DemoData::SHORT];
            self::store($p, $risk, $dir.'/analisis-risiko-'.strtolower($risk->code).'.pdf', self::pdf($lines), 'Analisis risiko '.$risk->code, 'laporan', null, '0');
        }
    }

    protected static function store(Models\Project $p, $attachable, string $path, string $bytes, string $title, string $category, ?string $no, string $rev): void
    {
        Storage::disk(Models\Document::DISK)->put($path, $bytes);
        $doc = new Models\Document([
            'project_id' => $p->id, 'title' => $title, 'category' => $category, 'document_no' => $no, 'revision' => $rev,
            'file_path' => $path, 'original_name' => basename($path),
        ]);
        $doc->attachable()->associate($attachable);
        $doc->uploaded_by = $p->manager_id;
        $doc->save();
    }

    /** @return array<int,string> */
    protected static function wrap(?string $text, int $width = 92): array
    {
        return explode("\n", wordwrap((string) $text, $width, "\n", true));
    }

    /** PDF satu halaman (Helvetica) tanpa pustaka tambahan. */
    public static function pdf(array $lines): string
    {
        $esc = fn ($s) => str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], iconv('UTF-8', 'Windows-1252//TRANSLIT', (string) $s) ?: '');
        $stream = "BT /F1 15 Tf 56 790 Td 18 TL\n";
        $first = true;
        foreach (array_slice($lines, 0, 44) as $i => $line) {
            if ($i === 1) {
                $stream .= "/F1 10 Tf 14 TL\n";
            }
            $stream .= '('.$esc($line).") '\n";
        }
        $stream .= "ET\n0.06 0.37 0.61 RG 2 w 56 806 m 539 806 l S\n";
        // Catatan kaki di setiap PDF contoh
        $stream .= "BT /F1 8 Tf 0.71 0.33 0.04 rg 56 36 Td (".$esc(DemoData::SHORT).") Tj ET\n";
        $objs = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
            '<< /Length '.strlen($stream)." >>\nstream\n".$stream."endstream",
        ];
        $out = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objs as $n => $o) {
            $offsets[] = strlen($out);
            $out .= ($n + 1)." 0 obj\n".$o."\nendobj\n";
        }
        $xref = strlen($out);
        $out .= "xref\n0 ".(count($objs) + 1)."\n0000000000 65535 f \n";
        foreach ($offsets as $o) {
            $out .= sprintf("%010d 00000 n \n", $o);
        }

        return $out.'trailer << /Size '.(count($objs) + 1)." /Root 1 0 R >>\nstartxref\n".$xref."\n%%EOF";
    }

    public static function xlsx(array $rows): ?string
    {
        if (! class_exists(\OpenSpout\Writer\XLSX\Writer::class) || ! class_exists(\ZipArchive::class)) {
            return null;
        }
        $tmp = tempnam(sys_get_temp_dir(), 'pmx').'.xlsx';
        $w = new \OpenSpout\Writer\XLSX\Writer;
        $w->openToFile($tmp);
        foreach ($rows as $r) {
            $w->addRow(\OpenSpout\Common\Entity\Row::fromValues($r));
        }
        $w->close();
        $bytes = file_get_contents($tmp);
        @unlink($tmp);

        return $bytes ?: null;
    }

    public static function docx(array $paragraphs): ?string
    {
        if (! class_exists(\ZipArchive::class)) {
            return null;
        }
        $tmp = tempnam(sys_get_temp_dir(), 'pmd');
        $zip = new \ZipArchive;
        $zip->open($tmp, \ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/></Types>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/></Relationships>');
        $body = '';
        foreach ($paragraphs as $i => $p) {
            $t = htmlspecialchars((string) $p, ENT_XML1);
            $body .= $i === 0 ? '<w:p><w:r><w:rPr><w:b/><w:sz w:val="32"/></w:rPr><w:t>'.$t.'</w:t></w:r></w:p>' : '<w:p><w:r><w:t xml:space="preserve">'.$t.'</w:t></w:r></w:p>';
        }
        $zip->addFromString('word/document.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:body>'.$body.'<w:sectPr><w:pgSz w:w="11906" w:h="16838"/></w:sectPr></w:body></w:document>');
        $zip->close();
        $bytes = file_get_contents($tmp);
        @unlink($tmp);

        return $bytes ?: null;
    }

    /** Ilustrasi "foto progres" sederhana (GD): langit, laut/tanah, struktur, batang progres. */
    public static function jpg(string $caption, float $pct, string $hex): ?string
    {
        if (! function_exists('imagecreatetruecolor')) {
            return null;
        }
        $w = 960;
        $h = 600;
        $im = imagecreatetruecolor($w, $h);
        for ($y = 0; $y < $h * 0.6; $y++) {
            $c = imagecolorallocate($im, 120 + (int) ($y / 6), 170 + (int) ($y / 8), 225);
            imageline($im, 0, $y, $w, $y, $c);
        }
        imagefilledrectangle($im, 0, (int) ($h * 0.6), $w, $h, imagecolorallocate($im, 32, 86, 120));
        [$r, $g, $b] = sscanf($hex, '#%02x%02x%02x');
        $steel = imagecolorallocate($im, 70, 76, 86);
        $accent = imagecolorallocate($im, $r ?? 15, $g ?? 94, $b ?? 156);
        $yellow = imagecolorallocate($im, 240, 180, 40);
        imagefilledrectangle($im, 330, 200, 630, 230, $accent);
        imagefilledrectangle($im, 360, 130, 470, 200, $yellow);
        imagefilledrectangle($im, 500, 150, 600, 200, $steel);
        foreach ([350, 450, 520, 610] as $x) {
            imagefilledrectangle($im, $x, 230, $x + 14, 470, $steel);
        }
        imageline($im, 357, 260, 617, 440, $steel);
        imageline($im, 617, 260, 357, 440, $steel);
        imagefilledrectangle($im, 0, $h - 70, $w, $h, imagecolorallocate($im, 255, 255, 255));
        imagefilledrectangle($im, 20, $h - 30, 20 + (int) (($w - 40) * min(100, $pct) / 100), $h - 18, $accent);
        imagerectangle($im, 20, $h - 30, $w - 20, $h - 18, $steel);
        imagestring($im, 5, 20, $h - 60, iconv('UTF-8', 'ASCII//TRANSLIT', $caption).'  -  progres '.round($pct).'% (ILUSTRASI DATA DEMO - PEMBELAJARAN)', $steel);
        ob_start();
        imagejpeg($im, null, 82);
        imagedestroy($im);

        return (string) ob_get_clean();
    }
}
