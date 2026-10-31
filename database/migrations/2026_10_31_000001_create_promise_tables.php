<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('job_title')->nullable()->after('email');
            $table->string('role', 30)->default('anggota')->after('job_title');
            $table->boolean('is_active')->default(true)->after('role');
            $table->timestamp('last_login_at')->nullable();
        });

        // Portofolio & program (Manajemen Portofolio Proyek)
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('type', 20)->default('portofolio'); // portofolio | program
            $table->string('owner_name')->nullable();
            $table->text('strategic_objective')->nullable();
            $table->text('description')->nullable();
            $table->string('color', 9)->default('#1F4E8C');
            $table->timestamps();
        });

        // Integrasi: proyek & piagam proyek
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('business_unit', 40)->nullable();
            $table->string('project_type', 40)->nullable();
            $table->string('location')->nullable();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('sponsor')->nullable();
            $table->string('phase', 30)->default('inisiasi');
            $table->string('status', 20)->default('aktif');
            $table->string('priority', 10)->default('sedang');
            $table->unsignedTinyInteger('strategic_score')->default(3);
            $table->date('start_date')->nullable();
            $table->date('finish_date')->nullable();
            $table->date('baseline_start')->nullable();
            $table->date('baseline_finish')->nullable();
            $table->date('status_date')->nullable();
            $table->decimal('budget', 16, 2)->default(0);
            $table->decimal('contingency', 16, 2)->default(0);
            $table->text('description')->nullable();
            $table->text('justification')->nullable();
            $table->text('objectives')->nullable();
            $table->text('success_criteria')->nullable();
            $table->text('scope_statement')->nullable();
            $table->text('out_of_scope')->nullable();
            $table->text('assumptions')->nullable();
            $table->text('constraints')->nullable();
            $table->timestamps();
        });

        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 20)->default('tenaga_kerja');
            $table->string('discipline')->nullable();
            $table->string('department')->nullable();
            $table->string('email')->nullable();
            $table->decimal('cost_rate', 10, 2)->default(0);
            $table->unsignedSmallInteger('capacity_hours')->default(40);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Ruang lingkup & waktu: WBS, jadwal, milestone
        Schema::create('wbs_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('wbs_items')->cascadeOnDelete();
            $table->string('code', 20);
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('deliverable')->nullable();
            $table->unsignedTinyInteger('level')->default(1);
            $table->unsignedInteger('sort')->default(0);
            $table->date('start_date')->nullable();
            $table->date('finish_date')->nullable();
            $table->date('baseline_start')->nullable();
            $table->date('baseline_finish')->nullable();
            $table->decimal('percent_complete', 5, 2)->default(0);
            $table->decimal('planned_cost', 16, 2)->default(0);
            $table->decimal('actual_cost', 16, 2)->default(0);
            $table->boolean('is_milestone')->default(false);
            $table->foreignId('resource_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->unique(['project_id', 'code']);
        });

        // Waktu: keterkaitan antartugas (PDM): FS, SS, FF, SF dengan lag/lead (hari)
        Schema::create('task_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('predecessor_id')->constrained('wbs_items')->cascadeOnDelete();
            $table->foreignId('successor_id')->constrained('wbs_items')->cascadeOnDelete();
            $table->string('type', 2)->default('FS');
            $table->integer('lag_days')->default(0);
            $table->timestamps();
            $table->unique(['predecessor_id', 'successor_id']);
        });

        // SDM: penugasan & lembar waktu
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('resource_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wbs_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('project_role')->nullable();
            $table->unsignedTinyInteger('allocation')->default(100);
            $table->date('start_date')->nullable();
            $table->date('finish_date')->nullable();
            $table->decimal('planned_hours', 10, 2)->default(0);
            $table->decimal('actual_hours', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('timesheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wbs_item_id')->nullable()->constrained()->nullOnDelete();
            $table->date('week_start');
            $table->decimal('hours', 6, 2)->default(0);
            $table->string('description')->nullable();
            $table->string('status', 15)->default('draf');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        // Risiko & isu
        Schema::create('risks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('code', 20);
            $table->string('title');
            $table->string('category', 30)->default('teknis');
            $table->text('cause')->nullable();
            $table->text('effect')->nullable();
            $table->unsignedTinyInteger('probability')->default(3);
            $table->unsignedTinyInteger('impact')->default(3);
            $table->string('response_strategy', 20)->default('mitigasi');
            $table->text('response_plan')->nullable();
            $table->string('owner')->nullable();
            $table->string('status', 15)->default('terbuka');
            $table->decimal('cost_exposure', 16, 2)->default(0);
            $table->date('identified_at')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamps();
        });

        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('risk_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code', 20);
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('priority', 10)->default('sedang');
            $table->string('status', 15)->default('terbuka');
            $table->string('owner')->nullable();
            $table->date('raised_at')->nullable();
            $table->date('due_date')->nullable();
            $table->text('resolution')->nullable();
            $table->timestamps();
        });

        // Integrasi: pengendalian perubahan terpadu & pelajaran
        Schema::create('change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('code', 20);
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category', 20)->default('lingkup');
            $table->decimal('cost_impact', 16, 2)->default(0);
            $table->integer('schedule_impact_days')->default(0);
            $table->string('status', 15)->default('diajukan');
            $table->string('requested_by')->nullable();
            $table->date('requested_at')->nullable();
            $table->string('decided_by')->nullable();
            $table->date('decided_at')->nullable();
            $table->text('decision_note')->nullable();
            $table->timestamps();
        });

        Schema::create('lessons_learned', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('knowledge_area', 20)->default('integrasi');
            $table->string('kind', 10)->default('positif');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('recommendation')->nullable();
            $table->date('recorded_at')->nullable();
            $table->timestamps();
        });

        // Mutu
        Schema::create('quality_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wbs_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code', 20);
            $table->string('title');
            $table->string('check_type', 20)->default('inspeksi');
            $table->string('standard')->nullable();
            $table->string('acceptance_criteria')->nullable();
            $table->date('planned_date')->nullable();
            $table->date('actual_date')->nullable();
            $table->string('result', 15)->default('terjadwal');
            $table->unsignedSmallInteger('defects')->default(0);
            $table->string('inspector')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Komunikasi: pemangku kepentingan, rencana komunikasi, laporan status
        Schema::create('stakeholders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('position')->nullable();
            $table->string('organization')->nullable();
            $table->string('category', 20)->default('internal');
            $table->unsignedTinyInteger('power')->default(3);
            $table->unsignedTinyInteger('interest')->default(3);
            $table->string('attitude', 15)->default('netral');
            $table->text('engagement_strategy')->nullable();
            $table->timestamps();
        });

        Schema::create('communication_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('comm_type', 20)->default('rapat');
            $table->string('audience')->nullable();
            $table->string('frequency', 20)->default('bulanan');
            $table->string('channel')->nullable();
            $table->string('owner')->nullable();
            $table->date('next_date')->nullable();
            $table->timestamps();
        });

        Schema::create('status_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->date('period_date');
            $table->string('overall_status', 10)->default('hijau');
            $table->string('schedule_status', 10)->default('hijau');
            $table->string('cost_status', 10)->default('hijau');
            $table->string('scope_status', 10)->default('hijau');
            $table->decimal('planned_percent', 5, 2)->default(0);
            $table->decimal('actual_percent', 5, 2)->default(0);
            $table->decimal('pv', 16, 2)->default(0);
            $table->decimal('ev', 16, 2)->default(0);
            $table->decimal('ac', 16, 2)->default(0);
            $table->text('summary')->nullable();
            $table->text('achievements')->nullable();
            $table->text('next_steps')->nullable();
            $table->string('prepared_by')->nullable();
            $table->timestamps();
            $table->unique(['project_id', 'period_date']);
        });

        // Pengadaan
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('category')->nullable();
            $table->string('country')->default('Indonesia');
            $table->unsignedTinyInteger('rating')->default(3);
            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('number', 40);
            $table->string('title');
            $table->string('contract_type', 20)->default('lump_sum');
            $table->string('procurement_method', 20)->default('tender_terbuka');
            $table->decimal('value', 16, 2)->default(0);
            $table->decimal('paid_amount', 16, 2)->default(0);
            $table->unsignedTinyInteger('progress')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status', 15)->default('perencanaan');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['contracts', 'vendors', 'status_reports', 'communication_plans', 'stakeholders', 'quality_checks',
            'lessons_learned', 'change_requests', 'issues', 'risks', 'timesheets', 'assignments', 'task_dependencies', 'wbs_items',
            'resources', 'projects', 'portfolios'] as $t) {
            Schema::dropIfExists($t);
        }
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['job_title', 'role', 'is_active', 'last_login_at']));
    }
};
