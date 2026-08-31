<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Models\Faq;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaqManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => Role::Admin]);
    }

    public function test_admin_and_manajemen_can_view_faq_list(): void
    {
        Faq::factory()->create([
            'question' => 'Berapa lama proses refund?',
            'answer' => 'Maksimal 7 hari kerja.',
            'category' => 'Refund',
        ]);

        foreach ([Role::Admin, Role::Manajemen] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $response = $this->actingAs($user)->get(route('admin.faqs.index'));
            $response->assertOk();
            $response->assertSee('Berapa lama proses refund?');
        }
    }

    public function test_customer_service_can_view_and_create_faq(): void
    {
        // Paritas dengan sistem lawas: CS dapat mengelola FAQ.
        $cs = User::factory()->create(['role' => Role::CustomerService]);

        Faq::factory()->create(['question' => 'FAQ terlihat oleh CS?']);

        $this->actingAs($cs)->get(route('admin.faqs.index'))
            ->assertOk()
            ->assertSee('FAQ terlihat oleh CS?');

        $this->actingAs($cs)->post(route('admin.faqs.store'), [
            'question' => 'CS bisa membuat FAQ?',
            'answer' => 'Bisa, setara dengan sistem lawas.',
            'category' => 'Umum',
        ])->assertRedirect(route('admin.faqs.index'));

        $this->assertDatabaseHas('faqs', ['question' => 'CS bisa membuat FAQ?']);
    }

    public function test_admin_can_create_faq(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->post(route('admin.faqs.store'), [
            'question' => 'Bagaimana cara melacak retur?',
            'answer' => 'Gunakan nomor tiket di halaman lacak.',
            'category' => 'Umum',
        ]);

        $response->assertRedirect(route('admin.faqs.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('faqs', [
            'question' => 'Bagaimana cara melacak retur?',
            'category' => 'Umum',
        ]);
    }

    public function test_manajemen_can_create_faq(): void
    {
        $manajemen = User::factory()->create(['role' => Role::Manajemen]);

        $this->actingAs($manajemen)->post(route('admin.faqs.store'), [
            'question' => 'Apakah bisa refund ke e-wallet?',
            'answer' => 'Bisa, pilih metode e-wallet.',
            'category' => 'Refund',
        ])->assertRedirect(route('admin.faqs.index'));

        $this->assertDatabaseHas('faqs', ['question' => 'Apakah bisa refund ke e-wallet?']);
    }

    public function test_store_faq_requires_question_and_answer(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->post(route('admin.faqs.store'), [
            'question' => '',
            'answer' => '',
        ]);

        $response->assertSessionHasErrors(['question', 'answer']);
    }

    public function test_admin_can_update_faq(): void
    {
        $admin = $this->admin();
        $faq = Faq::factory()->create();

        $response = $this->actingAs($admin)->put(route('admin.faqs.update', $faq), [
            'question' => 'Pertanyaan diperbarui?',
            'answer' => 'Jawaban diperbarui.',
            'category' => 'Pengiriman',
        ]);

        $response->assertRedirect(route('admin.faqs.index'));

        $faq->refresh();
        $this->assertSame('Pertanyaan diperbarui?', $faq->question);
        $this->assertSame('Jawaban diperbarui.', $faq->answer);
        $this->assertSame('Pengiriman', $faq->category);
    }

    public function test_admin_can_delete_faq(): void
    {
        $admin = $this->admin();
        $faq = Faq::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.faqs.destroy', $faq));

        $response->assertRedirect(route('admin.faqs.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('faqs', ['id' => $faq->id]);
    }
}