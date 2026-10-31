<?php

namespace App\Filament\Resources\TaskDependencies;

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

class TaskDependencyResource extends Resource
{
    use ProjectScopedAccess;

    protected static ?string $model = Models\TaskDependency::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static string|\UnitEnum|null $navigationGroup = 'Waktu';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Keterkaitan tugas';

    protected static ?string $pluralModelLabel = 'Keterkaitan Tugas (FS/SS/FF/SF)';

    protected static ?string $slug = 'keterkaitan-tugas';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            F::projectSelect()->columnSpanFull(),
            F::wbsSelect('predecessor_id', 'Pendahulu (predecessor)')->required(),
            F::wbsSelect('successor_id', 'Penerus (successor)')->required()->different('predecessor_id'),
            Select::make('type')->label('Jenis keterkaitan')->options(Pmbok::DEPENDENCY_TYPES)->default('FS')->required()->native(false),
            TextInput::make('lag_days')->label('Lag (+) / lead (−)')->numeric()->default(0)->suffix('hari'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['project', 'predecessor', 'successor']))
            ->columns([
                F::projectColumn(),
                TextColumn::make('predecessor.code')->label('Pendahulu')->description(fn ($record) => $record->predecessor?->name)->searchable(),
                TextColumn::make('type')->label('Jenis')->badge()->color(fn ($state) => ['FS' => 'primary', 'SS' => 'info', 'FF' => 'warning', 'SF' => 'danger'][$state] ?? 'gray')
                    ->tooltip(fn ($state) => Pmbok::DEPENDENCY_TYPES[$state] ?? $state),
                TextColumn::make('lag_days')->label('Lag')->formatStateUsing(fn ($state) => ($state > 0 ? '+' : '').$state.' hari')->alignEnd(),
                TextColumn::make('successor.code')->label('Penerus')->description(fn ($record) => $record->successor?->name)->searchable(),
                TextColumn::make('cek')->label('Jadwal')->badge()
                    ->state(fn ($record) => $record->isSatisfied() ? 'Terpenuhi' : 'Dilanggar')
                    ->color(fn ($state) => $state === 'Terpenuhi' ? 'success' : 'danger'),
            ])
            ->filters([
                F::projectFilter(),
                SelectFilter::make('type')->label('Jenis')->options(Pmbok::DEPENDENCY_TYPES),
            ])
            ->defaultSort('id')
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageTaskDependencies::route('/')];
    }
}
