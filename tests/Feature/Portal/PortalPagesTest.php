<?php

namespace Tests\Feature\Portal;

use App\Models\Faq;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_utama_portal_dapat_diakses(): void
    {
        $this->get(route('portal.home'))
            ->assertOk()
            ->assertSee('Portal Retur Pelanggan')
            ->assertSee('Ajukan Retur')
            ->assertSee('Lacak Retur')
            ->assertSee('Login Staf');
    }

    public function test_halaman_faq_menampilkan_daftar_pertanyaan(): void
    {
        $faqs = Faq::factory()->count(3)->create();

        $response = $this->get(route('portal.faq'));

        $response->assertOk()->assertSee('Pertanyaan Umum');

        foreach ($faqs as $faq) {
            $response->assertSee($faq->question);
        }
    }
}
