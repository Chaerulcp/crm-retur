<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Models\Product;
use App\Models\ReturnTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => Role::Admin]);
    }

    public function test_admin_can_view_product_list(): void
    {
        $admin = $this->admin();
        Product::factory()->create(['name' => 'Blender Turbo', 'sku' => 'SKU-BT01']);
        Product::factory()->create(['name' => 'Rice Cooker Mini', 'sku' => 'SKU-RC02']);

        $response = $this->actingAs($admin)->get(route('admin.products.index'));

        $response->assertOk();
        $response->assertSee('Manajemen Produk');
        $response->assertSee('Blender Turbo');
        $response->assertSee('SKU-BT01');
        $response->assertSee('Rice Cooker Mini');
    }

    public function test_admin_can_create_active_product(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Blender Turbo',
            'sku' => 'SKU-BT01',
            'description' => 'Blender berkecepatan tinggi',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $product = Product::where('sku', 'SKU-BT01')->first();
        $this->assertNotNull($product);
        $this->assertSame('Blender Turbo', $product->name);
        $this->assertSame('Blender berkecepatan tinggi', $product->description);
        $this->assertTrue($product->is_active);
    }

    public function test_admin_can_create_inactive_product(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Produk Nonaktif',
            'sku' => 'SKU-NA01',
            'is_active' => '0',
        ])->assertRedirect(route('admin.products.index'));

        $this->assertFalse(Product::where('sku', 'SKU-NA01')->first()->is_active);
    }

    public function test_store_product_requires_unique_sku(): void
    {
        $admin = $this->admin();
        Product::factory()->create(['sku' => 'SKU-0001']);

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Produk Duplikat',
            'sku' => 'SKU-0001',
        ]);

        $response->assertSessionHasErrors('sku');
        $this->assertSame(1, Product::where('sku', 'SKU-0001')->count());
    }

    public function test_admin_can_update_product_and_toggle_active_status(): void
    {
        $admin = $this->admin();
        $product = Product::factory()->create([
            'name' => 'Nama Lama',
            'sku' => 'SKU-LAMA',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.products.update', $product), [
            'name' => 'Nama Baru',
            'sku' => 'SKU-BARU',
            'description' => 'Deskripsi anyar',
            'is_active' => '0',
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $product->refresh();
        $this->assertSame('Nama Baru', $product->name);
        $this->assertSame('SKU-BARU', $product->sku);
        $this->assertSame('Deskripsi anyar', $product->description);
        $this->assertFalse($product->is_active);
    }

    public function test_update_product_keeps_sku_unique(): void
    {
        $admin = $this->admin();
        Product::factory()->create(['sku' => 'SKU-MILIK-LAIN']);
        $product = Product::factory()->create(['sku' => 'SKU-PUNYA-SAYA']);

        $response = $this->actingAs($admin)->put(route('admin.products.update', $product), [
            'name' => $product->name,
            'sku' => 'SKU-MILIK-LAIN',
        ]);

        $response->assertSessionHasErrors('sku');
    }

    public function test_admin_can_delete_product_without_tickets(): void
    {
        $admin = $this->admin();
        $product = Product::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.products.destroy', $product));

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_product_with_return_tickets_cannot_be_deleted(): void
    {
        $admin = $this->admin();
        $product = Product::factory()->create(['name' => 'Produk Laris']);
        ReturnTicket::factory()->create(['product_id' => $product->id]);

        $response = $this->from(route('admin.products.index'))
            ->actingAs($admin)
            ->delete(route('admin.products.destroy', $product));

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }
}