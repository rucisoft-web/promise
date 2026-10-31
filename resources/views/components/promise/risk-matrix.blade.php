@props(['risks', 'projectId' => null])
@php
    $cells = [];
    foreach ($risks as $r) { $cells[$r->probability][$r->impact][] = $r; }
    $lv = fn ($s) => \App\Support\Pmbok::riskLevel($s);
@endphp
<div class="pm-matrix">
    <div class="pm-matrix-y">Probabilitas →</div>
    <table>
        @for ($p = 5; $p >= 1; $p--)
            <tr>
                <th>{{ $p }}</th>
                @for ($i = 1; $i <= 5; $i++)
                    @php($list = $cells[$p][$i] ?? [])
                    <td class="pm-m-{{ $lv($p * $i) }}" title="{{ collect($list)->map(fn ($r) => $r->code.' '.$r->title)->implode("\n") }}">
                        @if (count($list))
                            @php($f = ['probability' => ['value' => $p], 'impact' => ['value' => $i]] + ($projectId ? ['project_id' => ['value' => $projectId]] : []))
                            <a href="{{ \App\Filament\Resources\Risks\RiskResource::getUrl('index', ['filters' => $f]) }}" class="pm-m-link"><b>{{ count($list) }}</b></a>
                        @endif
                    </td>
                @endfor
            </tr>
        @endfor
        <tr><th></th>@for ($i = 1; $i <= 5; $i++)<th>{{ $i }}</th>@endfor</tr>
    </table>
    <div class="pm-matrix-x">Dampak →</div>
</div>
