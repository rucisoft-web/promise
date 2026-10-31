<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/** Unduh/pratinjau lampiran. Berkas disimpan di disk privat, hanya pengguna yang masuk yang bisa membuka. */
class DocumentController extends Controller
{
    public function __invoke(Request $request, Document $document)
    {
        $disk = Storage::disk(Document::DISK);
        abort_unless($document->file_path && $disk->exists($document->file_path), 404);

        $name = $document->original_name ?: basename($document->file_path);
        $inline = $request->boolean('lihat') && ($document->isImage() || $document->mime_type === 'application/pdf');

        return $inline
            ? $disk->response($document->file_path, $name, ['Content-Type' => $document->mime_type])
            : $disk->download($document->file_path, $name);
    }
}
