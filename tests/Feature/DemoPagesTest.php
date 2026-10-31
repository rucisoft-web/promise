<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Project;
use App\Models\TaskDependency;
use App\Models\WbsItem;
use App\Support\DemoData;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** Data demo MedcoEnergi termuat lengkap dan semua halaman panel terbuka tanpa galat. */
class DemoPagesTest extends TestCase
{
    public function test_data_demo_dan_semua_halaman(): void
    {
        Storage::fake(Document::DISK);
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(29, Project::count());
        $this->assertSame(12, Project::where('code', 'like', 'SMB-%')->count());
        $this->assertSame(0, WbsItem::where('level', '>', 2)->count());
        $this->assertGreaterThan(250, WbsItem::where('level', 2)->count());
        $this->assertEqualsCanonicalizing(['FS', 'SS', 'FF', 'SF'], TaskDependency::distinct()->pluck('type')->all());
        $this->assertGreaterThan(80, Document::count());
        $this->assertEqualsWithDelta(0.81, Project::where('code', 'THA-BL1')->first()->evm()['spi'], 0.03);

        config(['promise.guest_login' => true]);
        $this->get('/admin/login')->assertOk()->assertSee(DemoData::TITLE);
        $this->get('/')->assertOk()->assertSee(DemoData::TITLE)->assertSee('keperluan pembelajaran');
        $this->actingAs(\App\Models\User::where('role', 'admin')->first());
        $this->get('/admin/risiko')->assertSee('pm-help-btn', false)->assertSee('Register Risiko')->assertSee('Probabilitas &amp; Dampak', false);
        $this->get('/admin/panduan')->assertSee('Panduan per layar')->assertSee('Keterkaitan Tugas (FS/SS/FF/SF)');
        $this->get('/admin')->assertSee(DemoData::TITLE)->assertSee('hanya untuk keperluan pembelajaran');
        $pages = ['/admin', '/admin/proyek', '/admin/proyek/create', '/admin/perubahan', '/admin/portofolio', '/admin/pelajaran', '/admin/wbs',
            '/admin/jadwal', '/admin/keterkaitan-tugas', '/admin/earned-value', '/admin/mutu', '/admin/sumber-daya', '/admin/penugasan',
            '/admin/lembar-waktu', '/admin/kapasitas', '/admin/pemangku-kepentingan', '/admin/rencana-komunikasi', '/admin/laporan-status',
            '/admin/risiko', '/admin/isu', '/admin/kontrak', '/admin/vendor', '/admin/dokumen', '/admin/pengguna', '/admin/panduan'];
        foreach ($pages as $url) {
            $this->get($url)->assertOk();
        }
        $p = Project::where('code', 'SEN-SSD')->first();
        $this->get('/admin/proyek/'.$p->id)->assertOk()->assertSee('Gantt chart dengan keterkaitan antartugas')->assertSee('pm-g-dep', false);
        $this->get('/admin/proyek/'.$p->id.'/edit')->assertOk();
        $this->get('/admin/jadwal?proyek='.$p->id)->assertOk()->assertSee($p->name);

        // Data demo terpisah per perusahaan
        $this->assertSame(17, Project::where('company', 'medco')->count());
        $this->assertSame(12, Project::where('company', 'smbc')->count());
        auth()->logout();
        $this->get('/admin/login')->assertSee('Pilih data demo perusahaan')->assertSee('PT Bank SMBC Indonesia Tbk')->assertSee('perusahaan=medco', false);
        $this->get('/')->assertSee('perusahaan=smbc', false);
        $this->get('/masuk-tamu?perusahaan=smbc')->assertRedirect('/admin');
        $this->get('/admin/proyek')->assertOk()->assertSee('SMB-CBS')->assertDontSee('NTN-FRL')
            ->assertSee('pm-company', false)->assertSee('PT Bank SMBC Indonesia Tbk');
        foreach (['/admin', '/admin/risiko', '/admin/wbs', '/admin/jadwal', '/admin/earned-value', '/admin/sumber-daya', '/admin/vendor', '/admin/dokumen', '/admin/kapasitas'] as $url) {
            $this->get($url)->assertOk()->assertSee('PT Bank SMBC Indonesia Tbk');
        }
        $this->get('/admin/vendor')->assertDontSee('V-NRL');
        $this->get('/admin/proyek/'.$p->id)->assertNotFound();
        $this->from('/admin/risiko')->get('/perusahaan-demo/medco')->assertRedirect(url('/admin/risiko'));
        $this->get('/admin/proyek')->assertSee('NTN-FRL')->assertDontSee('SMB-CBS')->assertSee('PT Medco Energi Internasional Tbk');
        $this->get('/perusahaan-demo/semua');
        $this->get('/admin/proyek')->assertSee('NTN-FRL')->assertSee('SMB-CBS');
    }

    public function test_anggota_tidak_bisa_membuka_pengguna(): void
    {
        $this->actingAs($this->user('anggota'));
        $this->get('/admin/pengguna')->assertForbidden();
    }
}
