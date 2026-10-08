<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => Role::Admin]);
    }

    public function test_admin_can_view_staff_list(): void
    {
        $admin = $this->admin();
        User::factory()->create(['name' => 'Rina CS', 'email' => 'rina@tokokita.com', 'role' => Role::CustomerService]);
        User::factory()->create(['name' => 'Joko Gudang', 'email' => 'joko@tokokita.com', 'role' => Role::Gudang]);

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertSee('Manajemen Staf');
        $response->assertSee('Rina CS');
        $response->assertSee('rina@tokokita.com');
        $response->assertSee('Joko Gudang');
        $this->assertCount(3, $response->viewData('users'));
    }

    public function test_admin_can_create_staff_user(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Budi Santoso',
            'email' => 'budi@tokokita.com',
            'role' => Role::Gudang->value,
            'password' => 'rahasia123',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $created = User::where('email', 'budi@tokokita.com')->first();
        $this->assertNotNull($created);
        $this->assertSame('Budi Santoso', $created->name);
        $this->assertSame(Role::Gudang, $created->role);
        $this->assertTrue(Hash::check('rahasia123', $created->password));
    }

    public function test_create_staff_requires_unique_email(): void
    {
        $admin = $this->admin();
        User::factory()->create(['email' => 'sama@tokokita.com']);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Duplikat',
            'email' => 'sama@tokokita.com',
            'role' => Role::CustomerService->value,
            'password' => 'rahasia123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertSame(1, User::where('email', 'sama@tokokita.com')->count());
    }

    public function test_create_staff_validates_role_and_password(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Peran Aneh',
            'email' => 'aneh@tokokita.com',
            'role' => 'Superuser',
            'password' => 'rahasia123',
        ]);
        $response->assertSessionHasErrors('role');

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Tanpa Password',
            'email' => 'nopass@tokokita.com',
            'role' => Role::CustomerService->value,
        ]);
        $response->assertSessionHasErrors('password');
    }

    public function test_admin_can_update_staff_without_changing_password(): void
    {
        $admin = $this->admin();
        $staff = User::factory()->create([
            'name' => 'Nama Lama',
            'email' => 'lama@tokokita.com',
            'role' => Role::CustomerService,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.users.update', $staff), [
            'name' => 'Nama Baru',
            'email' => 'baru@tokokita.com',
            'role' => Role::Manajemen->value,
            'password' => '',
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $staff->refresh();
        $this->assertSame('Nama Baru', $staff->name);
        $this->assertSame('baru@tokokita.com', $staff->email);
        $this->assertSame(Role::Manajemen, $staff->role);
        // Password lama tetap berlaku karena kolom password dikosongkan.
        $this->assertTrue(Hash::check('password', $staff->password));
    }

    public function test_admin_can_update_staff_password_when_filled(): void
    {
        $admin = $this->admin();
        $staff = User::factory()->create(['role' => Role::Gudang]);

        $this->actingAs($admin)->put(route('admin.users.update', $staff), [
            'name' => $staff->name,
            'email' => $staff->email,
            'role' => Role::Gudang->value,
            'password' => 'password-baru-123',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertTrue(Hash::check('password-baru-123', $staff->fresh()->password));
    }

    public function test_update_staff_email_must_stay_unique(): void
    {
        $admin = $this->admin();
        User::factory()->create(['email' => 'sudahdipakai@tokokita.com']);
        $staff = User::factory()->create(['email' => 'staff@tokokita.com', 'role' => Role::CustomerService]);

        $response = $this->actingAs($admin)->put(route('admin.users.update', $staff), [
            'name' => $staff->name,
            'email' => 'sudahdipakai@tokokita.com',
            'role' => Role::CustomerService->value,
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = $this->admin();

        $response = $this->from(route('admin.users.index'))
            ->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_can_delete_other_staff(): void
    {
        $admin = $this->admin();
        $staff = User::factory()->create(['role' => Role::CustomerService]);

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $staff));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $staff->id]);
    }
}