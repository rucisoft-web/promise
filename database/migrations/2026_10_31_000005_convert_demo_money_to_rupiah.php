<?php

use App\Support\Rupiah;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Mata uang tunggal Rupiah: data demo yang terpasang sebelumnya (disimpan dalam US$) dikonversi
 * dengan kurs asumsi Rupiah::DEMO_RATE. Data milik pengguna (tanpa label perusahaan demo) tidak diubah.
 */
return new class extends Migration
{
    public function up(): void
    {
        $ids = DB::table('projects')->whereNotNull('company')->pluck('id');
        Rupiah::convertProjects($ids);
        DB::table('resources')->whereNotNull('company')->update(Rupiah::multiplied(['cost_rate'], Rupiah::DEMO_RATE));

        // Teks risiko demo yang menyebut US$ diganti agar tidak rancu dengan mata uang tampilan
        foreach (['title', 'cause', 'response_plan'] as $col) {
            DB::table('risks')->whereIn('project_id', $ids)->where($col, 'like', '%US$%')
                ->update([$col => DB::raw("REPLACE(".$col.", 'US$', 'dolar AS')")]);
        }
    }

    public function down(): void
    {
        // Tidak dibalik: pembagian kembali dapat menimbulkan pembulatan.
    }
};
