@props(['documents'])
@php($icons = ['pdf' => 'PDF', 'doc' => 'DOC', 'docx' => 'DOC', 'xls' => 'XLS', 'xlsx' => 'XLS', 'csv' => 'CSV', 'ppt' => 'PPT', 'pptx' => 'PPT'])
@if ($documents->isEmpty())
    <p class="pm-muted">Belum ada dokumen. Unggah lewat menu Pusat Dokumen atau bagian Lampiran pada tugas, risiko, kontrak, dan record lain.</p>
@else
<div class="pm-docs">
    @foreach ($documents as $d)
        <a class="pm-doc" href="{{ $d->url($d->isImage() || $d->extension === 'pdf') }}" target="_blank" rel="noopener">
            @if ($d->isImage())
                <img src="{{ $d->url(true) }}" alt="{{ $d->title }}" loading="lazy">
            @else
                <span class="pm-doc-ic pm-doc-{{ $icons[$d->extension] ?? 'FILE' }}">{{ $icons[$d->extension] ?? strtoupper($d->extension ?: 'FILE') }}</span>
            @endif
            <b>{{ $d->title }}</b>
            <small>{{ $d->attachable_label }}</small>
            <small>{{ \App\Models\Document::CATEGORIES[$d->category] ?? '' }} · Rev {{ $d->revision }} · {{ $d->size_label }}</small>
        </a>
    @endforeach
</div>
@endif
