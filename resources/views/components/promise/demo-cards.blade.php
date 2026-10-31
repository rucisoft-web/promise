@props(['variant' => 'panel'])
@php
    $companies = \App\Support\DemoCompany::available();
    $onLanding = in_array($variant, ['landing', 'hero'], true);
    $c0 = $onLanding ? 'l-demo-cards' : 'pm-demo-cards';
    $btn = $onLanding ? 'l-btn l-btn-amber' : 'pm-guest-btn';
    $base = route('promise.guest');
@endphp
@if (count($companies))
    {{-- Langkah 1: pilih kartu perusahaan; langkah 2: Masuk sebagai Tamu ke dasbor perusahaan itu --}}
    <div class="pm-demo-select" data-demo-select data-base="{{ $base }}" wire:ignore>
        <div class="{{ $c0 }} {{ $variant === 'hero' ? 'l-demo-cards-hero' : '' }}" role="radiogroup" aria-label="Pilih data demo perusahaan">
            @foreach ($companies as $c)
                <button type="button" role="radio" aria-checked="false" class="{{ $c0 }}-item" data-demo-card="{{ $c['key'] }}" data-short="{{ $c['short'] }}" style="--c: {{ $c['color'] }}">
                    <span class="{{ $c0 }}-check" aria-hidden="true">&#10003;</span>
                    <span class="{{ $c0 }}-tag">{{ $c['sector'] }}</span>
                    <b class="{{ $c0 }}-name">{{ $c['name'] }}</b>
                    @if ($variant === 'landing')
                        <span class="{{ $c0 }}-sum">{{ $c['summary'] }}</span>
                    @endif
                    <span class="{{ $c0 }}-meta">{{ $c['projects'] }} proyek · {{ $c['portfolios'] }} portofolio/program</span>
                </button>
            @endforeach
        </div>
        <a href="{{ $base }}" class="{{ $btn }} pm-demo-go is-disabled" data-demo-go aria-disabled="true">Masuk sebagai Tamu</a>
        <p class="pm-demo-hint" data-demo-hint>Klik salah satu kartu perusahaan terlebih dahulu.</p>
    </div>
    @once
        <script>
            (function () {
                if (window.__pmDemoSelect) return; window.__pmDemoSelect = true;
                document.addEventListener('click', function (e) {
                    var card = e.target.closest('[data-demo-card]');
                    var go = e.target.closest('[data-demo-go]');
                    if (card) {
                        var box = card.closest('[data-demo-select]');
                        box.querySelectorAll('[data-demo-card]').forEach(function (b) {
                            var on = b === card; b.classList.toggle('is-selected', on); b.setAttribute('aria-checked', on ? 'true' : 'false');
                        });
                        var btn = box.querySelector('[data-demo-go]');
                        btn.href = box.dataset.base + '?perusahaan=' + encodeURIComponent(card.dataset.demoCard);
                        btn.classList.remove('is-disabled'); btn.removeAttribute('aria-disabled');
                        btn.textContent = 'Masuk sebagai Tamu · ' + card.dataset.short;
                        box.querySelector('[data-demo-hint]').textContent = 'Dasbor akan langsung menampilkan data ' + card.dataset.short + '.';
                    } else if (go && go.classList.contains('is-disabled')) {
                        e.preventDefault();
                        var hint = go.closest('[data-demo-select]').querySelector('[data-demo-hint]');
                        hint.classList.remove('is-shake'); void hint.offsetWidth; hint.classList.add('is-shake');
                    }
                });
            })();
        </script>
    @endonce
@endif
