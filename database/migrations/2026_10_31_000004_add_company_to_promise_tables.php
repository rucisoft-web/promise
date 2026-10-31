<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Label perusahaan data demo (medco / smbc) agar data dapat disaring per perusahaan. */
return new class extends Migration
{
    protected array $tables = ['portfolios', 'projects', 'resources', 'vendors'];

    public function up(): void
    {
        foreach ($this->tables as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->string('company', 20)->nullable()->index();
            });
        }

        // Isi label untuk data demo yang sudah terpasang sebelumnya
        DB::table('portfolios')->where('code', 'like', 'SMBC-%')->update(['company' => 'smbc']);
        DB::table('portfolios')->whereIn('code', ['HULU', 'POWER', 'MINING', 'KORP'])->update(['company' => 'medco']);
        foreach (['smbc', 'medco'] as $c) {
            $ids = DB::table('portfolios')->where('company', $c)->pluck('id');
            DB::table('projects')->whereIn('portfolio_id', $ids)->update(['company' => $c]);
        }
        DB::table('resources')->where('code', 'like', 'SMBC-%')->update(['company' => 'smbc']);
        DB::table('resources')->where(fn ($q) => $q->where('code', 'like', 'SDM-%')->orWhere('code', 'like', 'ALT-%'))->update(['company' => 'medco']);
        DB::table('vendors')->where('code', 'like', 'VB-%')->update(['company' => 'smbc']);
        DB::table('vendors')->where('code', 'like', 'V-%')->update(['company' => 'medco']);
    }

    public function down(): void
    {
        foreach ($this->tables as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->dropIndex(['company']);
                $table->dropColumn('company');
            });
        }
    }
};
