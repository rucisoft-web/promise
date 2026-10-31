<?php

namespace App\Filament\Resources\Portfolios;

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

class PortfolioResource extends Resource
{
    use AdminOnlyWrite;

    protected static ?string $model = Models\Portfolio::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string|\UnitEnum|null $navigationGroup = 'Integrasi';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Portofolio';

    protected static ?string $pluralModelLabel = 'Portofolio & Program';

    protected static ?string $slug = 'portofolio';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            TextInput::make('code')->label('Kode')->required()->maxLength(20)->unique(ignoreRecord: true),
            TextInput::make('name')->label('Nama')->required()->columnSpan(2),
            Select::make('type')->label('Jenis')->options(['portofolio' => 'Portofolio', 'program' => 'Program'])->default('portofolio')->native(false),
            TextInput::make('owner_name')->label('Pemilik'),
            \Filament\Forms\Components\ColorPicker::make('color')->label('Warna grafik'),
            Textarea::make('strategic_objective')->label('Sasaran strategis')->rows(2)->columnSpanFull(),
            Textarea::make('description')->label('Uraian')->rows(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\ColorColumn::make('color')->label(''),
                TextColumn::make('code')->label('Kode')->searchable(),
                TextColumn::make('name')->label('Portofolio / program')->searchable()->wrap()->description(fn ($record) => $record->strategic_objective),
                TextColumn::make('owner_name')->label('Pemilik'),
                TextColumn::make('projects_count')->label('Proyek')->counts('projects')->alignCenter(),
                TextColumn::make('projects_sum_budget')->label('Anggaran')->sum('projects', 'budget')->formatStateUsing(fn ($state) => Fmt::money($state))->alignEnd(),
            ])
            ->filters([

            ])
            ->defaultSort('code')
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManagePortfolios::route('/')];
    }
}
