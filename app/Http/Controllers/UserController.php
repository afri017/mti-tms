<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    /**
     * Tampilkan daftar user.
     */
    public function index()
    {
        // Ambil semua user dan role untuk dropdown
        $users = User::with('roles')->get();
        $roles = Role::all();

        return view('users.index', compact('users', 'roles'))->with('pageTitle', 'User Management');
    }

    /**
     * Simpan user baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role'     => 'nullable|string|exists:roles,name',
            'gambar'   => 'nullable|image|mimes:jpeg,jpg,png|max:2048', // max 2MB
        ]);

        // Handle upload gambar
        $gambarName = null;
        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar');
            $gambarName = time() . '_' . $gambar->getClientOriginalName();
            $gambar->move(public_path('dist/img'), $gambarName);
        }

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'gambar'   => $gambarName,
        ]);

        if (!empty($validated['role'])) {
            $user->assignRole($validated['role']);
        }

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Update user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role'     => 'nullable|string|exists:roles,name',
            'gambar'   => 'nullable|image|mimes:jpeg,jpg,png|max:2048', // max 2MB
        ]);

        $data = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        // Handle upload gambar baru
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($user->gambar && File::exists(public_path('dist/img/' . $user->gambar))) {
                File::delete(public_path('dist/img/' . $user->gambar));
            }

            // Upload gambar baru
            $gambar = $request->file('gambar');
            $gambarName = time() . '_' . $gambar->getClientOriginalName();
            $gambar->move(public_path('dist/img'), $gambarName);
            $data['gambar'] = $gambarName;
        }

        $user->update($data);

        // Update role user (jika ada)
        if (!empty($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Hapus user.
     */
    public function destroy(User $user)
    {
        // Hapus gambar jika ada
        if ($user->gambar && File::exists(public_path('dist/img/' . $user->gambar))) {
            File::delete(public_path('dist/img/' . $user->gambar));
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    /**
     * (Opsional) Tampilkan detail user.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }
}
