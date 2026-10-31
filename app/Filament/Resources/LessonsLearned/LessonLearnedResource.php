<?php

namespace App\Filament\Resources\LessonsLearned;

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

class LessonLearnedResource extends Resource
{
    use ProjectScopedAccess;

    protected static ?string $model = Models\LessonLearned::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-light-bulb';

    protected static string|\UnitEnum|null $navigationGroup = 'Integrasi';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Pelajaran';

    protected static ?string $pluralModelLabel = 'Pelajaran (Lessons Learned)';

    protected static ?string $slug = 'pelajaran';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            F::projectSelect(),
            Select::make('knowledge_area')->label('Area pengetahuan')->options(Pmbok::KNOWLEDGE_AREAS)->default('integrasi')->native(false),
            Select::make('kind')->label('Sifat')->options(['positif' => 'Praktik baik', 'perbaikan' => 'Perlu perbaikan'])->default('positif')->native(false),
            TextInput::make('title')->label('Pelajaran')->required()->columnSpanFull(),
            Textarea::make('description')->label('Uraian')->rows(3)->columnSpanFull(),
            Textarea::make('recommendation')->label('Rekomendasi untuk proyek berikutnya')->rows(2)->columnSpan(2),
            DatePicker::make('recorded_at')->label('Tanggal')->default(now()),
            F::attachments(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                F::projectColumn(),
                TextColumn::make('title')->label('Pelajaran')->wrap()->searchable()->description(fn ($record) => $record->recommendation),
                TextColumn::make('knowledge_area')->label('Area')->badge()->formatStateUsing(fn ($state) => Pmbok::KNOWLEDGE_AREAS[$state] ?? $state),
                TextColumn::make('kind')->label('Sifat')->badge()->formatStateUsing(fn ($state) => $state === 'positif' ? 'Praktik baik' : 'Perlu perbaikan')->color(fn ($state) => $state === 'positif' ? 'success' : 'warning'),
                TextColumn::make('recorded_at')->label('Tanggal')->date('d M Y')->sortable(),
            ])
            ->filters([
                F::projectFilter(),
                SelectFilter::make('knowledge_area')->label('Area pengetahuan')->options(Pmbok::KNOWLEDGE_AREAS),
            ])
            ->defaultSort('recorded_at')
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageLessonsLearned::route('/')];
    }
}
