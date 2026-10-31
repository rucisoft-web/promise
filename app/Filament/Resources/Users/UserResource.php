<?php

namespace App\Filament\Resources\Users;

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

class UserResource extends Resource
{
    use AdminOnlyWrite;

    protected static ?string $model = Models\User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static string|\UnitEnum|null $navigationGroup = 'Administrasi';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Pengguna';

    protected static ?string $pluralModelLabel = 'Pengguna';

    protected static ?string $slug = 'pengguna';

    public static function canViewAny(): bool
    {
        return (bool) static::user()?->isAdmin();
    }

    public static function canCreate(): bool
    {
        return (bool) static::user()?->isAdmin();
    }

    public static function canEdit(Model $record): bool
    {
        return (bool) static::user()?->isAdmin();
    }

    public static function canDelete(Model $record): bool
    {
        return (bool) static::user()?->isAdmin() && ! $record->is(static::user());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            TextInput::make('name')->label('Nama')->required(),
            TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
            TextInput::make('job_title')->label('Jabatan'),
            Select::make('role')->label('Peran')->options(Pmbok::ROLES)->default('anggota')->required()->native(false),
            TextInput::make('password')->label('Kata sandi')->password()->revealable()
                ->rule(\Illuminate\Validation\Rules\Password::default())
                ->required(fn (string $operation) => $operation === 'create')
                ->dehydrated(fn ($state) => filled($state))
                ->helperText('Kosongkan bila tidak diubah.'),
            Toggle::make('is_active')->label('Aktif')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable()->description(fn ($record) => $record->job_title),
                TextColumn::make('email')->searchable(),
                F::badge('role', 'Peran', Pmbok::ROLES, ['admin' => 'danger', 'pmo' => 'warning', 'manajer' => 'primary', 'anggota' => 'info', 'eksekutif' => 'success']),
                TextColumn::make('managed_projects_count')->label('Proyek dikelola')->counts('managedProjects')->alignCenter(),
                \Filament\Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('last_login_at')->label('Terakhir masuk')->since()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('role')->label('Peran')->options(Pmbok::ROLES),
            ])
            ->defaultSort('name')
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageUsers::route('/')];
    }
}
