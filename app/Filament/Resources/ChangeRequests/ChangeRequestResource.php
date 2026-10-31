<?php

namespace App\Filament\Resources\ChangeRequests;

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

class ChangeRequestResource extends Resource
{
    use ProjectScopedAccess;

    protected static ?string $model = Models\ChangeRequest::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static string|\UnitEnum|null $navigationGroup = 'Integrasi';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Permintaan perubahan';

    protected static ?string $pluralModelLabel = 'Pengendalian Perubahan';

    protected static ?string $slug = 'perubahan';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            F::projectSelect(),
            TextInput::make('code')->label('Nomor CR')->required()->maxLength(20),
            Select::make('category')->label('Kategori')->options(Pmbok::CR_CATEGORIES)->default('lingkup')->native(false),
            TextInput::make('title')->label('Perubahan yang diminta')->required()->columnSpanFull(),
            Textarea::make('description')->label('Uraian & alasan')->rows(3)->columnSpanFull(),
            F::money('cost_impact', 'Dampak biaya'),
            TextInput::make('schedule_impact_days')->label('Dampak jadwal')->numeric()->default(0)->suffix('hari'),
            Select::make('status')->options(Pmbok::CR_STATUSES)->default('diajukan')->native(false),
            TextInput::make('requested_by')->label('Diajukan oleh'),
            DatePicker::make('requested_at')->label('Tanggal pengajuan')->default(now()),
            TextInput::make('decided_by')->label('Diputuskan oleh (CCB)'),
            DatePicker::make('decided_at')->label('Tanggal keputusan'),
            Textarea::make('decision_note')->label('Catatan keputusan')->rows(2)->columnSpan(2),
            F::attachments(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                F::projectColumn(),
                TextColumn::make('code')->label('Nomor')->searchable(),
                TextColumn::make('title')->label('Perubahan')->wrap()->searchable()->description(fn ($record) => Pmbok::CR_CATEGORIES[$record->category] ?? ''),
                F::moneyColumn('cost_impact', 'Dampak biaya'),
                TextColumn::make('schedule_impact_days')->label('Dampak jadwal')->suffix(' hari')->alignEnd(),
                TextColumn::make('requested_at')->label('Diajukan')->date('d M Y')->sortable(),
                F::badge('status', 'Status', Pmbok::CR_STATUSES, ['diajukan' => 'info', 'dianalisis' => 'warning', 'disetujui' => 'success', 'ditolak' => 'danger']),
                F::attachmentsColumn(),
            ])
            ->filters([
                F::projectFilter(),
                SelectFilter::make('status')->options(Pmbok::CR_STATUSES),
                SelectFilter::make('category')->label('Kategori')->options(Pmbok::CR_CATEGORIES),
            ])
            ->defaultSort('requested_at')
            ->recordActions([
                Action::make('setujui')->label('Setujui')->icon('heroicon-o-check')->color('success')
                    ->visible(fn ($record) => in_array($record->status, ['diajukan', 'dianalisis']) && static::canEdit($record))
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'disetujui', 'decided_at' => now(), 'decided_by' => auth()->user()->name])),
                EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageChangeRequests::route('/')];
    }
}
