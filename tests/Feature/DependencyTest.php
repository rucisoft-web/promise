<?php

namespace Tests\Feature;

use App\Models\TaskDependency;
use App\Models\WbsItem;
use App\Support\GanttChart;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DependencyTest extends TestCase
{
    private function dep(string $pred, string $succ, string $type, int $lag = 0): TaskDependency
    {
        $p = WbsItem::where('code', $pred)->first();
        $s = WbsItem::where('code', $succ)->first();

        return TaskDependency::create(['predecessor_id' => $p->id, 'successor_id' => $s->id, 'type' => $type, 'lag_days' => $lag]);
    }

    public function test_empat_jenis_keterkaitan_dan_lag(): void
    {
        $this->project();

        // 1.1 selesai 20 Jan, 1.2 mulai 21 Jan
        $this->assertTrue($this->dep('1.1', '1.2', 'FS')->isSatisfied());
        $this->assertFalse($this->dep('1.2', '2.1', 'FS', 5)->isSatisfied()); // 2.1 mulai 10 Feb < 9 Feb + 5
        $this->assertTrue($this->dep('1.1', '2.1', 'SS', 30)->isSatisfied()); // 10 Feb ≥ 1 Jan + 30
        $this->assertFalse($this->dep('2.2', '2.1', 'FF')->isSatisfied());    // 11 Mar < 10 Apr
        $this->assertTrue($this->dep('2.1', '2.2', 'SF')->isSatisfied());     // selesai 2.2 ≥ mulai 2.1
        $this->assertSame('FS+5h', TaskDependency::where('lag_days', 5)->first()->label);
    }

    public function test_tugas_tidak_boleh_bergantung_pada_dirinya(): void
    {
        $this->project();
        $this->expectException(ValidationException::class);
        $this->dep('1.1', '1.1', 'FS');
    }

    public function test_gantt_menggambar_panah_untuk_setiap_jenis(): void
    {
        $p = $this->project();
        foreach ([['1.1', '1.2', 'FS'], ['1.1', '2.1', 'SS'], ['1.2', '2.1', 'FF'], ['2.1', '2.2', 'SF']] as [$a, $b, $t]) {
            $this->dep($a, $b, $t);
        }
        $g = GanttChart::build($p);

        $this->assertCount(6, $g['rows']);
        $this->assertSame(['FS', 'SS', 'FF', 'SF'], array_column($g['arrows'], 'type'));
        $this->assertStringStartsWith('M', $g['arrows'][0]['path']);
    }
}
