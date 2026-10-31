<?php

namespace App\Filament\Resources\Vendors\Pages;

use App\Filament\Resources\Vendors\VendorResource;
use Filament\Resources\Pages\ManageRecords;

class ManageVendors extends ManageRecords
{
    protected static string $resource = VendorResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
