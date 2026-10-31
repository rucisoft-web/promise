<x-filament-widgets::widget>
    <x-filament::section heading="Peta panas risiko portofolio" description="Jumlah risiko terbuka per sel probabilitas × dampak">
        <div class="pm-grid-2">
            <x-promise.risk-matrix :risks="$risks" />
            <ul class="pm-list">
                @foreach ($top as $r)
                    <li><span class="pm-score pm-score-{{ \App\Support\Pmbok::riskLevel($r->score) }}">{{ $r->score }}</span><div><b>{{ $r->title }}</b><small>{{ $r->project?->code }} · {{ $r->owner }} · {{ \App\Support\Pmbok::RISK_RESPONSES[$r->response_strategy] ?? '' }}</small></div></li>
                @endforeach
            </ul>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
