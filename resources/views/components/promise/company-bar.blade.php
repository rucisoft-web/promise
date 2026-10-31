@php
    use App\Support\DemoCompany;
    $companies = DemoCompany::available();
    $current = DemoCompany::current();
    $own = config('promise.company_name');
@endphp
@if (count($companies))
    <div class="pm-company" aria-label="Perusahaan yang ditampilkan">
        <span class="pm-company-ic" aria-hidden="true">&#127970;</span>
        @if ($current)
            <span>Perusahaan: <b>{{ DemoCompany::name($current) }}</b> <em>(data demo)</em></span>
        @else
            <span>Perusahaan: <b>{{ collect($companies)->pluck('name')->join(', ', ' dan ') }}</b> <em>(semua data demo)</em></span>
        @endif
        <span class="pm-company-sw">
            {{ $current ? 'Ganti:' : 'Pilih:' }}
            @foreach ($companies as $c)
                @continue($c['key'] === $current)
                <a href="{{ route('promise.company', $c['key']) }}">{{ $c['short'] }}</a>
                @if (! $loop->last || $current) <span aria-hidden="true">·</span> @endif
            @endforeach
            @if ($current)
                <a href="{{ route('promise.company', 'semua') }}">Semua</a>
            @endif
        </span>
    </div>
@elseif (filled($own))
    <div class="pm-company"><span class="pm-company-ic" aria-hidden="true">&#127970;</span><span>Perusahaan: <b>{{ $own }}</b></span></div>
@endif
