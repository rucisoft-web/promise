<?php

namespace App\Filament\Resources\Documents;

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

class DocumentResource extends Resource
{
    use ProjectScopedAccess;

    protected static ?string $model = Models\Document::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder-open';

    protected static string|\UnitEnum|null $navigationGroup = 'Umum';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Dokumen';

    protected static ?string $pluralModelLabel = 'Pusat Dokumen';

    protected static ?string $slug = 'dokumen';

    protected static ?string $navigationLabel = 'Pusat Dokumen';

    public static function canEdit(Model $record): bool
    {
        return (bool) static::user()?->canEditProject($record->project);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(4)->components([
            F::projectSelect()->columnSpan(2),
            Select::make('attachable_key')->label('Lampirkan ke')->columnSpan(2)
                ->options(fn (Get $get) => \App\Support\DocumentTargets::options((int) $get('project_id')))
                ->afterStateHydrated(fn ($component, $record) => $component->state($record?->attachable_type ? $record->attachable_type.'#'.$record->attachable_id : null))
                ->searchable()->helperText('Kosongkan untuk dokumen umum proyek.'),
            ...F::documentFields(),
            Textarea::make('description')->label('Keterangan')->rows(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['project', 'attachable', 'uploader']))
            ->columns([
                TextColumn::make('title')->label('Dokumen')->searchable(['title', 'original_name', 'document_no'])->wrap()
                    ->icon(fn ($record) => $record->isImage() ? 'heroicon-o-photo' : 'heroicon-o-document')
                    ->description(fn ($record) => $record->original_name)
                    ->url(fn ($record) => $record->url($record->isImage() || $record->extension === 'pdf'))->openUrlInNewTab(),
                F::projectColumn(),
                TextColumn::make('attachable_label')->label('Melekat pada')->state(fn ($record) => $record->attachable_label)->wrap()->toggleable(),
                TextColumn::make('category')->label('Kategori')->badge()->formatStateUsing(fn ($state) => Models\Document::CATEGORIES[$state] ?? $state),
                TextColumn::make('document_no')->label('Nomor')->toggleable(),
                TextColumn::make('revision')->label('Rev')->alignCenter(),
                TextColumn::make('ext')->label('Jenis')->state(fn ($record) => strtoupper($record->extension))->badge()->color('gray'),
                TextColumn::make('size')->label('Ukuran')->formatStateUsing(fn ($state, $record) => $record->size_label)->alignEnd()->toggleable(),
                TextColumn::make('uploader.name')->label('Pengunggah')->toggleable(),
                TextColumn::make('created_at')->label('Diunggah')->date('d M Y')->sortable(),
            ])
            ->filters([
                F::projectFilter(),
                SelectFilter::make('category')->label('Kategori')->options(Models\Document::CATEGORIES),
                SelectFilter::make('attachable_type')->label('Melekat pada')->options(Models\Document::ATTACHABLE_LABELS),
                SelectFilter::make('jenis')->label('Jenis berkas')->options(['pdf' => 'PDF', 'word' => 'Word', 'excel' => 'Excel', 'gambar' => 'Gambar'])
                    ->query(fn (Builder $q, array $data) => match ($data['value'] ?? null) {
                        'pdf' => $q->where('mime_type', 'application/pdf'),
                        'word' => $q->where('mime_type', 'like', '%word%'),
                        'excel' => $q->where(fn ($w) => $w->where('mime_type', 'like', '%sheet%')->orWhere('mime_type', 'like', '%excel%')->orWhere('mime_type', 'text/csv')),
                        'gambar' => $q->where('mime_type', 'like', 'image/%'),
                        default => $q,
                    }),
            ])
            ->defaultSort('created_at')
            ->recordActions([
                Action::make('unduh')->label('Unduh')->icon('heroicon-o-arrow-down-tray')->url(fn ($record) => $record->url())->openUrlInNewTab(),
                EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageDocuments::route('/')];
    }
}
