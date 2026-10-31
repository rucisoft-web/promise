<?php

namespace App\Filament\Resources\Risks;

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

class RiskResource extends Resource
{
    use ProjectScopedAccess;

    protected static ?string $model = Models\Risk::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static string|\UnitEnum|null $navigationGroup = 'Risiko';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Risiko';

    protected static ?string $pluralModelLabel = 'Register Risiko';

    protected static ?string $slug = 'risiko';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Identifikasi')->columns(3)->schema([
                F::projectSelect(),
                TextInput::make('code')->label('Kode')->required()->maxLength(20),
                Select::make('category')->label('Kategori')->options(Pmbok::RISK_CATEGORIES)->default('teknis')->native(false),
                TextInput::make('title')->label('Risiko')->required()->columnSpanFull(),
                Textarea::make('cause')->label('Penyebab')->rows(2),
                Textarea::make('effect')->label('Dampak bila terjadi')->rows(2),
                DatePicker::make('identified_at')->label('Tanggal identifikasi')->default(now()),
            ]),
            Section::make('Analisis kualitatif & respons')->columns(4)->schema([
                Select::make('probability')->label('Probabilitas (1–5)')->options([1 => '1 Sangat jarang', 2 => '2 Jarang', 3 => '3 Mungkin', 4 => '4 Sering', 5 => '5 Hampir pasti'])->default(3)->required()->live(),
                Select::make('impact')->label('Dampak (1–5)')->options([1 => '1 Sangat kecil', 2 => '2 Kecil', 3 => '3 Sedang', 4 => '4 Besar', 5 => '5 Sangat besar'])->default(3)->required()->live(),
                \Filament\Forms\Components\Placeholder::make('skor')->label('Skor')->content(fn (Get $get) => ((int) $get('probability') * (int) $get('impact')).' · '.ucfirst(Pmbok::riskLevel((int) $get('probability') * (int) $get('impact')))),
                F::money('cost_exposure', 'Eksposur biaya'),
                Select::make('response_strategy')->label('Strategi respons')->options(Pmbok::RISK_RESPONSES)->default('mitigasi')->native(false),
                TextInput::make('owner')->label('Pemilik risiko'),
                Select::make('status')->options(Pmbok::RISK_STATUSES)->default('terbuka')->native(false),
                DatePicker::make('due_date')->label('Tenggat respons'),
                Textarea::make('response_plan')->label('Rencana respons')->rows(2)->columnSpanFull(),
            ]),
            F::attachments(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('project')->orderByRaw('probability * impact desc'))
            ->columns([
                F::projectColumn(),
                TextColumn::make('code')->label('Kode')->searchable(),
                TextColumn::make('title')->label('Risiko')->wrap()->searchable()->description(fn ($record) => Pmbok::RISK_CATEGORIES[$record->category] ?? $record->category),
                TextColumn::make('probability')->label('P')->alignCenter(),
                TextColumn::make('impact')->label('D')->alignCenter(),
                TextColumn::make('score')->label('Skor')->badge()->alignCenter()->state(fn ($record) => $record->score)->color(fn ($state) => Pmbok::riskColor((int) $state)),
                TextColumn::make('response_strategy')->label('Respons')->formatStateUsing(fn ($state) => Pmbok::RISK_RESPONSES[$state] ?? $state)->toggleable(),
                TextColumn::make('owner')->label('Pemilik')->toggleable(),
                F::moneyColumn('cost_exposure', 'Eksposur')->toggleable(),
                F::badge('status', 'Status', Pmbok::RISK_STATUSES, ['terbuka' => 'danger', 'dipantau' => 'warning', 'terjadi' => 'danger', 'ditutup' => 'success']),
                F::attachmentsColumn(),
            ])
            ->filters([
                F::projectFilter(),
                SelectFilter::make('category')->label('Kategori')->options(Pmbok::RISK_CATEGORIES),
                SelectFilter::make('status')->options(Pmbok::RISK_STATUSES),
                SelectFilter::make('response_strategy')->label('Respons')->options(Pmbok::RISK_RESPONSES),
            ])
            ->defaultSort('probability')
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageRisks::route('/')];
    }
}
