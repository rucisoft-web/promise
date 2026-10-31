<?php

namespace App\Filament\Widgets;

use App\Services\PortfolioAnalytics as A;
use App\Support\Fmt;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PortfolioStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected ?string $pollingInterval = null;

    protected function getColumns(): int|array
    {
        return ['default' => 1, 'sm' => 2, 'lg' => 3];
    }

    protected function getStats(): array
    {
        $t = A::totals();
        $curve = A::sCurve();
        $ev = array_values(array_filter($curve['ev'], fn ($v) => $v !== null));
        $ix = fn ($v) => $v === null ? 'gray' : ($v >= 0.95 ? 'success' : ($v >= 0.85 ? 'warning' : 'danger'));

        return [
            Stat::make('Proyek aktif', $t['active'].' dari '.$t['projects'])
                ->description($t['red'].' proyek berstatus merah')->descriptionIcon('heroicon-m-briefcase')
                ->color($t['red'] > 0 ? 'danger' : 'success'),
            Stat::make('Anggaran portofolio (BAC)', Fmt::money($t['bac']))
                ->description('EAC '.Fmt::money($t['eac']))->descriptionIcon('heroicon-m-banknotes')->color('primary'),
            Stat::make('Nilai hasil (EV)', Fmt::money($t['ev']))
                ->description('AC '.Fmt::money($t['ac']).' · PV '.Fmt::money($t['pv']))
                ->chart(array_slice($ev, -12))->color('info'),
            Stat::make('SPI portofolio', Fmt::index($t['spi']))
                ->description(($t['spi'] ?? 1) >= 1 ? 'sesuai/lebih cepat dari jadwal' : 'di belakang jadwal')
                ->descriptionIcon(($t['spi'] ?? 1) >= 1 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')->color($ix($t['spi'])),
            Stat::make('CPI portofolio', Fmt::index($t['cpi']))
                ->description(($t['cpi'] ?? 1) >= 1 ? 'di bawah anggaran' : 'melampaui anggaran')
                ->descriptionIcon(($t['cpi'] ?? 1) >= 1 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')->color($ix($t['cpi'])),
            Stat::make('Risiko tinggi terbuka', $t['high_risks'])
                ->description($t['open_risks'].' risiko · '.$t['open_issues'].' isu · '.$t['pending_cr'].' CR menunggu')
                ->descriptionIcon('heroicon-m-shield-exclamation')->color($t['high_risks'] > 0 ? 'danger' : 'success'),
        ];
    }
}
