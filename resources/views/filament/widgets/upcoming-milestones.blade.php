<x-filament-widgets::widget>
    <x-filament::section heading="Milestone mendatang" description="Belum tercapai, 30 hari lalu sampai 180 hari ke depan">
        <ul class="pm-list">
            @forelse ($items as $m)
                @php($late = $m->finish_date->lt($statusDate))
                <li><span class="pm-ms {{ $late ? 'pm-ms-late' : '' }}">◆</span><div><a class="pm-link" href="{{ \App\Filament\Pages\JadwalGantt::getUrl(['proyek' => $m->project_id]) }}"><b>{{ $m->name }}</b></a><small>{{ $m->project?->code }} · {{ $m->finish_date->translatedFormat('d M Y') }}@if ($late) · <span class="pm-late">lewat {{ $m->finish_date->diffInDays($statusDate) }} hari</span>@endif</small></div></li>
            @empty
                <li class="pm-muted">Tidak ada milestone dalam rentang ini.</li>
            @endforelse
        </ul>
    </x-filament::section>
</x-filament-widgets::widget>
