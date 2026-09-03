<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $search = $request->input('search');
        
        $users = User::when($search, function($query) use ($search) {
            $query->where('full_name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%")
                  ->orWhere('username', 'ilike', "%{$search}%");
        })->latest()->paginate(10)->withQueryString();

        $divisions = \App\Models\Division::all();
        $roles = \App\Models\Role::all();
        return view('users.index', compact('users', 'divisions', 'roles', 'search'));
    }

    public function create()
    {
        $divisions = \App\Models\Division::all();
        $roles = \App\Models\Role::all();
        return view('users.form', [
            'user' => new User(),
            'divisions' => $divisions,
            'roles' => $roles
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'division_id' => 'nullable|exists:divisions,id',
            'role_id' => 'required|exists:roles,id',
            'active' => 'nullable|boolean',
        ]);

        $validated['active'] = $request->has('active');
        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $divisions = \App\Models\Division::all();
        $roles = \App\Models\Role::all();
        return view('users.form', compact('user', 'divisions', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'division_id' => 'nullable|exists:divisions,id',
            'role_id' => 'required|exists:roles,id',
            'active' => 'nullable|boolean',
        ]);

        $validated['active'] = $request->has('active');

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')->with('error', 'Tidak bisa menghapus akun Anda sendiri.');
        }
        
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
