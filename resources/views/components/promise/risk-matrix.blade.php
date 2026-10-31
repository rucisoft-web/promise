@props(['risks'])
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
                        @if (count($list))<b>{{ count($list) }}</b>@endif
                    </td>
                @endfor
            </tr>
        @endfor
        <tr><th></th>@for ($i = 1; $i <= 5; $i++)<th>{{ $i }}</th>@endfor</tr>
    </table>
    <div class="pm-matrix-x">Dampak →</div>
</div>
