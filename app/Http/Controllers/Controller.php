<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

/**
 * Controller dasar.
 *
 * Dibuat turunan Illuminate\Routing\Controller (seperti Laravel 10) supaya
 * middleware bisa dipasang lewat constructor, misalnya:
 * $this->middleware('permission:hapus-tugas')->only('destroy');
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /**
     * Ambil kata kunci pencarian dari query string dengan aman.
     * Nilai non-string (mis. ?q[]=x) dianggap kosong.
     */
    protected function kataKunci(Request $request, string $kunci = 'q'): string
    {
        $nilai = $request->query($kunci);

        return is_string($nilai) ? trim($nilai) : '';
    }
}
