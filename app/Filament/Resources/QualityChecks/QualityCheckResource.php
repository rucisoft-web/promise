<?php

namespace App\Filament\Resources\QualityChecks;

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

class QualityCheckResource extends Resource
{
    use ProjectScopedAccess;

    protected static ?string $model = Models\QualityCheck::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-check-badge';

    protected static string|\UnitEnum|null $navigationGroup = 'Mutu';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Pemeriksaan mutu';

    protected static ?string $pluralModelLabel = 'Pengendalian Mutu';

    protected static ?string $slug = 'mutu';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            F::projectSelect(),
            TextInput::make('code')->label('Kode')->required()->maxLength(20),
            Select::make('check_type')->label('Jenis')->options(Pmbok::QUALITY_TYPES)->default('inspeksi')->native(false),
            TextInput::make('title')->label('Pemeriksaan')->required()->columnSpan(2),
            F::wbsSelect(),
            TextInput::make('standard')->label('Standar acuan')->placeholder('API, ASME, SNI, ISO 9001'),
            TextInput::make('acceptance_criteria')->label('Kriteria penerimaan')->columnSpan(2),
            DatePicker::make('planned_date')->label('Rencana'),
            DatePicker::make('actual_date')->label('Pelaksanaan'),
            Select::make('result')->label('Hasil')->options(Pmbok::QUALITY_RESULTS)->default('terjadwal')->native(false),
            TextInput::make('defects')->label('Jumlah temuan/cacat')->numeric()->default(0),
            TextInput::make('inspector')->label('Pemeriksa'),
            Textarea::make('notes')->label('Catatan')->rows(2)->columnSpanFull(),
            F::attachments(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                F::projectColumn(),
                TextColumn::make('code')->label('Kode')->searchable(),
                TextColumn::make('title')->label('Pemeriksaan')->wrap()->searchable()->description(fn ($record) => $record->standard),
                TextColumn::make('check_type')->label('Jenis')->formatStateUsing(fn ($state) => Pmbok::QUALITY_TYPES[$state] ?? $state),
                TextColumn::make('planned_date')->label('Rencana')->date('d M Y')->sortable(),
                TextColumn::make('defects')->label('Cacat')->alignEnd(),
                F::badge('result', 'Hasil', Pmbok::QUALITY_RESULTS, ['terjadwal' => 'gray', 'lulus' => 'success', 'bersyarat' => 'warning', 'gagal' => 'danger']),
                F::attachmentsColumn(),
            ])
            ->filters([
                F::projectFilter(),
                SelectFilter::make('result')->label('Hasil')->options(Pmbok::QUALITY_RESULTS),
                SelectFilter::make('check_type')->label('Jenis')->options(Pmbok::QUALITY_TYPES),
            ])
            ->defaultSort('planned_date')
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageQualityChecks::route('/')];
    }
}
