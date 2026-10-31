@props(['project', 'level' => 3])
@php($g = \App\Support\GanttChart::build($project, (int) $level))
<div class="pm-gantt-wrap">
    @if (empty($g['rows']))
        <p class="pm-muted">Belum ada elemen WBS bertanggal untuk proyek ini.</p>
    @else
    <div class="pm-gantt-legend">
        <span><i class="pm-lg pm-lg-task"></i>Tugas (progres diarsir)</span>
        <span><i class="pm-lg pm-lg-sum"></i>Ringkasan WBS level 1</span>
        <span><i class="pm-lg pm-lg-base"></i>Baseline</span>
        <span><i class="pm-lg pm-lg-late"></i>Terlambat dari baseline</span>
        <span><i class="pm-lg pm-lg-ms"></i>Milestone</span>
        <span><i class="pm-lg pm-lg-dep"></i>Keterkaitan FS/SS/FF/SF</span>
        <span><i class="pm-lg pm-lg-bad"></i>Keterkaitan dilanggar</span>
        <span><i class="pm-lg pm-lg-today"></i>Tanggal status {{ $g['statusDate'] }}</span>
    </div>
    <div class="pm-gantt-scroll">
    <svg class="pm-gantt" width="{{ $g['width'] }}" height="{{ $g['height'] }}" viewBox="0 0 {{ $g['width'] }} {{ $g['height'] }}" role="img" aria-label="Gantt chart {{ $project->name }}">
        <defs>
            <marker id="pm-arr-{{ $project->id }}" viewBox="0 0 8 8" refX="7" refY="4" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0 L8 4 L0 8 z" class="pm-g-arrowhead"/></marker>
            <marker id="pm-arr-bad-{{ $project->id }}" viewBox="0 0 8 8" refX="7" refY="4" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0 L8 4 L0 8 z" class="pm-g-arrowhead-bad"/></marker>
        </defs>
        {{-- kepala waktu --}}
        <rect x="0" y="0" width="{{ $g['width'] }}" height="{{ $g['header'] }}" class="pm-g-head"/>
        <text x="10" y="18" class="pm-g-hd">Kode</text>
        <text x="70" y="18" class="pm-g-hd">Elemen WBS / tugas</text>
        <text x="10" y="38" class="pm-g-sub">Mulai – Selesai · % selesai</text>
        @foreach ($g['years'] as $y)
            <text x="{{ $y['x'] + 4 }}" y="17" class="pm-g-hd">{{ $y['label'] }}</text>
            <line x1="{{ $y['x'] }}" x2="{{ $y['x'] }}" y1="0" y2="{{ $g['height'] }}" class="pm-g-year"/>
        @endforeach
        @foreach ($g['months'] as $m)
            <line x1="{{ $m['x'] }}" x2="{{ $m['x'] }}" y1="24" y2="{{ $g['height'] }}" class="pm-g-grid"/>
            @if ($m['w'] >= 18)
                <text x="{{ $m['x'] + $m['w'] / 2 }}" y="39" text-anchor="middle" class="pm-g-sub">{{ $m['label'] }}</text>
            @endif
        @endforeach
        <line x1="{{ $g['label'] }}" x2="{{ $g['label'] }}" y1="0" y2="{{ $g['height'] }}" class="pm-g-sep"/>

        {{-- baris --}}
        @foreach ($g['rows'] as $r)
            @php($mid = $r['y'] + $g['row'] / 2)
            <g class="pm-g-row">
                <title>{{ $r['tooltip'] }}</title>
                <rect x="0" y="{{ $r['y'] }}" width="{{ $g['width'] }}" height="{{ $g['row'] }}" class="{{ $loop->even ? 'pm-g-zebra' : 'pm-g-plain' }}"/>
                <text x="10" y="{{ $mid + 4 }}" class="pm-g-code {{ $r['summary'] ? 'pm-g-b' : '' }}">{{ $r['code'] }}</text>
                <text x="{{ 62 + ($r['level'] - 1) * 12 }}" y="{{ $mid + 4 }}" class="pm-g-name {{ $r['summary'] ? 'pm-g-b' : '' }}">{{ \Illuminate\Support\Str::limit($r['name'], 34 - ($r['level'] - 1) * 2) }}</text>
                <text x="{{ $g['label'] - 8 }}" y="{{ $mid + 4 }}" text-anchor="end" class="pm-g-pct">{{ \App\Support\Fmt::num($r['pct']) }}%</text>
                @if ($r['bx1'] !== null && ! $r['milestone'])
                    <rect x="{{ $r['bx1'] }}" y="{{ $r['y'] + $g['row'] - 8 }}" width="{{ max(2, $r['bx2'] - $r['bx1']) }}" height="4" rx="1" class="pm-g-base"/>
                @endif
                @if ($r['x1'] !== null)
                    @if ($r['milestone'])
                        @php($cx = $r['x1'])
                        <path d="M{{ $cx }} {{ $mid - 8 }} L{{ $cx + 8 }} {{ $mid }} L{{ $cx }} {{ $mid + 8 }} L{{ $cx - 8 }} {{ $mid }} Z" class="pm-g-ms {{ $r['pct'] >= 100 ? 'pm-g-ms-done' : '' }}"/>
                        <text x="{{ $cx + 12 }}" y="{{ $mid + 4 }}" class="pm-g-sub">{{ $r['finish'] }}</text>
                    @elseif ($r['summary'])
                        @php($w = max(3, $r['x2'] - $r['x1']))
                        <path d="M{{ $r['x1'] }} {{ $r['y'] + 7 }} h{{ $w }} v9 l-5 -4 H{{ $r['x1'] + 5 }} l-5 4 Z" class="pm-g-sum"/>
                        <rect x="{{ $r['x1'] }}" y="{{ $r['y'] + 7 }}" width="{{ $w * min(100, $r['pct']) / 100 }}" height="5" class="pm-g-sum-done"/>
                    @else
                        @php($w = max(3, $r['x2'] - $r['x1']))
                        <rect x="{{ $r['x1'] }}" y="{{ $r['y'] + 6 }}" width="{{ $w }}" height="12" rx="3" class="pm-g-task {{ $r['late'] ? 'pm-g-late' : '' }}"/>
                        <rect x="{{ $r['x1'] }}" y="{{ $r['y'] + 6 }}" width="{{ $w * min(100, $r['pct']) / 100 }}" height="12" rx="3" class="pm-g-done"/>
                        @if ($r['resource'] && $w < 260)
                            <text x="{{ $r['x2'] + 6 }}" y="{{ $mid + 2 }}" class="pm-g-res">{{ \Illuminate\Support\Str::limit($r['resource'], 22) }}</text>
                        @endif
                    @endif
                @endif
            </g>
        @endforeach

        {{-- panah keterkaitan --}}
        @foreach ($g['arrows'] as $a)
            <path d="{{ $a['path'] }}" class="pm-g-dep {{ $a['ok'] ? '' : 'pm-g-dep-bad' }}" marker-end="url(#{{ $a['ok'] ? 'pm-arr-' : 'pm-arr-bad-' }}{{ $project->id }})"><title>{{ $a['title'] }}</title></path>
        @endforeach

        @if ($g['today'])
            <line x1="{{ $g['today'] }}" x2="{{ $g['today'] }}" y1="24" y2="{{ $g['height'] }}" class="pm-g-today"/>
        @endif
    </svg>
    </div>
    @endif
</div>
