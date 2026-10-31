<?php

namespace Tests;

use App\Models\Project;
use App\Models\User;
use App\Models\WbsItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    public $mockConsoleOutput = false;

    protected function user(string $role = 'admin', ?string $email = null): User
    {
        return User::forceCreate([
            'name' => ucfirst($role).' '.Str::random(4), 'email' => $email ?? $role.'-'.Str::random(6).'@uji.local',
            'password' => 'Rahasia-12345', 'role' => $role, 'is_active' => true,
        ]);
    }

    /** Proyek 100 hari dengan dua deliverable dan empat paket kerja (anggaran total 1.000.000). */
    protected function project(array $attrs = []): Project
    {
        $p = Project::create(array_merge([
            'code' => 'UJI-'.Str::upper(Str::random(4)), 'name' => 'Proyek Uji', 'status' => 'aktif', 'phase' => 'eksekusi',
            'start_date' => '2026-01-01', 'finish_date' => '2026-04-10', 'status_date' => '2026-02-19', 'budget' => 1000000,
        ], $attrs));
        $a = WbsItem::create(['project_id' => $p->id, 'code' => '1', 'name' => 'Rekayasa', 'sort' => 1]);
        $b = WbsItem::create(['project_id' => $p->id, 'code' => '2', 'name' => 'Konstruksi', 'sort' => 2]);
        // 1.1: 1–20 Jan (selesai), 1.2: 21 Jan – 9 Feb (selesai 50%), 2.1: 10 Feb – 11 Mar, 2.2: 12 Mar – 10 Apr
        WbsItem::create(['project_id' => $p->id, 'parent_id' => $a->id, 'code' => '1.1', 'name' => 'Desain dasar', 'sort' => 3, 'start_date' => '2026-01-01', 'finish_date' => '2026-01-20', 'planned_cost' => 200000, 'actual_cost' => 220000, 'percent_complete' => 100]);
        WbsItem::create(['project_id' => $p->id, 'parent_id' => $a->id, 'code' => '1.2', 'name' => 'Desain rinci', 'sort' => 4, 'start_date' => '2026-01-21', 'finish_date' => '2026-02-09', 'planned_cost' => 200000, 'actual_cost' => 110000, 'percent_complete' => 50]);
        WbsItem::create(['project_id' => $p->id, 'parent_id' => $b->id, 'code' => '2.1', 'name' => 'Pondasi', 'sort' => 5, 'start_date' => '2026-02-10', 'finish_date' => '2026-03-11', 'planned_cost' => 300000, 'actual_cost' => 0, 'percent_complete' => 0]);
        WbsItem::create(['project_id' => $p->id, 'parent_id' => $b->id, 'code' => '2.2', 'name' => 'Struktur', 'sort' => 6, 'start_date' => '2026-03-12', 'finish_date' => '2026-04-10', 'planned_cost' => 300000, 'actual_cost' => 0, 'percent_complete' => 0]);

        return $p->fresh();
    }
}
