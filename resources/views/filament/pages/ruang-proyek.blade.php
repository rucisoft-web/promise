@php
    use App\Support\Fmt;
    use App\Support\Pmbok;
    $f = fn (string $resource) => $resource::getUrl('index', ['filters' => ['project_id' => ['value' => $p->id]]]);
    $R = 'App\\Filament\\Resources\\';
    $idx = fn ($v) => $v === null ? 'gray' : ($v >= 0.95 ? 'ok' : ($v >= 0.85 ? 'warn' : 'bad'));
    $tabs = [
        'gantt' => 'Gantt & Jadwal', 'wbs' => 'WBS', 'risiko' => 'Risiko & Isu', 'perubahan' => 'Perubahan',
        'pengadaan' => 'Pengadaan', 'mutu' => 'Mutu', 'tim' => 'Tim & Komunikasi', 'dokumen' => 'Dokumen ('.$documents->count().')', 'piagam' => 'Piagam',
    ];
@endphp
<x-filament-panels::page>
    <div class="pm-kpis">
        <div class="pm-kpi"><span>Kesehatan</span><b class="pm-rag pm-rag-{{ $health }}">{{ Pmbok::RAG[$health] }}</b><small>{{ $p->phase_label }} · {{ Pmbok::PROJECT_STATUSES[$p->status] ?? $p->status }}</small></div>
        <div class="pm-kpi"><span>% selesai (EV/BAC)</span><b>{{ Fmt::pct($evm['actual_pct']) }}</b><small>rencana {{ Fmt::pct($evm['planned_pct']) }}</small>
            <div class="pm-bar"><i style="width: {{ min(100, $evm['actual_pct']) }}%"></i><em style="left: {{ min(100, $evm['planned_pct']) }}%"></em></div></div>
        <div class="pm-kpi"><span>Anggaran (BAC)</span><b>{{ Fmt::money($evm['bac']) }}</b><small>kontinjensi {{ Fmt::money($p->contingency) }}</small></div>
        <div class="pm-kpi"><span>Nilai hasil (EV)</span><b>{{ Fmt::money($evm['ev']) }}</b><small>PV {{ Fmt::money($evm['pv']) }} · AC {{ Fmt::money($evm['ac']) }}</small></div>
        <div class="pm-kpi"><span>SPI</span><b class="pm-ix-{{ $idx($evm['spi']) }}">{{ Fmt::index($evm['spi']) }}</b><small>SV {{ Fmt::money($evm['sv']) }}</small></div>
        <div class="pm-kpi"><span>CPI</span><b class="pm-ix-{{ $idx($evm['cpi']) }}">{{ Fmt::index($evm['cpi']) }}</b><small>CV {{ Fmt::money($evm['cv']) }}</small></div>
        <div class="pm-kpi"><span>Perkiraan akhir (EAC)</span><b>{{ Fmt::money($evm['eac']) }}</b><small>VAC {{ Fmt::money($evm['vac']) }} · TCPI {{ Fmt::index($evm['tcpi']) }}</small></div>
        <div class="pm-kpi"><span>Jadwal</span><b>{{ $p->start_date?->format('d/m/y') }} – {{ $p->finish_date?->format('d/m/y') }}</b><small>tanggal status {{ $p->statusDate()->format('d/m/Y') }}</small></div>
    </div>

    <div x-data="{ tab: (new URLSearchParams(location.search)).get('bagian') || 'gantt' }" class="pm-card">
        <nav class="pm-tabs" role="tablist">
            @foreach ($tabs as $k => $label)
                <button type="button" role="tab" :aria-selected="tab === '{{ $k }}'" :class="{ 'is-on': tab === '{{ $k }}' }" @click="tab = '{{ $k }}'">{{ $label }}</button>
            @endforeach
        </nav>

        <section x-show="tab === 'gantt'" class="pm-pane">
            <div class="pm-pane-head"><h3>Gantt chart dengan keterkaitan antartugas</h3>
                <span><a href="{{ \App\Filament\Pages\JadwalGantt::getUrl(['proyek' => $p->id]) }}">Layar penuh</a> · <a href="{{ $f($R.'TaskDependencies\\TaskDependencyResource') }}">Kelola keterkaitan ({{ $deps->count() }})</a></span></div>
            <x-promise.gantt :project="$p" />
        </section>

        <section x-show="tab === 'wbs'" x-cloak class="pm-pane">
            <div class="pm-pane-head"><h3>Work Breakdown Structure</h3><a href="{{ $f($R.'WbsItems\\WbsItemResource') }}">Kelola WBS</a></div>
            <div class="pm-table-scroll"><table class="pm-table">
                <thead><tr><th>Kode</th><th>Elemen WBS</th><th>Deliverable</th><th>PJ</th><th>Mulai</th><th>Selesai</th><th class="r">Anggaran (PV)</th><th class="r">Aktual (AC)</th><th class="r">% selesai</th></tr></thead>
                <tbody>
                @foreach ($wbs as $w)
                    <tr class="{{ $w->level === 1 ? 'pm-tr-sum' : '' }}">
                        <td>{{ $w->code }}</td>
                        <td style="padding-left: {{ 0.6 + ($w->level - 1) * 1.2 }}rem">@if ($w->is_milestone)◆ @endif{{ $w->name }}</td>
                        <td>{{ $w->deliverable }}</td>
                        <td>{{ $w->resource?->name }}</td>
                        <td>{{ $w->start_date?->format('d/m/y') }}</td>
                        <td>{{ $w->finish_date?->format('d/m/y') }}@if ($w->slip_days > 0) <span class="pm-late">+{{ $w->slip_days }}h</span>@endif</td>
                        <td class="r">{{ Fmt::money($w->planned_cost) }}</td>
                        <td class="r">{{ Fmt::money($w->actual_cost) }}</td>
                        <td class="r"><div class="pm-mini"><i style="width: {{ min(100, $w->percent_complete) }}%"></i></div>{{ Fmt::num($w->percent_complete) }}%</td>
                    </tr>
                @endforeach
                </tbody>
            </table></div>
        </section>

        <section x-show="tab === 'risiko'" x-cloak class="pm-pane">
            <div class="pm-grid-2">
                <div>
                    <div class="pm-pane-head"><h3>Matriks risiko (probabilitas × dampak)</h3><a href="{{ $f($R.'Risks\\RiskResource') }}">Register risiko</a></div>
                    <x-promise.risk-matrix :risks="$risks->where('status', '!=', 'ditutup')" />
                </div>
                <div>
                    <div class="pm-pane-head"><h3>Risiko teratas</h3></div>
                    <ul class="pm-list">
                        @forelse ($risks->where('status', '!=', 'ditutup')->take(6) as $r)
                            <li><span class="pm-score pm-score-{{ Pmbok::riskLevel($r->score) }}">{{ $r->score }}</span><div><b>{{ $r->code }} {{ $r->title }}</b><small>{{ Pmbok::RISK_RESPONSES[$r->response_strategy] ?? $r->response_strategy }} · {{ $r->owner }} · {{ Pmbok::RISK_STATUSES[$r->status] ?? $r->status }}</small></div></li>
                        @empty <li class="pm-muted">Belum ada risiko terbuka.</li> @endforelse
                    </ul>
                    <div class="pm-pane-head"><h3>Isu</h3><a href="{{ $f($R.'Issues\\IssueResource') }}">Log isu</a></div>
                    <ul class="pm-list">
                        @forelse ($issues->take(6) as $i)
                            <li><span class="pm-dot pm-dot-{{ $i->status === 'selesai' ? 'ok' : ($i->priority === 'tinggi' ? 'bad' : 'warn') }}"></span><div><b>{{ $i->code }} {{ $i->title }}</b><small>{{ Pmbok::ISSUE_STATUSES[$i->status] ?? $i->status }} · {{ $i->owner }} · tenggat {{ $i->due_date?->format('d/m/Y') }}</small></div></li>
                        @empty <li class="pm-muted">Tidak ada isu.</li> @endforelse
                    </ul>
                </div>
            </div>
        </section>

        <section x-show="tab === 'perubahan'" x-cloak class="pm-pane">
            <div class="pm-pane-head"><h3>Pengendalian perubahan terpadu</h3><a href="{{ $f($R.'ChangeRequests\\ChangeRequestResource') }}">Kelola permintaan perubahan</a></div>
            <div class="pm-table-scroll"><table class="pm-table">
                <thead><tr><th>Kode</th><th>Perubahan</th><th>Kategori</th><th class="r">Dampak biaya</th><th class="r">Dampak jadwal</th><th>Status</th><th>Tanggal</th></tr></thead>
                <tbody>
                @forelse ($changes as $c)
                    <tr><td>{{ $c->code }}</td><td>{{ $c->title }}</td><td>{{ Pmbok::CR_CATEGORIES[$c->category] ?? $c->category }}</td><td class="r">{{ Fmt::money($c->cost_impact) }}</td><td class="r">{{ $c->schedule_impact_days }} hari</td><td><span class="pm-chip pm-chip-{{ $c->status }}">{{ Pmbok::CR_STATUSES[$c->status] ?? $c->status }}</span></td><td>{{ $c->requested_at?->format('d/m/Y') }}</td></tr>
                @empty <tr><td colspan="7" class="pm-muted">Belum ada permintaan perubahan.</td></tr> @endforelse
                </tbody>
            </table></div>
            @if ($lessons->isNotEmpty())
                <div class="pm-pane-head"><h3>Pelajaran (lessons learned)</h3><a href="{{ $f($R.'LessonsLearned\\LessonLearnedResource') }}">Semua pelajaran</a></div>
                <ul class="pm-list">@foreach ($lessons as $l)<li><span class="pm-dot pm-dot-{{ $l->kind === 'positif' ? 'ok' : 'warn' }}"></span><div><b>{{ $l->title }}</b><small>{{ Pmbok::KNOWLEDGE_AREAS[$l->knowledge_area] ?? '' }} · {{ $l->recommendation }}</small></div></li>@endforeach</ul>
            @endif
        </section>

        <section x-show="tab === 'pengadaan'" x-cloak class="pm-pane">
            <div class="pm-pane-head"><h3>Kontrak & pengadaan</h3><a href="{{ $f($R.'Contracts\\ContractResource') }}">Kelola kontrak</a></div>
            <div class="pm-table-scroll"><table class="pm-table">
                <thead><tr><th>Nomor</th><th>Paket</th><th>Vendor</th><th>Jenis</th><th class="r">Nilai</th><th class="r">Dibayar</th><th class="r">Progres</th><th>Status</th></tr></thead>
                <tbody>
                @forelse ($contracts as $c)
                    <tr><td>{{ $c->number }}</td><td>{{ $c->title }}</td><td>{{ $c->vendor?->name }}</td><td>{{ Pmbok::CONTRACT_TYPES[$c->contract_type] ?? $c->contract_type }}</td><td class="r">{{ Fmt::money($c->value) }}</td><td class="r">{{ Fmt::money($c->paid_amount) }}</td><td class="r">{{ $c->progress }}%</td><td>{{ Pmbok::CONTRACT_STATUSES[$c->status] ?? $c->status }}</td></tr>
                @empty <tr><td colspan="8" class="pm-muted">Belum ada kontrak.</td></tr> @endforelse
                </tbody>
            </table></div>
        </section>

        <section x-show="tab === 'mutu'" x-cloak class="pm-pane">
            <div class="pm-pane-head"><h3>Pemeriksaan mutu</h3><a href="{{ $f($R.'QualityChecks\\QualityCheckResource') }}">Kelola pemeriksaan</a></div>
            <div class="pm-table-scroll"><table class="pm-table">
                <thead><tr><th>Kode</th><th>Pemeriksaan</th><th>Jenis</th><th>Standar</th><th>Rencana</th><th>Hasil</th><th class="r">Cacat</th></tr></thead>
                <tbody>
                @forelse ($quality as $q)
                    <tr><td>{{ $q->code }}</td><td>{{ $q->title }}</td><td>{{ Pmbok::QUALITY_TYPES[$q->check_type] ?? $q->check_type }}</td><td>{{ $q->standard }}</td><td>{{ $q->planned_date?->format('d/m/Y') }}</td><td><span class="pm-chip pm-chip-{{ $q->result }}">{{ Pmbok::QUALITY_RESULTS[$q->result] ?? $q->result }}</span></td><td class="r">{{ $q->defects }}</td></tr>
                @empty <tr><td colspan="7" class="pm-muted">Belum ada pemeriksaan mutu.</td></tr> @endforelse
                </tbody>
            </table></div>
        </section>

        <section x-show="tab === 'tim'" x-cloak class="pm-pane">
            <div class="pm-grid-2">
                <div>
                    <div class="pm-pane-head"><h3>Tim proyek</h3><a href="{{ $f($R.'Assignments\\AssignmentResource') }}">Penugasan</a></div>
                    <ul class="pm-list">
                        @forelse ($team->unique('resource_id') as $a)
                            <li><span class="pm-avatar">{{ \Illuminate\Support\Str::of($a->resource?->name)->explode(' ')->map(fn ($s) => mb_substr($s, 0, 1))->take(2)->implode('') }}</span><div><b>{{ $a->resource?->name }}</b><small>{{ $a->project_role }} · alokasi {{ $a->allocation }}% · {{ $a->resource?->discipline }}</small></div></li>
                        @empty <li class="pm-muted">Belum ada penugasan.</li> @endforelse
                    </ul>
                </div>
                <div>
                    <div class="pm-pane-head"><h3>Pemangku kepentingan</h3><a href="{{ $f($R.'Stakeholders\\StakeholderResource') }}">Register</a></div>
                    <ul class="pm-list">
                        @foreach ($stakeholders as $s)
                            <li><span class="pm-score pm-score-{{ $s->power * $s->interest >= 15 ? 'tinggi' : ($s->power * $s->interest >= 8 ? 'sedang' : 'rendah') }}">{{ $s->power }}·{{ $s->interest }}</span><div><b>{{ $s->name }}</b><small>{{ $s->organization }} · {{ Pmbok::ATTITUDES[$s->attitude] ?? '' }} · {{ $s->engagement_strategy }}</small></div></li>
                        @endforeach
                    </ul>
                    <div class="pm-pane-head"><h3>Rencana komunikasi</h3><a href="{{ $f($R.'CommunicationPlans\\CommunicationPlanResource') }}">Kelola</a></div>
                    <ul class="pm-list">
                        @foreach ($comms as $c)
                            <li><span class="pm-dot pm-dot-ok"></span><div><b>{{ $c->title }}</b><small>{{ Pmbok::FREQUENCIES[$c->frequency] ?? '' }} · {{ $c->audience }} · berikutnya {{ $c->next_date?->format('d/m/Y') }}</small></div></li>
                        @endforeach
                    </ul>
                    @if ($reports->isNotEmpty())
                        <div class="pm-pane-head"><h3>Laporan status terakhir</h3><a href="{{ $f($R.'StatusReports\\StatusReportResource') }}">Semua laporan</a></div>
                        <ul class="pm-list">@foreach ($reports as $r)<li><span class="pm-dot pm-dot-{{ ['hijau' => 'ok', 'kuning' => 'warn', 'merah' => 'bad'][$r->overall_status] ?? 'ok' }}"></span><div><b>{{ $r->period_date->translatedFormat('F Y') }}</b><small>{{ $r->summary }}</small></div></li>@endforeach</ul>
                    @endif
                </div>
            </div>
        </section>

        <section x-show="tab === 'dokumen'" x-cloak class="pm-pane">
            <div class="pm-pane-head"><h3>Pusat dokumen proyek</h3><a href="{{ $f($R.'Documents\\DocumentResource') }}">Kelola & unggah dokumen</a></div>
            <x-promise.document-grid :documents="$documents" />
        </section>

        <section x-show="tab === 'piagam'" x-cloak class="pm-pane">
            <div class="pm-charter">
                @foreach (['description' => 'Deskripsi', 'justification' => 'Justifikasi bisnis', 'objectives' => 'Tujuan terukur', 'success_criteria' => 'Kriteria keberhasilan', 'scope_statement' => 'Pernyataan ruang lingkup', 'out_of_scope' => 'Di luar lingkup', 'assumptions' => 'Asumsi', 'constraints' => 'Kendala'] as $k => $label)
                    @if ($p->{$k})<div><h4>{{ $label }}</h4><p>{!! nl2br(e($p->{$k})) !!}</p></div>@endif
                @endforeach
                <div><h4>Sponsor & portofolio</h4><p>{{ $p->sponsor }}<br>{{ $p->portfolio?->name }}</p></div>
            </div>
        </section>
    </div>
</x-filament-panels::page>
