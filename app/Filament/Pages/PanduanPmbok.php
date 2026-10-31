<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PanduanPmbok extends Page
{
    protected static ?string $title = 'Panduan Promise & PMBOK';

    protected static ?string $navigationLabel = 'Panduan PMBOK';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static string|\UnitEnum|null $navigationGroup = 'Umum';

    protected static ?int $navigationSort = 9;

    protected static ?string $slug = 'panduan';

    protected string $view = 'filament.pages.panduan-pmbok';
}
