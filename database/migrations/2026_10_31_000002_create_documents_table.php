<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Lampiran dokumen/gambar pada proyek, tugas WBS, risiko, isu, perubahan, mutu, kontrak, laporan status
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->cascadeOnDelete();
            $table->nullableMorphs('attachable');
            $table->string('title');
            $table->string('document_no', 60)->nullable();
            $table->string('revision', 10)->default('0');
            $table->text('description')->nullable();
            $table->string('category', 20)->default('lainnya');
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
