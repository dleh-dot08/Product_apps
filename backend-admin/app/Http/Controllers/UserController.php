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
        $status = $request->input('status', 'all');
        $limit = $request->input('limit', 10);
        
        $query = User::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%")
                  ->orWhere('username', 'ilike', "%{$search}%");
            });
        }

        if ($status !== 'all') {
            $query->where('active', $status === 'active');
        }

        // Limit allowed values to prevent errors
        $allowedLimits = [10, 20, 30, 'all'];
        if (!in_array($limit, $allowedLimits)) {
            $limit = 10;
        }

        if ($limit === 'all') {
            $users = $query->latest()->get(); // Getting all records if 'all' is selected
            // Create a manual paginator-like object to keep the view compatible if it uses links()
            $users = new \Illuminate\Pagination\LengthAwarePaginator($users, $users->count(), max(1, $users->count()));
        } else {
            $users = $query->latest()->paginate($limit)->withQueryString();
        }

        $divisions = \App\Models\Division::all();
        $roles = \App\Models\Role::all();

        // Calculate KPI Data
        $totalUsers = User::count();
        $activeDivisions = \App\Models\Division::count();
        $totalRoles = \App\Models\Role::count();

        // Fetch Modules for Privileges Matrix (Do not paginate to prevent sync issues)
        $modules = \App\Models\Module::all();

        return view('users.index', compact('users', 'divisions', 'roles', 'search', 'status', 'limit', 'totalUsers', 'activeDivisions', 'totalRoles', 'modules'));
    }

    public function updatePrivileges(Request $request)
    {
        $privileges = $request->input('privileges', []);

        $roles = \App\Models\Role::all();
        
        foreach ($roles as $role) {
            $roleModulesData = [];
            
            if (isset($privileges[$role->id])) {
                foreach ($privileges[$role->id] as $moduleId => $permissions) {
                    // Checkboxes will send an array like ["View Dashboard" => "1", "Create Data" => "1"]
                    // We only want the keys that were checked.
                    $granted = array_keys(array_filter($permissions, function($val) {
                        return $val == '1' || $val === true || $val === 'on';
                    }));

                    $roleModulesData[$moduleId] = [
                        'granted_permissions' => json_encode($granted)
                    ];
                }
            }
            
            $role->modules()->sync($roleModulesData);
        }

        return redirect()->back()->with('success', 'Matriks Hak Akses berhasil diperbarui.');
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
