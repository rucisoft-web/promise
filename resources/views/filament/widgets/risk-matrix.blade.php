<x-filament-widgets::widget>
    <x-filament::section heading="Peta panas risiko portofolio" description="Jumlah risiko terbuka per sel probabilitas × dampak">
        <div class="pm-grid-2">
            <x-promise.risk-matrix :risks="$risks" />
            <ul class="pm-list">
                @foreach ($top as $r)
                    <li><span class="pm-score pm-score-{{ \App\Support\Pmbok::riskLevel($r->score) }}">{{ $r->score }}</span><div><a class="pm-link" href="{{ $r->project ? \App\Filament\Resources\Projects\ProjectResource::getUrl('view', ['record' => $r->project_id]).'?bagian=risiko' : '#' }}"><b>{{ $r->title }}</b></a><small>{{ $r->project?->code }} · {{ $r->owner }} · {{ \App\Support\Pmbok::RISK_RESPONSES[$r->response_strategy] ?? '' }}</small></div></li>
                @endforeach
            </ul>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
