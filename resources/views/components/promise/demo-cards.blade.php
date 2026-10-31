@props(['variant' => 'panel'])
@php
    $companies = \App\Support\DemoCompany::available();
    $class = $variant === 'landing' ? 'l-demo-cards' : 'pm-demo-cards';
@endphp
@if (count($companies))
    <div class="{{ $class }}" role="list" aria-label="Pilihan data demo">
        @foreach ($companies as $c)
            <a role="listitem" class="{{ $class }}-item" href="{{ route('promise.guest', ['perusahaan' => $c['key']]) }}" style="--c: {{ $c['color'] }}">
                <span class="{{ $class }}-tag">{{ $c['sector'] }}</span>
                <b class="{{ $class }}-name">{{ $c['name'] }}</b>
                @if ($variant === 'landing')
                    <span class="{{ $class }}-sum">{{ $c['summary'] }}</span>
                @endif
                <span class="{{ $class }}-meta">{{ $c['projects'] }} proyek · {{ $c['portfolios'] }} portofolio/program</span>
                <span class="{{ $class }}-go">Masuk sebagai Tamu &rarr;</span>
            </a>
        @endforeach
    </div>
@endif
