<?php

namespace App\Filament\Widgets;

use App\Services\PortfolioAnalytics as A;

/** Kuadran kinerja: SPI (sumbu X) vs CPI (sumbu Y), ukuran gelembung = BAC. */
class SpiCpiChart extends BaseChart
{
    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 5;

    protected ?string $heading = 'Kuadran kinerja proyek: SPI × CPI';

    protected ?string $description = 'Kanan atas = lebih cepat & lebih hemat. Ukuran gelembung = anggaran (BAC).';

    protected function getType(): string
    {
        return 'bubble';
    }

    protected function getData(): array
    {
        $ps = A::active()->filter(fn ($p) => $p->evm()['spi'] !== null && $p->evm()['cpi'] !== null);
        $max = max(1, $ps->max(fn ($p) => $p->evm()['bac']));
        $sets = [];
        foreach ($ps->values() as $i => $p) {
            $e = $p->evm();
            $color = ['hijau' => '#2E7D4F', 'kuning' => '#D98E04', 'merah' => '#D32F2F'][$p->health()];
            $sets[] = [
                'label' => $p->code,
                'data' => [['x' => round($e['spi'], 3), 'y' => round($e['cpi'], 3), 'r' => round(6 + 18 * sqrt($e['bac'] / $max), 1)]],
                'backgroundColor' => $color.'B3',
                'borderColor' => $color,
            ];
        }

        return ['datasets' => $sets];
    }

    protected function getOptions(): array
    {
        return $this->legendBottom([
            'plugins' => ['legend' => ['display' => false]],
            'aspectRatio' => 1.25,
            'scales' => [
                'x' => ['title' => ['display' => true, 'text' => 'SPI'], 'suggestedMin' => 0.7, 'suggestedMax' => 1.2],
                'y' => ['title' => ['display' => true, 'text' => 'CPI'], 'suggestedMin' => 0.7, 'suggestedMax' => 1.2],
            ],
        ]);
    }
}
