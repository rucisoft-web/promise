<?php

namespace App\Filament\Resources\Projects;

use App\Filament\Concerns\ProjectScopedAccess;
use App\Filament\Support\F;
use App\Models\Portfolio;
use App\Models\Project;
use App\Models\User;
use App\Support\Fmt;
use App\Support\Pmbok;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ProjectResource extends Resource
{
    use ProjectScopedAccess;

    protected static ?string $model = Project::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static string|\UnitEnum|null $navigationGroup = 'Integrasi';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Proyek';

    protected static ?string $pluralModelLabel = 'Proyek & Piagam';

    protected static ?string $slug = 'proyek';

    protected static ?string $recordTitleAttribute = 'name';

    public static function canCreate(): bool
    {
        return (bool) static::user()?->managesAll();
    }

    public static function canDelete(Model $record): bool
    {
        return (bool) static::user()?->managesAll();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Tabs::make('proyek')->persistTabInQueryString()->tabs([
                Tab::make('Identitas')->icon('heroicon-o-identification')->columns(3)->schema([
                    TextInput::make('code')->label('Kode proyek')->required()->maxLength(20)->unique(ignoreRecord: true),
                    TextInput::make('name')->label('Nama proyek')->required()->columnSpan(2),
                    Select::make('portfolio_id')->label('Portofolio / program')->options(fn () => Portfolio::orderBy('code')->pluck('name', 'id'))->searchable(),
                    Select::make('business_unit')->label('Unit bisnis')->options(Pmbok::BUSINESS_UNITS)->native(false),
                    TextInput::make('project_type')->label('Jenis proyek')->placeholder('mis. Pengembangan lapangan offshore'),
                    TextInput::make('location')->label('Lokasi'),
                    Select::make('manager_id')->label('Manajer proyek')->options(fn () => User::whereIn('role', ['admin', 'pmo', 'manajer'])->orderBy('name')->pluck('name', 'id'))->searchable(),
                    TextInput::make('sponsor')->label('Sponsor proyek'),
                    Select::make('phase')->label('Fase (kelompok proses)')->options(Pmbok::PHASES)->default('inisiasi')->required()->native(false),
                    Select::make('status')->label('Status')->options(Pmbok::PROJECT_STATUSES)->default('aktif')->required()->native(false),
                    Select::make('priority')->label('Prioritas')->options(Pmbok::PRIORITIES)->default('sedang')->native(false),
                    Select::make('strategic_score')->label('Keselarasan strategis (1–5)')->options(array_combine(range(1, 5), range(1, 5)))->default(3),
                    Textarea::make('description')->label('Deskripsi singkat')->rows(3)->columnSpanFull(),
                ]),
                Tab::make('Piagam Proyek')->icon('heroicon-o-document-check')->columns(2)->schema([
                    Textarea::make('justification')->label('Justifikasi bisnis')->rows(4),
                    Textarea::make('objectives')->label('Tujuan terukur')->rows(4),
                    Textarea::make('success_criteria')->label('Kriteria keberhasilan')->rows(4),
                    Textarea::make('scope_statement')->label('Pernyataan ruang lingkup')->rows(4),
                    Textarea::make('out_of_scope')->label('Di luar lingkup')->rows(3),
                    Textarea::make('assumptions')->label('Asumsi')->rows(3),
                    Textarea::make('constraints')->label('Kendala')->rows(3)->columnSpanFull(),
                ]),
                Tab::make('Jadwal & Anggaran')->icon('heroicon-o-calendar-days')->columns(3)->schema([
                    DatePicker::make('start_date')->label('Mulai'),
                    DatePicker::make('finish_date')->label('Selesai')->afterOrEqual('start_date'),
                    DatePicker::make('status_date')->label('Tanggal status (data date)')->helperText('Dasar perhitungan EVM. Kosong = hari ini.'),
                    DatePicker::make('baseline_start')->label('Baseline mulai'),
                    DatePicker::make('baseline_finish')->label('Baseline selesai'),
                    F::money('budget', 'Anggaran (BAC)')->helperText('Nilai awal; BAC dihitung ulang dari paket kerja WBS.'),
                    F::money('contingency', 'Cadangan kontinjensi'),
                ]),
                Tab::make('Lampiran')->icon('heroicon-o-paper-clip')->schema([F::attachments()]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->with(["wbsItems", "manager", "portfolio"]))
            ->columns([
                TextColumn::make('code')->label('Kode')->sortable()->searchable()->weight('bold'),
                TextColumn::make('name')->label('Proyek')->searchable()->wrap()
                    ->description(fn (Project $r) => trim((Pmbok::BUSINESS_UNITS[$r->business_unit] ?? '').' · '.($r->location ?? ''), ' ·')),
                TextColumn::make('manager.name')->label('Manajer')->toggleable(),
                F::badge('phase', 'Fase', Pmbok::PHASES, ['inisiasi' => 'gray', 'perencanaan' => 'info', 'eksekusi' => 'primary', 'pengendalian' => 'warning', 'penutupan' => 'success']),
                TextColumn::make('kesehatan')->label('Kesehatan')->badge()
                    ->state(fn (Project $r) => $r->health())
                    ->formatStateUsing(fn ($state) => Pmbok::RAG[$state] ?? $state)
                    ->color(fn ($state) => Pmbok::RAG_COLORS[$state] ?? 'gray'),
                TextColumn::make('progres')->label('% Selesai')->alignEnd()
                    ->state(fn (Project $r) => Fmt::pct($r->evm()['actual_pct'])),
                TextColumn::make('bac')->label('BAC')->alignEnd()->state(fn (Project $r) => Fmt::money($r->evm()['bac'])),
                TextColumn::make('spi')->label('SPI')->alignEnd()->state(fn (Project $r) => Fmt::index($r->evm()['spi']))
                    ->color(fn (Project $r) => ($r->evm()['spi'] ?? 1) >= 0.95 ? 'success' : (($r->evm()['spi'] ?? 1) >= 0.85 ? 'warning' : 'danger')),
                TextColumn::make('cpi')->label('CPI')->alignEnd()->state(fn (Project $r) => Fmt::index($r->evm()['cpi']))
                    ->color(fn (Project $r) => ($r->evm()['cpi'] ?? 1) >= 0.95 ? 'success' : (($r->evm()['cpi'] ?? 1) >= 0.85 ? 'warning' : 'danger')),
                TextColumn::make('finish_date')->label('Selesai')->date('d M Y')->sortable(),
                F::badge('status', 'Status', Pmbok::PROJECT_STATUSES, ['aktif' => 'primary', 'selesai' => 'success', 'ditunda' => 'warning', 'dibatalkan' => 'danger'])->toggleable(),
                F::attachmentsColumn(),
            ])
            ->filters([
                SelectFilter::make('portfolio_id')->label('Portofolio')->options(fn () => Portfolio::orderBy('code')->pluck('name', 'id')),
                SelectFilter::make('business_unit')->label('Unit bisnis')->options(Pmbok::BUSINESS_UNITS),
                SelectFilter::make('phase')->label('Fase')->options(Pmbok::PHASES),
                SelectFilter::make('status')->options(Pmbok::PROJECT_STATUSES),
            ])
            ->defaultSort('code')
            ->recordUrl(fn (Project $r) => static::getUrl('view', ['record' => $r]))
            ->recordActions([ViewAction::make()->label('Ruang proyek'), EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view' => Pages\ViewProject::route('/{record}'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
