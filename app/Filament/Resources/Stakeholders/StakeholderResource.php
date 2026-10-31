<?php

namespace App\Filament\Resources\Stakeholders;

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

class StakeholderResource extends Resource
{
    use ProjectScopedAccess;

    protected static ?string $model = Models\Stakeholder::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Komunikasi';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Pemangku kepentingan';

    protected static ?string $pluralModelLabel = 'Register Pemangku Kepentingan';

    protected static ?string $slug = 'pemangku-kepentingan';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            F::projectSelect(),
            TextInput::make('name')->label('Nama/pihak')->required()->columnSpan(2),
            TextInput::make('position')->label('Jabatan'),
            TextInput::make('organization')->label('Organisasi'),
            Select::make('category')->label('Kategori')->options(Pmbok::STAKEHOLDER_CATEGORIES)->default('internal')->native(false),
            Select::make('power')->label('Pengaruh (1–5)')->options(array_combine(range(1, 5), range(1, 5)))->default(3),
            Select::make('interest')->label('Kepentingan (1–5)')->options(array_combine(range(1, 5), range(1, 5)))->default(3),
            Select::make('attitude')->label('Sikap')->options(Pmbok::ATTITUDES)->default('netral')->native(false),
            Textarea::make('engagement_strategy')->label('Strategi keterlibatan')->rows(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                F::projectColumn(),
                TextColumn::make('name')->label('Pemangku kepentingan')->searchable()->description(fn ($record) => trim($record->position.' · '.$record->organization, ' ·')),
                TextColumn::make('category')->label('Kategori')->formatStateUsing(fn ($state) => Pmbok::STAKEHOLDER_CATEGORIES[$state] ?? $state),
                TextColumn::make('power')->label('Pengaruh')->alignCenter(),
                TextColumn::make('interest')->label('Kepentingan')->alignCenter(),
                TextColumn::make('kuadran')->label('Kuadran')->badge()
                    ->state(fn ($record) => $record->power >= 3 ? ($record->interest >= 3 ? 'Kelola erat' : 'Jaga kepuasan') : ($record->interest >= 3 ? 'Beri informasi' : 'Pantau'))
                    ->color(fn ($state) => ['Kelola erat' => 'danger', 'Jaga kepuasan' => 'warning', 'Beri informasi' => 'info', 'Pantau' => 'gray'][$state]),
                F::badge('attitude', 'Sikap', Pmbok::ATTITUDES, ['pendukung' => 'success', 'netral' => 'gray', 'penentang' => 'danger']),
            ])
            ->filters([
                F::projectFilter(),
                SelectFilter::make('category')->label('Kategori')->options(Pmbok::STAKEHOLDER_CATEGORIES),
            ])
            ->defaultSort('power')
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageStakeholders::route('/')];
    }
}
