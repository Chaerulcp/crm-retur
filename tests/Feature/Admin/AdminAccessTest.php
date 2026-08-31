<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(Role $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    public function test_guest_is_redirected_to_login_on_admin_pages(): void
    {
        $this->get(route('admin.users.index'))->assertRedirect(route('login'));
        $this->get(route('admin.products.index'))->assertRedirect(route('login'));
        $this->get(route('admin.faqs.index'))->assertRedirect(route('login'));
        $this->get(route('admin.analytics'))->assertRedirect(route('login'));
    }

    public function test_non_admin_roles_are_blocked_from_user_management(): void
    {
        foreach ([Role::CustomerService, Role::Gudang, Role::Manajemen] as $role) {
            $user = $this->makeUser($role);

            $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
            $this->actingAs($user)->get(route('admin.users.create'))->assertForbidden();
            $this->actingAs($user)->post(route('admin.users.store'), [])->assertForbidden();
        }
    }

    public function test_non_admin_roles_are_blocked_from_product_management(): void
    {
        foreach ([Role::CustomerService, Role::Gudang, Role::Manajemen] as $role) {
            $user = $this->makeUser($role);

            $this->actingAs($user)->get(route('admin.products.index'))->assertForbidden();
            $this->actingAs($user)->get(route('admin.products.create'))->assertForbidden();
            $this->actingAs($user)->post(route('admin.products.store'), [])->assertForbidden();
        }
    }

    public function test_gudang_is_blocked_from_faq_and_analytics(): void
    {
        $user = $this->makeUser(Role::Gudang);

        $this->actingAs($user)->get(route('admin.faqs.index'))->assertForbidden();
        $this->actingAs($user)->post(route('admin.faqs.store'), [])->assertForbidden();
        $this->actingAs($user)->get(route('admin.analytics'))->assertForbidden();
    }

    public function test_customer_service_can_manage_faq_but_not_analytics(): void
    {
        // Paritas dengan sistem lawas: CS ikut mengelola FAQ, tanpa akses analitik.
        $user = $this->makeUser(Role::CustomerService);

        $this->actingAs($user)->get(route('admin.faqs.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.faqs.create'))->assertOk();
        $this->actingAs($user)->get(route('admin.analytics'))->assertForbidden();
    }

    public function test_admin_can_access_all_admin_pages(): void
    {
        $admin = $this->makeUser(Role::Admin);

        $this->actingAs($admin)->get(route('admin.users.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.users.create'))->assertOk();
        $this->actingAs($admin)->get(route('admin.products.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.products.create'))->assertOk();
        $this->actingAs($admin)->get(route('admin.faqs.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.analytics'))->assertOk();
    }

    public function test_manajemen_can_access_faq_and_analytics_but_not_users_or_products(): void
    {
        $manajemen = $this->makeUser(Role::Manajemen);

        $this->actingAs($manajemen)->get(route('admin.faqs.index'))->assertOk();
        $this->actingAs($manajemen)->get(route('admin.faqs.create'))->assertOk();
        $this->actingAs($manajemen)->get(route('admin.analytics'))->assertOk();
        $this->actingAs($manajemen)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($manajemen)->get(route('admin.products.index'))->assertForbidden();
    }
}