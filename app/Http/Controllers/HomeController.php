<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Beranda untuk pengguna yang belum punya akses panel admin.
     */
    public function __invoke(): View
    {
        return view('home');
    }
}
