<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use App\Helpers\logActivity;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $user->assignRole($request->role);

        // Log
        logActivity::add('user', 'created', $user, 'User dibuat',
            ['new' => [
                'role' => $request->role,
                'name' => $request->name,
                'email' => $request->email,
            ]
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->first();

        return view('admin.users.edit', compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'exists:roles,name'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $input = $request->only(['name', 'email']);
        $input['role'] = $request->role;

        if (!empty($request->password)) {
            $input['password'] = Hash::make($request->password);
        }

        $user->update($input);
        $user->syncRoles([$request->role]);

        // Log
        logActivity::add('user', 'updated', $user, 'User updated', [
            'new' => [
                'role' => $request->role,
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ],
            'old' => [
                'role' => $user->role,
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
            ]
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting self
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete yourself.');
        }

        $user->delete();

        // Log
        logActivity::add('user', 'deleted', $user, 'User deleted', [
            'old' => [
                'role' => $user->role,
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
            ]
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully');
    }
}
