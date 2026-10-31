<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Widgets\ProjectCostByWbsChart;
use App\Filament\Widgets\ProjectSCurveChart;
use App\Models\Document;
use App\Models\Project;
use App\Support\Pmbok;
use Filament\Resources\Pages\ViewRecord;

/** Ruang Proyek: ringkasan piagam, EVM, Gantt, WBS, risiko, perubahan, pengadaan, mutu, tim, dan dokumen. */
class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected string $view = 'filament.pages.ruang-proyek';

    public function getTitle(): string
    {
        return $this->record->name;
    }

    public function getSubheading(): ?string
    {
        $p = $this->record;

        return trim($p->code.' · '.(Pmbok::BUSINESS_UNITS[$p->business_unit] ?? '').' · '.$p->location.' · Manajer: '.($p->manager?->name ?? '-'), ' ·');
    }

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\EditAction::make()->label('Ubah proyek')];
    }

    protected function getFooterWidgets(): array
    {
        return [ProjectSCurveChart::class, ProjectCostByWbsChart::class];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return ['md' => 2];
    }

    protected function getViewData(): array
    {
        /** @var Project $p */
        $p = $this->record->load(['wbsItems.resource', 'portfolio', 'manager']);

        return [
            'p' => $p,
            'evm' => $p->evm(),
            'health' => $p->health(),
            'wbs' => $p->wbsItems,
            'risks' => $p->risks()->orderByRaw('probability * impact desc')->get(),
            'issues' => $p->issues()->latest('raised_at')->get(),
            'changes' => $p->changeRequests()->latest('requested_at')->get(),
            'contracts' => $p->contracts()->with('vendor')->get(),
            'quality' => $p->qualityChecks()->orderBy('planned_date')->get(),
            'team' => $p->assignments()->with('resource')->get(),
            'stakeholders' => $p->stakeholders()->orderByDesc('power')->get(),
            'comms' => $p->communicationPlans()->orderBy('next_date')->get(),
            'reports' => $p->statusReports()->latest('period_date')->limit(3)->get(),
            'lessons' => $p->lessons()->get(),
            'documents' => Document::with(['attachable', 'uploader'])->where('project_id', $p->id)->latest()->get(),
            'deps' => $p->dependencies()->with(['predecessor', 'successor'])->get(),
        ];
    }
}
