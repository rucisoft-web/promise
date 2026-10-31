<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Instalasi awal memakai panjang 40, terlalu pendek untuk sebagian jenis proyek di MySQL
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('project_type', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        //
    }
};
