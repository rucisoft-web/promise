<?php

namespace App\Filament\Resources\Assignments;

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

class AssignmentResource extends Resource
{
    use ProjectScopedAccess;

    protected static ?string $model = Models\Assignment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-plus';

    protected static string|\UnitEnum|null $navigationGroup = 'Sumber Daya Manusia';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Penugasan';

    protected static ?string $pluralModelLabel = 'Penugasan Tim Proyek';

    protected static ?string $slug = 'penugasan';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            F::projectSelect(),
            Select::make('resource_id')->label('Sumber daya')->options(fn () => Models\Resource::where('is_active', true)->orderBy('name')->pluck('name', 'id'))->searchable()->required(),
            TextInput::make('project_role')->label('Peran di proyek'),
            F::wbsSelect('wbs_item_id', 'Paket kerja', true),
            TextInput::make('allocation')->label('Alokasi')->numeric()->minValue(1)->maxValue(100)->default(100)->suffix('%'),
            DatePicker::make('start_date')->label('Mulai'),
            DatePicker::make('finish_date')->label('Selesai')->afterOrEqual('start_date'),
            TextInput::make('planned_hours')->label('Jam rencana')->numeric()->default(0),
            TextInput::make('actual_hours')->label('Jam aktual')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['project', 'resource', 'wbsItem']))
            ->columns([
                F::projectColumn(),
                TextColumn::make('resource.name')->label('Sumber daya')->searchable()->description(fn ($record) => $record->project_role),
                TextColumn::make('wbsItem.code')->label('WBS')->tooltip(fn ($record) => $record->wbsItem?->name),
                TextColumn::make('allocation')->label('Alokasi')->suffix('%')->alignEnd(),
                TextColumn::make('start_date')->label('Mulai')->date('d/m/y'),
                TextColumn::make('finish_date')->label('Selesai')->date('d/m/y'),
                TextColumn::make('planned_hours')->label('Jam rencana')->numeric(0)->alignEnd(),
                TextColumn::make('actual_hours')->label('Jam aktual')->numeric(0)->alignEnd(),
            ])
            ->filters([
                F::projectFilter(),
                SelectFilter::make('resource_id')->label('Sumber daya')->relationship('resource', 'name')->searchable()->preload(),
            ])
            ->defaultSort('start_date')
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageAssignments::route('/')];
    }
}
