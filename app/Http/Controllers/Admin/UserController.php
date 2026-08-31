<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Tabel daftar staf.
     */
    public function index(): View
    {
        $users = User::query()->orderBy('name')->paginate(15);

        return view('admin.users.index', ['users' => $users]);
    }

    /**
     * Formulir tambah staf.
     */
    public function create(): View
    {
        return view('admin.users.create', [
            'user' => new User,
            'roles' => Role::values(),
        ]);
    }

    /**
     * Simpan staf baru.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        User::create($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Staf {$data['name']} berhasil ditambahkan.");
    }

    /**
     * Formulir ubah data staf.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => Role::values(),
        ]);
    }

    /**
     * Perbarui data staf. Password hanya diubah jika kolom diisi.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Data staf {$user->name} berhasil diperbarui.");
    }

    /**
     * Hapus staf. Admin tidak boleh menghapus akunnya sendiri.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Staf {$user->name} berhasil dihapus.");
    }
}