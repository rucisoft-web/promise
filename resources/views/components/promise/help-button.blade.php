@php
    $key = \App\Support\Help::keyForPath(request()->path());
    $h = \App\Support\Help::get($key);
    $shots = \App\Support\Help::shots($key);
@endphp
<div x-data="{ open: false }" x-on:keydown.escape.window="open = false" class="pm-help">
    <button type="button" x-on:click="open = true" class="pm-help-btn" title="Bantuan untuk layar ini">
        <x-filament::icon icon="heroicon-o-question-mark-circle" class="pm-help-ico" />
        <span>Bantuan</span>
    </button>
    <template x-teleport="body">
        <div x-show="open" x-cloak class="pm-help-wrap" role="dialog" aria-modal="true" aria-label="Bantuan: {{ $h['title'] }}">
            <div class="pm-help-overlay" x-on:click="open = false" x-show="open" x-transition.opacity></div>
            <aside class="pm-help-panel" x-show="open" x-transition:enter="pm-help-in" x-transition:enter-start="pm-help-from" x-transition:leave="pm-help-in" x-transition:leave-end="pm-help-from">
                <header>
                    <div><small>Bantuan · {{ $h['area'] }}</small><h2>{{ $h['title'] }}</h2></div>
                    <button type="button" x-on:click="open = false" aria-label="Tutup">✕</button>
                </header>
                @include('filament.partials.help-body', ['h' => $h, 'shots' => $shots])
                <footer><a href="{{ \App\Filament\Pages\PanduanPmbok::getUrl() }}#panduan-layar">Buka semua panduan layar →</a></footer>
            </aside>
        </div>
    </template>
</div>
