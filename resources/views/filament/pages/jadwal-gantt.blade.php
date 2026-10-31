<x-filament-panels::page>
    <div class="pm-toolbar">
        <label>Proyek
            <select wire:model.live="projectId" class="pm-select">
                @foreach ($projects as $p)
                    <option value="{{ $p->id }}">{{ $p->code }} · {{ $p->name }}</option>
                @endforeach
            </select>
        </label>
        <label>Tampilkan
            <select wire:model.live="level" class="pm-select">
                <option value="1">WBS level 1</option>
                <option value="2">WBS level 1–2</option>
                <option value="3">Semua level</option>
            </select>
        </label>
        @if ($project)
            <a class="pm-link" href="{{ \App\Filament\Resources\Projects\ProjectResource::getUrl('view', ['record' => $project]) }}">Ruang proyek →</a>
            <a class="pm-link" href="{{ \App\Filament\Resources\TaskDependencies\TaskDependencyResource::getUrl('index', ['filters' => ['project_id' => ['value' => $project->id]]]) }}">Kelola keterkaitan →</a>
        @endif
    </div>
    @if ($project)
        <div class="pm-card" wire:key="gantt-{{ $project->id }}-{{ $level }}">
            <x-promise.gantt :project="$project" :level="$level" />
        </div>
        <div class="pm-card">
            <h3 class="pm-h3">Keterkaitan antartugas ({{ $deps->count() }})</h3>
            <p class="pm-muted">FS = penerus mulai setelah pendahulu selesai · SS = mulai bersamaan · FF = selesai bersamaan · SF = penerus selesai setelah pendahulu mulai. Lag positif menunda, negatif (lead) mempercepat.</p>
            <div class="pm-table-scroll"><table class="pm-table">
                <thead><tr><th>Pendahulu</th><th>Jenis</th><th>Penerus</th><th>Lag</th><th>Status</th></tr></thead>
                <tbody>
                @foreach ($deps as $d)
                    <tr><td>{{ $d->predecessor?->code }} {{ $d->predecessor?->name }}</td><td><span class="pm-chip pm-dep-{{ $d->type }}">{{ $d->type }}</span></td><td>{{ $d->successor?->code }} {{ $d->successor?->name }}</td><td>{{ $d->lag_days }} hari</td><td>@if ($d->isSatisfied())<span class="pm-chip pm-chip-lulus">Terpenuhi</span>@else<span class="pm-chip pm-chip-gagal">Dilanggar</span>@endif</td></tr>
                @endforeach
                </tbody>
            </table></div>
        </div>
    @endif
</x-filament-panels::page>
