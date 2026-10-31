<?php

namespace Tests\Feature;

use App\Models\WbsItem;
use Tests\TestCase;

class EarnedValueTest extends TestCase
{
    public function test_evm_dihitung_dari_paket_kerja_pada_tanggal_status(): void
    {
        $p = $this->project();
        $e = $p->evm();

        // PV 19 Feb: 1.1 penuh (200rb) + 1.2 penuh (200rb) + 2.1 10/30 hari (100rb)
        $this->assertEqualsWithDelta(1000000, $e['bac'], 0.01);
        $this->assertEqualsWithDelta(500000, $e['pv'], 50);
        $this->assertEqualsWithDelta(300000, $e['ev'], 0.01);
        $this->assertEqualsWithDelta(330000, $e['ac'], 0.01);
        $this->assertEqualsWithDelta(0.6, $e['spi'], 0.001);
        $this->assertEqualsWithDelta(300000 / 330000, $e['cpi'], 0.0001);
        $this->assertEqualsWithDelta(1100000, $e['eac'], 0.5);
        $this->assertEqualsWithDelta(-200000, $e['sv'], 50);
        $this->assertSame('merah', $p->health());
    }

    public function test_level_satu_merangkum_anaknya(): void
    {
        $p = $this->project();
        $top = WbsItem::where('project_id', $p->id)->where('code', '1')->first();

        $this->assertSame(1, $top->level);
        $this->assertEquals(400000, $top->planned_cost);
        $this->assertEquals(330000, $top->actual_cost);
        $this->assertEquals(75.0, $top->percent_complete); // (200rb×100 + 200rb×50) / 400rb
        $this->assertSame('2026-01-01', $top->start_date->toDateString());
        $this->assertSame('2026-02-09', $top->finish_date->toDateString());
        $this->assertSame(2, WbsItem::where('project_id', $p->id)->where('code', '1.2')->value('level'));
    }

    public function test_baseline_terisi_otomatis_dan_keterlambatan_terhitung(): void
    {
        $p = $this->project();
        $item = WbsItem::where('project_id', $p->id)->where('code', '2.1')->first();
        $this->assertSame('2026-03-11', $item->baseline_finish->toDateString());

        $item->update(['finish_date' => '2026-03-21']);
        $this->assertSame(10, $item->fresh()->slip_days);
    }
}
