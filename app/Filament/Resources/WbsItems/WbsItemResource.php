<?php

namespace App\Filament\Resources\WbsItems;

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

class WbsItemResource extends Resource
{
    use ProjectScopedAccess;

    protected static ?string $model = Models\WbsItem::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Ruang Lingkup';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Elemen WBS';

    protected static ?string $pluralModelLabel = 'Work Breakdown Structure';

    protected static ?string $slug = 'wbs';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Elemen WBS')->columns(3)->schema([
                F::projectSelect(),
                Select::make('parent_id')->label('Induk (level 1)')
                    ->options(fn (Get $get) => Models\WbsItem::where('project_id', $get('project_id') ?: 0)->where('level', '<', 3)->orderBy('sort')->get()->mapWithKeys(fn ($w) => [$w->id => $w->label]))
                    ->searchable()->helperText('Kosongkan untuk elemen level 1 (deliverable utama).'),
                TextInput::make('code')->label('Kode WBS')->required()->maxLength(20)->placeholder('1.2'),
                TextInput::make('name')->label('Nama elemen / paket kerja')->required()->columnSpan(2),
                TextInput::make('deliverable')->label('Deliverable'),
                Textarea::make('description')->label('Kamus WBS (uraian pekerjaan)')->rows(2)->columnSpanFull(),
            ]),
            Section::make('Jadwal, biaya & progres')->columns(4)->description('Isian untuk paket kerja. Elemen yang punya anak dihitung otomatis dari anaknya.')->schema([
                DatePicker::make('start_date')->label('Mulai')->required(),
                DatePicker::make('finish_date')->label('Selesai')->required()->afterOrEqual('start_date'),
                DatePicker::make('baseline_start')->label('Baseline mulai'),
                DatePicker::make('baseline_finish')->label('Baseline selesai'),
                F::money('planned_cost', 'Anggaran (PV/BAC)'),
                F::money('actual_cost', 'Biaya aktual (AC)'),
                TextInput::make('percent_complete')->label('% selesai')->numeric()->minValue(0)->maxValue(100)->suffix('%')->default(0),
                Select::make('resource_id')->label('Penanggung jawab')->options(fn () => Models\Resource::where('is_active', true)->orderBy('name')->pluck('name', 'id'))->searchable(),
                Toggle::make('is_milestone')->label('Milestone'),
                TextInput::make('sort')->label('Urutan')->numeric()->default(0),
            ]),
            F::attachments(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['project', 'resource'])->orderBy('project_id')->orderBy('sort'))
            ->columns([
                F::projectColumn(),
                TextColumn::make('code')->label('Kode')->sortable()->weight(fn ($record) => $record->level === 1 ? 'bold' : null),
                TextColumn::make('name')->label('Elemen WBS')->searchable()->wrap()
                    ->formatStateUsing(fn ($state, $record) => str_repeat('    ', $record->level - 1).($record->is_milestone ? '◆ ' : '').$state)
                    ->weight(fn ($record) => $record->level === 1 ? 'bold' : null)
                    ->description(fn ($record) => $record->deliverable),
                TextColumn::make('level')->label('Lv')->alignCenter()->toggleable(),
                TextColumn::make('resource.name')->label('PJ')->toggleable(),
                TextColumn::make('start_date')->label('Mulai')->date('d/m/y')->sortable(),
                TextColumn::make('finish_date')->label('Selesai')->date('d/m/y')->sortable()
                    ->description(fn ($record) => $record->slip_days > 0 ? '+'.$record->slip_days.' hari' : null),
                F::moneyColumn('planned_cost', 'PV'),
                F::moneyColumn('actual_cost', 'AC')->toggleable(),
                TextColumn::make('percent_complete')->label('% selesai')->alignEnd()->sortable()->formatStateUsing(fn ($state) => Fmt::num($state).'%')
                    ->color(fn ($state) => $state >= 100 ? 'success' : ($state > 0 ? 'primary' : 'gray')),
                F::attachmentsColumn(),
            ])
            ->filters([
                F::projectFilter(),
                SelectFilter::make('level')->label('Level')->options([1 => 'Level 1 (deliverable)', 2 => 'Level 2 (paket kerja)']),
                TernaryFilter::make('is_milestone')->label('Milestone'),
            ])
            ->defaultSort('code')
            ->defaultGroup(\Filament\Tables\Grouping\Group::make('project.name')->label('Proyek')->collapsible())
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageWbsItems::route('/')];
    }
}
