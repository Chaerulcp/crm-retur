<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Landing page portal: sambutan + CTA pengajuan dan pelacakan retur.
     */
    public function home(): View
    {
        return view('portal.home');
    }

    /**
     * Daftar pertanyaan umum (FAQ) dikelompokkan per kategori.
     */
    public function faq(): View
    {
        $faqs = Faq::query()
            ->orderBy('category')
            ->orderBy('question')
            ->get()
            ->groupBy('category');

        return view('portal.faq', ['faqs' => $faqs]);
    }
}
