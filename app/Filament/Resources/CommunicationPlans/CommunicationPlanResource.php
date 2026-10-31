<?php

namespace App\Filament\Resources\CommunicationPlans;

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

class CommunicationPlanResource extends Resource
{
    use ProjectScopedAccess;

    protected static ?string $model = Models\CommunicationPlan::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    protected static string|\UnitEnum|null $navigationGroup = 'Komunikasi';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Rencana komunikasi';

    protected static ?string $pluralModelLabel = 'Rencana Komunikasi';

    protected static ?string $slug = 'rencana-komunikasi';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            F::projectSelect(),
            TextInput::make('title')->label('Komunikasi')->required()->columnSpan(2),
            Select::make('comm_type')->label('Jenis')->options(Pmbok::COMM_TYPES)->default('rapat')->native(false),
            Select::make('frequency')->label('Frekuensi')->options(Pmbok::FREQUENCIES)->default('bulanan')->native(false),
            TextInput::make('channel')->label('Media/saluran'),
            TextInput::make('audience')->label('Audiens')->columnSpan(2),
            TextInput::make('owner')->label('Penanggung jawab'),
            DatePicker::make('next_date')->label('Jadwal berikutnya'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                F::projectColumn(),
                TextColumn::make('title')->label('Komunikasi')->searchable()->description(fn ($record) => $record->audience),
                TextColumn::make('comm_type')->label('Jenis')->formatStateUsing(fn ($state) => Pmbok::COMM_TYPES[$state] ?? $state),
                TextColumn::make('frequency')->label('Frekuensi')->badge()->formatStateUsing(fn ($state) => Pmbok::FREQUENCIES[$state] ?? $state),
                TextColumn::make('channel')->label('Saluran')->toggleable(),
                TextColumn::make('owner')->label('PJ'),
                TextColumn::make('next_date')->label('Berikutnya')->date('d M Y')->sortable(),
            ])
            ->filters([
                F::projectFilter(),
            ])
            ->defaultSort('next_date')
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageCommunicationPlans::route('/')];
    }
}
