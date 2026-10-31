<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Risk;
use App\Support\GuestMode;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AccessTest extends TestCase
{
    public function test_manajer_hanya_mengubah_proyeknya(): void
    {
        $pm = $this->user('manajer');
        $other = $this->user('manajer');
        $p = $this->project(['manager_id' => $pm->id]);

        $this->assertTrue($pm->canEditProject($p));
        $this->assertFalse($other->canEditProject($p));
        $this->assertTrue($this->user('pmo')->canEditProject($p));
        $this->assertFalse($this->user('eksekutif')->canEditProject($p));
        $this->assertFalse($this->user('anggota')->canEditProjects());
    }

    public function test_landing_dan_tombol_masuk_sebagai_tamu(): void
    {
        config(['promise.guest_login' => true]);
        $this->project();

        $this->get('/')->assertOk()->assertSee('Masuk sebagai Tamu');
        $this->get('/admin/login')->assertOk()->assertSee('Masuk sebagai Tamu');
        $this->get('/masuk-tamu')->assertRedirect('/admin');
        $this->assertTrue(GuestMode::isGuest(auth()->user()));
        $this->get('/admin')->assertOk()->assertSee('Dasbor Portofolio Proyek');
    }

    public function test_tamu_tidak_bisa_mengubah_data(): void
    {
        config(['promise.guest_login' => true]);
        $p = $this->project();
        $this->get('/masuk-tamu');

        $this->expectExceptionMessage(GuestMode::MESSAGE);
        Risk::create(['project_id' => $p->id, 'code' => 'R-1', 'title' => 'Uji']);
    }

    public function test_login_tamu_mati_bila_tidak_diaktifkan(): void
    {
        config(['promise.guest_login' => false]);
        $this->get('/masuk-tamu')->assertNotFound();
        $this->get('/')->assertRedirect('/admin');
    }

    public function test_lampiran_hanya_untuk_pengguna_yang_masuk(): void
    {
        Storage::fake(Document::DISK);
        $p = $this->project();
        Storage::disk(Document::DISK)->put('dokumen/uji.pdf', '%PDF-1.4 uji');
        $doc = new Document(['title' => 'Gambar teknik', 'file_path' => 'dokumen/uji.pdf', 'original_name' => 'gambar.pdf']);
        $doc->attachable()->associate($p->wbsItems()->where('code', '1.1')->first());
        $doc->save();

        $this->assertSame($p->id, $doc->project_id);
        $this->assertSame('Tugas WBS 1.1 Desain dasar', $doc->attachable_label);
        $this->assertSame('pdf', $doc->extension);

        $this->get($doc->url())->assertRedirect();
        $this->actingAs($this->user('anggota'))->get($doc->url())->assertOk()->assertDownload('gambar.pdf');

        $doc->delete();
        Storage::disk(Document::DISK)->assertMissing('dokumen/uji.pdf');
    }
}
