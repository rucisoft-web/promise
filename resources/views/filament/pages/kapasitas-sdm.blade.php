<x-filament-panels::page>
    <div class="pm-card">
        <p class="pm-muted">Persentase = jumlah alokasi penugasan proyek aktif pada bulan itu terhadap kapasitas orang tersebut. <span class="pm-heat pm-heat-0">0</span> kosong · <span class="pm-heat pm-heat-2">50–80</span> wajar · <span class="pm-heat pm-heat-3">80–100</span> penuh · <span class="pm-heat pm-heat-4">&gt;100</span> kelebihan beban.</p>
        <div class="pm-table-scroll"><table class="pm-table pm-heatmap">
            <thead><tr><th>Sumber daya</th><th>Fungsi</th>@foreach ($months as $m)<th class="c">{{ $m }}</th>@endforeach</tr></thead>
            <tbody>
            @foreach ($rows as $r)
                <tr>
                    <td><b>{{ $r['resource']->name }}</b><br><small class="pm-muted">{{ $r['resource']->discipline }}</small></td>
                    <td>{{ $r['resource']->department }}</td>
                    @foreach ($r['cells'] as $v)
                        @php($cls = $v <= 0 ? 0 : ($v < 50 ? 1 : ($v < 80 ? 2 : ($v <= 100 ? 3 : 4))))
                        <td class="c"><span class="pm-heat pm-heat-{{ $cls }}">{{ $v > 0 ? $v.'%' : '·' }}</span></td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table></div>
    </div>
</x-filament-panels::page>
