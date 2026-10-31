<?php

namespace App\Filament\Resources\Issues;

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

class IssueResource extends Resource
{
    use ProjectScopedAccess;

    protected static ?string $model = Models\Issue::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static string|\UnitEnum|null $navigationGroup = 'Risiko';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Isu';

    protected static ?string $pluralModelLabel = 'Log Isu';

    protected static ?string $slug = 'isu';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            F::projectSelect(),
            TextInput::make('code')->label('Kode')->required()->maxLength(20),
            Select::make('priority')->label('Prioritas')->options(Pmbok::PRIORITIES)->default('sedang')->native(false),
            TextInput::make('title')->label('Isu')->required()->columnSpanFull(),
            Textarea::make('description')->label('Uraian')->rows(2)->columnSpanFull(),
            Select::make('risk_id')->label('Risiko asal')->options(fn (Get $get) => Models\Risk::where('project_id', $get('project_id') ?: 0)->pluck('title', 'id'))->searchable(),
            TextInput::make('owner')->label('Penanggung jawab'),
            Select::make('status')->options(Pmbok::ISSUE_STATUSES)->default('terbuka')->native(false),
            DatePicker::make('raised_at')->label('Tanggal')->default(now()),
            DatePicker::make('due_date')->label('Tenggat'),
            Textarea::make('resolution')->label('Penyelesaian')->rows(2)->columnSpanFull(),
            F::attachments(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                F::projectColumn(),
                TextColumn::make('code')->label('Kode')->searchable(),
                TextColumn::make('title')->label('Isu')->wrap()->searchable(),
                F::badge('priority', 'Prioritas', Pmbok::PRIORITIES, ['tinggi' => 'danger', 'sedang' => 'warning', 'rendah' => 'gray']),
                TextColumn::make('owner')->label('PJ'),
                TextColumn::make('due_date')->label('Tenggat')->date('d M Y')->sortable()
                    ->color(fn ($state, $record) => $state && $state->isPast() && $record->status !== 'selesai' ? 'danger' : null),
                F::badge('status', 'Status', Pmbok::ISSUE_STATUSES, ['terbuka' => 'danger', 'ditangani' => 'warning', 'selesai' => 'success']),
                F::attachmentsColumn(),
            ])
            ->filters([
                F::projectFilter(),
                SelectFilter::make('status')->options(Pmbok::ISSUE_STATUSES),
                SelectFilter::make('priority')->label('Prioritas')->options(Pmbok::PRIORITIES),
            ])
            ->defaultSort('raised_at')
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageIssues::route('/')];
    }
}
