<?php

namespace App\Filament\Support;

use App\Models\Document;
use App\Models\Project;
use App\Models\WbsItem;
use App\Support\Fmt;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

/** Komponen form/tabel yang berulang di semua modul Promise. */
class F
{
    public static function projectSelect(): Select
    {
        return Select::make('project_id')->label('Proyek')
            ->options(fn () => Project::editableBy(auth()->user())->orderBy('code')->get()->mapWithKeys(fn ($p) => [$p->id => $p->label]))
            ->searchable()->required()->live()
            ->default(fn () => request()->integer('proyek') ?: null);
    }

    public static function wbsSelect(string $name = 'wbs_item_id', string $label = 'Elemen WBS', bool $leafOnly = false): Select
    {
        return Select::make($name)->label($label)
            ->options(function (Get $get) use ($leafOnly) {
                $q = WbsItem::where('project_id', $get('project_id') ?: 0)->orderBy('sort')->orderBy('code');
                if ($leafOnly) {
                    $q->where('level', '>', 1);
                }

                return $q->get()->mapWithKeys(fn ($w) => [$w->id => str_repeat('— ', max(0, $w->level - 1)).$w->label]);
            })
            ->searchable();
    }

    public static function projectColumn(): TextColumn
    {
        return TextColumn::make('project.code')->label('Proyek')->sortable()->searchable()
            ->tooltip(fn ($record) => $record->project?->name)
            ->badge()->color('gray');
    }

    public static function projectFilter(): SelectFilter
    {
        return SelectFilter::make('project_id')->label('Proyek')
            ->options(fn () => Project::orderBy('code')->get()->mapWithKeys(fn ($p) => [$p->id => $p->label]))
            ->searchable();
    }

    public static function money(string $name, string $label): TextInput
    {
        return TextInput::make($name)->label($label)->numeric()->prefix(config('promise.currency'))->default(0);
    }

    public static function moneyColumn(string $name, string $label): TextColumn
    {
        return TextColumn::make($name)->label($label)->alignEnd()->sortable()
            ->formatStateUsing(fn ($state) => Fmt::money($state));
    }

    /** @param array<string,string> $options @param array<string,string> $colors */
    public static function badge(string $name, string $label, array $options, array $colors = []): TextColumn
    {
        return TextColumn::make($name)->label($label)->badge()
            ->formatStateUsing(fn ($state) => $options[$state] ?? $state)
            ->color(fn ($state) => $colors[$state] ?? 'gray');
    }

    /** Bagian form "Lampiran": unggah PDF, Word, Excel, PowerPoint, PNG/JPG ke record ini (masuk Pusat Dokumen). */
    public static function attachments(): Section
    {
        return Section::make('Lampiran dokumen & gambar')
            ->description('PDF, Word, Excel, PowerPoint, PNG, JPG, maksimal 20 MB per berkas. Semua lampiran juga tampil di Pusat Dokumen.')
            ->icon('heroicon-o-paper-clip')
            ->collapsible()
            ->columnSpanFull()
            ->schema([
                Repeater::make('documents')->relationship()->hiddenLabel()
                    ->schema(static::documentFields())
                    ->columns(4)
                    ->defaultItems(0)
                    ->addActionLabel('Tambah lampiran')
                    ->itemLabel(fn (array $state) => $state['title'] ?? null)
                    ->collapsible(),
            ]);
    }

    /** @return array<int,\Filament\Schemas\Components\Component|\Filament\Forms\Components\Field> */
    public static function documentFields(): array
    {
        return [
            FileUpload::make('file_path')->label('Berkas')
                ->disk(Document::DISK)->directory(Document::DIRECTORY)->visibility('private')
                ->acceptedFileTypes(Document::ACCEPTED_MIME)
                ->maxSize(Document::MAX_KB)
                ->storeFileNamesIn('original_name')
                ->downloadable()->openable()->previewable()
                ->required()->columnSpan(2),
            TextInput::make('title')->label('Judul dokumen')->maxLength(255)->columnSpan(2)
                ->helperText('Kosongkan untuk memakai nama berkas.'),
            Select::make('category')->label('Kategori')->options(Document::CATEGORIES)->default('lainnya')->native(false),
            TextInput::make('document_no')->label('Nomor dokumen')->maxLength(60),
            TextInput::make('revision')->label('Revisi')->default('0')->maxLength(10),
        ];
    }

    public static function attachmentsColumn(): TextColumn
    {
        return TextColumn::make('documents_count')->label('Lampiran')->counts('documents')
            ->icon('heroicon-o-paper-clip')->alignCenter()->toggleable()
            ->formatStateUsing(fn ($state) => $state ?: '–');
    }
}
