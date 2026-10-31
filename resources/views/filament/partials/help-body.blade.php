<div class="pm-help-body">
    @if (! empty($h['konsep']))
        <section><h3>Konsep</h3>@foreach ($h['konsep'] as $para)<p>{{ $para }}</p>@endforeach</section>
    @endif
    @if (! empty($h['langkah']))
        <section><h3>Cara memakai</h3><ol>@foreach ($h['langkah'] as $step)<li>{{ $step }}</li>@endforeach</ol></section>
    @endif
    @if (! empty($h['isian']))
        <section><h3>Data yang diisi</h3>
            <dl class="pm-help-fields">@foreach ($h['isian'] as [$field, $desc])<dt>{{ $field }}</dt><dd>{{ $desc }}</dd>@endforeach</dl>
        </section>
    @endif
    @if (! empty($h['contoh']))
        <section><h3>Contoh</h3>@foreach ($h['contoh'] as $ex)<p class="pm-help-ex">{{ $ex }}</p>@endforeach</section>
    @endif
    @if (! empty($shots))
        <section><h3>Tangkapan layar</h3>
            @foreach ($shots as $file => $caption)
                <figure class="pm-help-shot"><a href="{{ asset('images/bantuan/'.$file) }}" target="_blank" rel="noopener"><img src="{{ asset('images/bantuan/'.$file) }}" alt="{{ $caption }}" loading="lazy"></a><figcaption>{{ $caption }}</figcaption></figure>
            @endforeach
        </section>
    @endif
</div>
