<?php

namespace Tests\Unit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

/**
 * Helper kataKunci() dipakai semua halaman daftar sebelum masuk ke when(),
 * jadi perilakunya diuji tersendiri tanpa database.
 */
class KataKunciTest extends TestCase
{
    /**
     * Controller anonim untuk membuka helper protected kataKunci().
     */
    private function ambilKataKunci(array $query, string $kunci = 'q'): string
    {
        $pengendali = new class () extends Controller {
            public function ambil(Request $request, string $kunci): string
            {
                return $this->kataKunci($request, $kunci);
            }
        };

        return $pengendali->ambil(Request::create('/', 'GET', $query), $kunci);
    }

    public function test_spasi_di_pinggir_kata_kunci_dibuang(): void
    {
        $this->assertSame('budi santoso', $this->ambilKataKunci(['q' => '  budi santoso  ']));
    }

    public function test_parameter_yang_tidak_ada_dianggap_kosong(): void
    {
        $this->assertSame('', $this->ambilKataKunci([]));
    }

    public function test_nilai_array_dianggap_kosong(): void
    {
        $this->assertSame('', $this->ambilKataKunci(['q' => ['a', 'b']]));
    }

    public function test_nama_parameter_bisa_diganti(): void
    {
        $this->assertSame('if', $this->ambilKataKunci(['cari' => 'if'], 'cari'));
    }
}
