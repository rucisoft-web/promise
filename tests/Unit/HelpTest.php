<?php

namespace Tests\Unit;

use App\Support\Help;
use PHPUnit\Framework\TestCase;

class HelpTest extends TestCase
{
    public function test_kunci_bantuan_dari_alamat(): void
    {
        $this->assertSame('dasbor', Help::keyForPath('admin'));
        $this->assertSame('risiko', Help::keyForPath('admin/risiko'));
        $this->assertSame('proyek', Help::keyForPath('admin/proyek'));
        $this->assertSame('proyek-form', Help::keyForPath('admin/proyek/create'));
        $this->assertSame('proyek-form', Help::keyForPath('admin/proyek/5/edit'));
        $this->assertSame('ruang-proyek', Help::keyForPath('admin/proyek/5'));
        $this->assertSame('umum', Help::keyForPath('admin/profile'));
    }
}
