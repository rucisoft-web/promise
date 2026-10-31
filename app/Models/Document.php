<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use Concerns\ScopedByProjectCompany;

    protected static bool $projectOptional = true;

    public const DISK = 'local';

    public const DIRECTORY = 'dokumen';

    /** Jenis berkas yang boleh diunggah: PDF, Word, Excel, PowerPoint, gambar. */
    public const ACCEPTED_MIME = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/csv',
        'image/png',
        'image/jpeg',
        'image/webp',
    ];

    public const MAX_KB = 20480;

    public const CATEGORIES = [
        'gambar_teknik' => 'Gambar teknik',
        'spesifikasi' => 'Spesifikasi',
        'laporan' => 'Laporan',
        'foto' => 'Foto lapangan',
        'kontrak' => 'Kontrak/legal',
        'notulen' => 'Notulen',
        'sertifikat' => 'Sertifikat/izin',
        'lainnya' => 'Lainnya',
    ];

    /** Label jenis induk lampiran. */
    public const ATTACHABLE_LABELS = [
        Project::class => 'Proyek',
        WbsItem::class => 'Tugas WBS',
        Risk::class => 'Risiko',
        Issue::class => 'Isu',
        ChangeRequest::class => 'Permintaan perubahan',
        QualityCheck::class => 'Pemeriksaan mutu',
        Contract::class => 'Kontrak',
        StatusReport::class => 'Laporan status',
        LessonLearned::class => 'Pelajaran',
    ];

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::saving(function (self $doc) {
            $doc->uploaded_by ??= auth()->id();
            if ($doc->isDirty('file_path') && $doc->file_path && Storage::disk(self::DISK)->exists($doc->file_path)) {
                $disk = Storage::disk(self::DISK);
                $doc->mime_type = $disk->mimeType($doc->file_path) ?: null;
                $doc->size = (int) $disk->size($doc->file_path);
            }
            $doc->original_name ??= basename((string) $doc->file_path);
            $doc->title = $doc->title ?: pathinfo((string) $doc->original_name, PATHINFO_FILENAME);
            if (! $doc->project_id && $doc->attachable) {
                $doc->project_id = $doc->attachable instanceof Project ? $doc->attachable->id : $doc->attachable->project_id;
            }
        });
        static::updated(function (self $doc) {
            $old = $doc->getOriginal('file_path');
            if ($doc->wasChanged('file_path') && $old && ! static::where('file_path', $old)->exists()) {
                Storage::disk(self::DISK)->delete($old);
            }
        });
        static::deleted(function (self $doc) {
            if ($doc->file_path && ! static::where('file_path', $doc->file_path)->exists()) {
                Storage::disk(self::DISK)->delete($doc->file_path);
            }
        });
    }

    /** Isian form "Lampirkan ke" berbentuk "Kelas#id". */
    public function setAttachableKeyAttribute(?string $key): void
    {
        [$type, $id] = array_pad(explode('#', (string) $key, 2), 2, null);
        if ($type && $id && array_key_exists($type, self::ATTACHABLE_LABELS)) {
            $this->attributes['attachable_type'] = $type;
            $this->attributes['attachable_id'] = (int) $id;
        } else {
            $this->attributes['attachable_type'] = null;
            $this->attributes['attachable_id'] = null;
        }
    }

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }

    public function getExtensionAttribute(): string
    {
        return strtolower(pathinfo((string) ($this->original_name ?: $this->file_path), PATHINFO_EXTENSION));
    }

    public function getSizeLabelAttribute(): string
    {
        $s = (int) $this->size;

        return $s >= 1048576 ? number_format($s / 1048576, 1, ',', '.').' MB' : number_format(max(1, $s / 1024), 0, ',', '.').' KB';
    }

    public function getAttachableLabelAttribute(): string
    {
        $type = self::ATTACHABLE_LABELS[$this->attachable_type] ?? 'Lampiran';
        $a = $this->attachable;
        $name = match (true) {
            $a instanceof Project => $a->code,
            $a instanceof WbsItem => $a->code.' '.$a->name,
            $a instanceof StatusReport => $a->period_date?->translatedFormat('F Y'),
            $a !== null => trim(($a->code ?? $a->number ?? '').' '.($a->title ?? '')),
            default => '',
        };

        return trim($type.' '.$name);
    }

    public function url(bool $inline = false): string
    {
        return route('promise.document', ['document' => $this->id, 'lihat' => $inline ? 1 : null]);
    }
}
