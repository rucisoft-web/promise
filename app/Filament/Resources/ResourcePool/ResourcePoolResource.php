<?php

namespace App\Filament\Resources\ResourcePool;

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

class ResourcePoolResource extends Resource
{
    use AdminOnlyWrite;

    protected static ?string $model = Models\Resource::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Sumber Daya Manusia';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Sumber daya';

    protected static ?string $pluralModelLabel = 'Kelompok Sumber Daya';

    protected static ?string $slug = 'sumber-daya';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            TextInput::make('code')->label('Kode')->required()->maxLength(20)->unique(ignoreRecord: true),
            TextInput::make('name')->label('Nama')->required()->columnSpan(2),
            Select::make('type')->label('Jenis')->options(Pmbok::RESOURCE_TYPES)->default('tenaga_kerja')->native(false),
            TextInput::make('discipline')->label('Disiplin/keahlian'),
            TextInput::make('department')->label('Fungsi/departemen'),
            TextInput::make('email')->email(),
            Select::make('user_id')->label('Akun pengguna')->options(fn () => Models\User::orderBy('name')->pluck('name', 'id'))->searchable()->helperText('Untuk mengisi lembar waktu sendiri.'),
            TextInput::make('cost_rate')->label('Tarif per jam')->numeric()->prefix(config('promise.currency')),
            TextInput::make('capacity_hours')->label('Kapasitas per minggu')->numeric()->default(40)->suffix('jam'),
            Toggle::make('is_active')->label('Aktif')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('Kode')->searchable(),
                TextColumn::make('name')->label('Nama')->searchable()->description(fn ($record) => $record->discipline),
                TextColumn::make('department')->label('Fungsi')->toggleable(),
                TextColumn::make('type')->label('Jenis')->formatStateUsing(fn ($state) => Pmbok::RESOURCE_TYPES[$state] ?? $state),
                TextColumn::make('cost_rate')->label('Tarif/jam')->formatStateUsing(fn ($state) => Fmt::money($state, false))->alignEnd(),
                TextColumn::make('capacity_hours')->label('Kapasitas')->suffix(' jam/mg')->alignEnd(),
                TextColumn::make('assignments_count')->label('Penugasan')->counts('assignments')->alignCenter(),
                \Filament\Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->filters([
                SelectFilter::make('type')->label('Jenis')->options(Pmbok::RESOURCE_TYPES),
                SelectFilter::make('department')->label('Fungsi')->options(fn () => Models\Resource::whereNotNull('department')->distinct()->orderBy('department')->pluck('department', 'department')),
            ])
            ->defaultSort('name')
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageResourcePool::route('/')];
    }
}
