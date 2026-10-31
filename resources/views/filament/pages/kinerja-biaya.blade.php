@php use App\Support\Fmt; use App\Support\Pmbok; $ix = fn ($v) => $v === null ? '' : ($v >= 0.95 ? 'pm-ix-ok' : ($v >= 0.85 ? 'pm-ix-warn' : 'pm-ix-bad')); @endphp
<x-filament-panels::page>
    <div class="pm-card">
        <div class="pm-table-scroll"><table class="pm-table pm-table-num">
            <thead><tr><th>Proyek</th><th>Kesehatan</th><th class="r">BAC</th><th class="r">PV</th><th class="r">EV</th><th class="r">AC</th><th class="r">SV</th><th class="r">CV</th><th class="r">SPI</th><th class="r">CPI</th><th class="r">EAC</th><th class="r">ETC</th><th class="r">VAC</th><th class="r">TCPI</th><th class="r">% selesai</th></tr></thead>
            <tbody>
            @foreach ($projects as $p)
                @php($e = $p->evm())
                <tr>
                    <td><a href="{{ \App\Filament\Resources\Projects\ProjectResource::getUrl('view', ['record' => $p]) }}"><b>{{ $p->code }}</b> {{ \Illuminate\Support\Str::limit($p->name, 40) }}</a></td>
                    <td><span class="pm-rag pm-rag-{{ $p->health() }}">{{ Pmbok::RAG[$p->health()] }}</span></td>
                    <td class="r">{{ Fmt::money($e['bac']) }}</td><td class="r">{{ Fmt::money($e['pv']) }}</td><td class="r">{{ Fmt::money($e['ev']) }}</td><td class="r">{{ Fmt::money($e['ac']) }}</td>
                    <td class="r {{ $e['sv'] < 0 ? 'pm-neg' : '' }}">{{ Fmt::money($e['sv']) }}</td><td class="r {{ $e['cv'] < 0 ? 'pm-neg' : '' }}">{{ Fmt::money($e['cv']) }}</td>
                    <td class="r {{ $ix($e['spi']) }}">{{ Fmt::index($e['spi']) }}</td><td class="r {{ $ix($e['cpi']) }}">{{ Fmt::index($e['cpi']) }}</td>
                    <td class="r">{{ Fmt::money($e['eac']) }}</td><td class="r">{{ Fmt::money($e['etc']) }}</td><td class="r {{ $e['vac'] < 0 ? 'pm-neg' : '' }}">{{ Fmt::money($e['vac']) }}</td><td class="r">{{ Fmt::index($e['tcpi']) }}</td>
                    <td class="r">{{ Fmt::pct($e['actual_pct']) }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot><tr><th>Portofolio aktif</th><th></th><th class="r">{{ Fmt::money($totals['bac']) }}</th><th class="r">{{ Fmt::money($totals['pv']) }}</th><th class="r">{{ Fmt::money($totals['ev']) }}</th><th class="r">{{ Fmt::money($totals['ac']) }}</th><th class="r">{{ Fmt::money($totals['ev'] - $totals['pv']) }}</th><th class="r">{{ Fmt::money($totals['ev'] - $totals['ac']) }}</th><th class="r">{{ Fmt::index($totals['spi']) }}</th><th class="r">{{ Fmt::index($totals['cpi']) }}</th><th class="r">{{ Fmt::money($totals['eac']) }}</th><th colspan="4"></th></tr></tfoot>
        </table></div>
        <p class="pm-muted pm-formula">SV = EV − PV · CV = EV − AC · SPI = EV ÷ PV · CPI = EV ÷ AC · EAC = BAC ÷ CPI · ETC = EAC − AC · VAC = BAC − EAC · TCPI = (BAC − EV) ÷ (BAC − AC). PV dihitung dari baseline paket kerja pada tanggal status masing-masing proyek.</p>
    </div>
</x-filament-panels::page>
