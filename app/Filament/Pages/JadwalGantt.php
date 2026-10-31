<?php

namespace App\Filament\Pages;

use App\Models\Project;
use Filament\Pages\Page;
use Livewire\Attributes\Url;

/** Jadwal proyek: Gantt chart dengan baseline, milestone, dan keterkaitan FS/SS/FF/SF. */
class JadwalGantt extends Page
{
    protected static ?string $title = 'Jadwal Proyek (Gantt)';

    protected static ?string $navigationLabel = 'Gantt Chart';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static string|\UnitEnum|null $navigationGroup = 'Waktu';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'jadwal';

    protected string $view = 'filament.pages.jadwal-gantt';

    #[Url(as: 'proyek')]
    public ?int $projectId = null;

    #[Url(as: 'level')]
    public int $level = 2;

    public function mount(): void
    {
        $this->projectId ??= Project::orderByRaw("status = 'aktif' desc")->orderBy('code')->value('id');
    }

    protected function getViewData(): array
    {
        $project = $this->projectId ? Project::find($this->projectId) : null;

        return [
            'projects' => Project::orderBy('code')->get(),
            'project' => $project,
            'deps' => $project?->dependencies()->with(['predecessor', 'successor'])->get() ?? collect(),
        ];
    }
}
