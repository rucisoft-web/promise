@php
    use App\Support\DemoCompany;
    $companies = DemoCompany::available();
    $current = DemoCompany::current();
    $own = config('promise.company_name');
@endphp
@if ($current && isset($companies[$current]))
    <div class="pm-company" aria-label="Perusahaan yang ditampilkan">
        <span class="pm-company-ic" aria-hidden="true">&#127970;</span>
        <span>Perusahaan: <b>{{ $companies[$current]['name'] }}</b> <em>(data demo)</em></span>
        @if (count($companies) > 1)
            <span class="pm-company-sw">
                Ganti ke:
                @foreach (collect($companies)->except($current) as $c)
                    <a href="{{ route('promise.company', $c['key']) }}">{{ $c['short'] }}</a>@if (! $loop->last) <span aria-hidden="true">·</span> @endif
                @endforeach
            </span>
        @endif
    </div>
@elseif (filled($own))
    <div class="pm-company"><span class="pm-company-ic" aria-hidden="true">&#127970;</span><span>Perusahaan: <b>{{ $own }}</b></span></div>
@endif
