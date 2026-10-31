<?php

namespace App\Filament\Resources\Vendors;

use App\Filament\Concerns\AdminOnlyWrite;
use App\Filament\Concerns\ProjectScopedAccess;
use App\Filament\Support\F;
use App\Models;
use App\Support\Fmt;
use App\Support\Pmbok;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class VendorResource extends Resource
{
    use AdminOnlyWrite;

    protected static ?string $model = Models\Vendor::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengadaan';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Vendor';

    protected static ?string $pluralModelLabel = 'Daftar Vendor';

    protected static ?string $slug = 'vendor';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            TextInput::make('code')->label('Kode')->required()->maxLength(20)->unique(ignoreRecord: true),
            TextInput::make('name')->label('Nama perusahaan')->required()->columnSpan(2),
            TextInput::make('category')->label('Bidang'),
            TextInput::make('country')->label('Negara')->default('Indonesia'),
            Select::make('rating')->label('Penilaian kinerja')->options([1 => '★', 2 => '★★', 3 => '★★★', 4 => '★★★★', 5 => '★★★★★'])->default(3),
            TextInput::make('contact_name')->label('Kontak'),
            TextInput::make('email')->email(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('Kode')->searchable(),
                TextColumn::make('name')->label('Vendor')->searchable()->description(fn ($record) => $record->category),
                TextColumn::make('country')->label('Negara'),
                TextColumn::make('rating')->label('Kinerja')->formatStateUsing(fn ($state) => str_repeat('★', (int) $state).str_repeat('☆', 5 - (int) $state))->color('warning'),
                TextColumn::make('contracts_count')->label('Kontrak')->counts('contracts')->alignCenter(),
                TextColumn::make('contracts_sum_value')->label('Nilai kontrak')->sum('contracts', 'value')->formatStateUsing(fn ($state) => Fmt::money($state))->alignEnd(),
            ])
            ->filters([

            ])
            ->defaultSort('name')
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageVendors::route('/')];
    }
}
