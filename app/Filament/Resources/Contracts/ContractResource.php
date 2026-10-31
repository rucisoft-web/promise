<?php

namespace App\Filament\Resources\Contracts;

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

class ContractResource extends Resource
{
    use ProjectScopedAccess;

    protected static ?string $model = Models\Contract::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengadaan';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Kontrak';

    protected static ?string $pluralModelLabel = 'Kontrak & Paket Pengadaan';

    protected static ?string $slug = 'kontrak';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            F::projectSelect(),
            TextInput::make('number')->label('Nomor kontrak/paket')->required()->maxLength(40),
            Select::make('vendor_id')->label('Vendor')->options(fn () => Models\Vendor::orderBy('name')->pluck('name', 'id'))->searchable(),
            TextInput::make('title')->label('Paket pekerjaan')->required()->columnSpanFull(),
            Select::make('contract_type')->label('Jenis kontrak')->options(Pmbok::CONTRACT_TYPES)->default('lump_sum')->native(false),
            Select::make('procurement_method')->label('Metode pengadaan')->options(Pmbok::PROCUREMENT_METHODS)->default('tender_terbuka')->native(false),
            Select::make('status')->options(Pmbok::CONTRACT_STATUSES)->default('perencanaan')->native(false),
            F::money('value', 'Nilai kontrak'),
            F::money('paid_amount', 'Sudah dibayar'),
            TextInput::make('progress')->label('Progres pekerjaan')->numeric()->minValue(0)->maxValue(100)->suffix('%')->default(0),
            DatePicker::make('start_date')->label('Mulai'),
            DatePicker::make('end_date')->label('Berakhir')->afterOrEqual('start_date'),
            Textarea::make('notes')->label('Catatan')->rows(2)->columnSpanFull(),
            F::attachments(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['project', 'vendor']))
            ->columns([
                F::projectColumn(),
                TextColumn::make('number')->label('Nomor')->searchable(),
                TextColumn::make('title')->label('Paket')->wrap()->searchable()->description(fn ($record) => $record->vendor?->name),
                TextColumn::make('contract_type')->label('Jenis')->formatStateUsing(fn ($state) => Pmbok::CONTRACT_TYPES[$state] ?? $state)->toggleable(),
                TextColumn::make('procurement_method')->label('Metode')->formatStateUsing(fn ($state) => Pmbok::PROCUREMENT_METHODS[$state] ?? $state)->toggleable(isToggledHiddenByDefault: true),
                F::moneyColumn('value', 'Nilai'),
                F::moneyColumn('paid_amount', 'Dibayar'),
                TextColumn::make('progress')->label('Progres')->suffix('%')->alignEnd(),
                TextColumn::make('end_date')->label('Berakhir')->date('d M Y')->sortable(),
                F::badge('status', 'Status', Pmbok::CONTRACT_STATUSES, ['perencanaan' => 'gray', 'tender' => 'info', 'evaluasi' => 'warning', 'berjalan' => 'primary', 'selesai' => 'success', 'terminasi' => 'danger']),
                F::attachmentsColumn(),
            ])
            ->filters([
                F::projectFilter(),
                SelectFilter::make('status')->options(Pmbok::CONTRACT_STATUSES),
                SelectFilter::make('vendor_id')->label('Vendor')->relationship('vendor', 'name')->searchable()->preload(),
            ])
            ->defaultSort('start_date')
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageContracts::route('/')];
    }
}
