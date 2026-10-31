<?php

namespace App\Filament\Resources\StatusReports;

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

class StatusReportResource extends Resource
{
    use ProjectScopedAccess;

    protected static ?string $model = Models\StatusReport::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Komunikasi';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Laporan status';

    protected static ?string $pluralModelLabel = 'Laporan Status Proyek';

    protected static ?string $slug = 'laporan-status';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(4)->components([
            F::projectSelect()->columnSpan(2),
            DatePicker::make('period_date')->label('Periode (akhir bulan)')->required(),
            TextInput::make('prepared_by')->label('Disusun oleh'),
            Select::make('overall_status')->label('Status umum')->options(Pmbok::RAG)->default('hijau')->native(false),
            Select::make('schedule_status')->label('Jadwal')->options(Pmbok::RAG)->default('hijau')->native(false),
            Select::make('cost_status')->label('Biaya')->options(Pmbok::RAG)->default('hijau')->native(false),
            Select::make('scope_status')->label('Lingkup')->options(Pmbok::RAG)->default('hijau')->native(false),
            TextInput::make('planned_percent')->label('% rencana')->numeric()->suffix('%'),
            TextInput::make('actual_percent')->label('% aktual')->numeric()->suffix('%'),
            F::money('pv', 'PV kumulatif'),
            F::money('ev', 'EV kumulatif'),
            F::money('ac', 'AC kumulatif'),
            Textarea::make('summary')->label('Ringkasan')->rows(2)->columnSpanFull(),
            Textarea::make('achievements')->label('Capaian periode ini')->rows(2)->columnSpan(2),
            Textarea::make('next_steps')->label('Rencana periode berikutnya')->rows(2)->columnSpan(2),
            F::attachments(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                F::projectColumn(),
                TextColumn::make('period_date')->label('Periode')->date('M Y')->sortable(),
                F::badge('overall_status', 'Umum', Pmbok::RAG, Pmbok::RAG_COLORS),
                F::badge('schedule_status', 'Jadwal', Pmbok::RAG, Pmbok::RAG_COLORS),
                F::badge('cost_status', 'Biaya', Pmbok::RAG, Pmbok::RAG_COLORS),
                TextColumn::make('actual_percent')->label('% aktual')->formatStateUsing(fn ($state, $record) => Fmt::pct($state).' / '.Fmt::pct($record->planned_percent))->alignEnd(),
                TextColumn::make('spi')->label('SPI')->state(fn ($record) => $record->pv > 0 ? Fmt::index($record->ev / $record->pv) : '–')->alignEnd(),
                TextColumn::make('cpi')->label('CPI')->state(fn ($record) => $record->ac > 0 ? Fmt::index($record->ev / $record->ac) : '–')->alignEnd(),
                TextColumn::make('summary')->label('Ringkasan')->wrap()->limit(90)->toggleable(),
                F::attachmentsColumn(),
            ])
            ->filters([
                F::projectFilter(),
                SelectFilter::make('overall_status')->label('Status')->options(Pmbok::RAG),
            ])
            ->defaultSort('period_date')
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageStatusReports::route('/')];
    }
}
