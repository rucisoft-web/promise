<?php

namespace App\Filament\Resources\Timesheets;

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

class TimesheetResource extends Resource
{
    use ProjectScopedAccess;

    protected static ?string $model = Models\Timesheet::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static string|\UnitEnum|null $navigationGroup = 'Sumber Daya Manusia';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Lembar waktu';

    protected static ?string $pluralModelLabel = 'Lembar Waktu (Timesheet)';

    protected static ?string $slug = 'lembar-waktu';

    public static function canCreate(): bool
    {
        $u = static::user();

        return (bool) ($u?->canEditProjects() || $u?->resource);
    }

    public static function canEdit(Model $record): bool
    {
        $u = static::user();
        if (! $u || $u->isGuestVisitor()) {
            return false;
        }
        if ($u->canEditProject($record->project)) {
            return true;
        }

        return $record->status === 'draf' && (int) $u->resource?->id === (int) $record->resource_id;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            Select::make('project_id')->label('Proyek')
                ->options(fn () => Models\Project::active()->orderBy('code')->get()->mapWithKeys(fn ($p) => [$p->id => $p->label]))
                ->searchable()->required()->live(),
            Select::make('resource_id')->label('Sumber daya')
                ->options(fn () => auth()->user()->canEditProjects() ? Models\Resource::orderBy('name')->pluck('name', 'id') : Models\Resource::where('user_id', auth()->id())->pluck('name', 'id'))
                ->default(fn () => auth()->user()->resource?->id)->required()->searchable(),
            F::wbsSelect('wbs_item_id', 'Paket kerja', true),
            DatePicker::make('week_start')->label('Minggu mulai (Senin)')->default(now()->startOfWeek())->required(),
            TextInput::make('hours')->label('Jam kerja')->numeric()->minValue(0)->maxValue(84)->required(),
            Select::make('status')->options(fn () => auth()->user()->canEditProjects() ? Pmbok::TIMESHEET_STATUSES : ['draf' => 'Draf', 'diajukan' => 'Diajukan'])->default('draf')->native(false),
            TextInput::make('description')->label('Uraian pekerjaan')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['project', 'resource', 'wbsItem']))
            ->columns([
                TextColumn::make('week_start')->label('Minggu')->date('d M Y')->sortable(),
                TextColumn::make('resource.name')->label('Sumber daya')->searchable(),
                F::projectColumn(),
                TextColumn::make('wbsItem.code')->label('WBS')->tooltip(fn ($record) => $record->wbsItem?->name),
                TextColumn::make('hours')->label('Jam')->numeric(1)->alignEnd()->summarize(\Filament\Tables\Columns\Summarizers\Sum::make()->label('Total')),
                TextColumn::make('description')->label('Uraian')->wrap()->toggleable(),
                F::badge('status', 'Status', Pmbok::TIMESHEET_STATUSES, ['draf' => 'gray', 'diajukan' => 'info', 'disetujui' => 'success', 'ditolak' => 'danger']),
            ])
            ->filters([
                F::projectFilter(),
                SelectFilter::make('resource_id')->label('Sumber daya')->relationship('resource', 'name')->searchable()->preload(),
                SelectFilter::make('status')->options(Pmbok::TIMESHEET_STATUSES),
            ])
            ->defaultSort('week_start')
            ->recordActions([
                Action::make('setujui')->label('Setujui')->icon('heroicon-o-check')->color('success')
                    ->visible(fn ($record) => $record->status === 'diajukan' && auth()->user()->canEditProject($record->project))
                    ->action(fn ($record) => $record->update(['status' => 'disetujui', 'approved_by' => auth()->id(), 'approved_at' => now()])),
                EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageTimesheets::route('/')];
    }
}
