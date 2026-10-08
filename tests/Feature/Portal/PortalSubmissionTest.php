<?php

namespace Tests\Feature\Portal;

use App\Enums\TicketStatus;
use App\Mail\TicketSubmittedMail;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ReturnTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PortalSubmissionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function validPayload(Product $product, array $overrides = []): array
    {
        return array_merge([
            'customer_name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '081234567890',
            'product_id' => $product->id,
            'invoice_number' => 'INV-12345',
            'reason' => 'Barang yang saya terima rusak pada bagian layar.',
            'refund_method' => 'Transfer Bank',
        ], $overrides);
    }

    public function test_halaman_pengajuan_dapat_diakses(): void
    {
        $product = Product::factory()->create(['name' => 'Sepatu Lari Kenari']);

        $this->get(route('portal.create'))
            ->assertOk()
            ->assertSee('Ajukan Retur')
            ->assertSee('Sepatu Lari Kenari');
    }

    public function test_pengajuan_sukses_membuat_tiket_dengan_nomor_dan_email_diantrekan(): void
    {
        Mail::fake();
        Storage::fake('public');

        $product = Product::factory()->create();

        $response = $this->post(route('portal.store'), $this->validPayload($product, [
            'evidences' => [
                UploadedFile::fake()->create('foto-kerusakan.jpg', 500, 'image/jpeg'),
                UploadedFile::fake()->create('video-unboxing.mp4', 2000, 'video/mp4'),
            ],
        ]));

        $response->assertRedirect(route('portal.success'));

        $ticket = ReturnTicket::query()->first();
        $this->assertNotNull($ticket);
        $this->assertMatchesRegularExpression('/^RET-\d{8}-\d{4}$/', $ticket->ticket_number);
        $this->assertSame(TicketStatus::Diajukan, $ticket->status);
        $this->assertNotEmpty($ticket->tracking_token);

        $customer = Customer::query()->where('email', 'budi@example.com')->first();
        $this->assertNotNull($customer);
        $this->assertSame($customer->id, $ticket->customer_id);

        // Bukti tersimpan di disk public pada folder evidences/{nomor tiket}.
        $this->assertSame(2, $ticket->evidences()->count());
        $this->assertSame(1, $ticket->evidences()->where('kind', 'image')->count());
        $this->assertSame(1, $ticket->evidences()->where('kind', 'video')->count());

        foreach ($ticket->evidences as $evidence) {
            $this->assertStringStartsWith("evidences/{$ticket->ticket_number}/", $evidence->path);
            Storage::disk('public')->assertExists($evidence->path);
        }

        Mail::assertQueued(
            TicketSubmittedMail::class,
            fn (TicketSubmittedMail $mail): bool => $mail->hasTo('budi@example.com'),
        );

        // Halaman sukses menampilkan nomor tiket.
        $this->get(route('portal.success'))
            ->assertOk()
            ->assertSee($ticket->ticket_number);
    }

    public function test_pelanggan_lama_digunakan_kembali_berdasarkan_email(): void
    {
        Mail::fake();

        $existing = Customer::factory()->create(['email' => 'budi@example.com']);
        $product = Product::factory()->create();

        $this->post(route('portal.store'), $this->validPayload($product))
            ->assertRedirect(route('portal.success'));

        $this->assertSame(1, Customer::query()->where('email', 'budi@example.com')->count());
        $this->assertSame($existing->id, ReturnTicket::query()->first()->customer_id);
    }

    public function test_field_wajib_divalidasi(): void
    {
        Mail::fake();

        $this->post(route('portal.store'), [])
            ->assertSessionHasErrors(['customer_name', 'email', 'product_id', 'reason']);

        $this->assertSame(0, ReturnTicket::query()->count());
        Mail::assertNothingQueued();
    }

    public function test_berkas_bukti_selain_gambar_atau_video_ditolak(): void
    {
        $product = Product::factory()->create();

        $this->post(route('portal.store'), $this->validPayload($product, [
            'evidences' => [UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf')],
        ]))->assertSessionHasErrors('evidences.0');

        $this->assertSame(0, ReturnTicket::query()->count());
    }

    public function test_gambar_melebihi_batas_ukuran_ditolak(): void
    {
        $product = Product::factory()->create();

        $this->post(route('portal.store'), $this->validPayload($product, [
            'evidences' => [UploadedFile::fake()->create('foto-besar.jpg', 6000, 'image/jpeg')],
        ]))->assertSessionHasErrors('evidences.0');

        $this->assertSame(0, ReturnTicket::query()->count());
    }

    public function test_halaman_sukses_menampilkan_tautan_lacak_dengan_token(): void
    {
        Mail::fake();

        $product = Product::factory()->create();

        $this->post(route('portal.store'), $this->validPayload($product))
            ->assertRedirect(route('portal.success'));

        $ticket = ReturnTicket::query()->first();

        $expectedUrl = route('portal.tracking.show', ['ticket_number' => $ticket->ticket_number])
            .'?token='.$ticket->tracking_token;

        $this->get(route('portal.success'))
            ->assertOk()
            ->assertSee($ticket->ticket_number)
            ->assertSee($expectedUrl, false);
    }

    public function test_halaman_sukses_tanpa_sesi_dialihkan_ke_form(): void
    {
        $this->get(route('portal.success'))->assertRedirect(route('portal.create'));
    }
}
