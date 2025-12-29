<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TicketLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use App\Helpers\logActivity;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'location'])->latest()->paginate(10);
        $roles = Role::pluck('name', 'name')->all();
        $locations = TicketLocation::where('is_active', true)->get();
        return view('admin.users.index', compact('users', 'roles', 'locations'));
    }

    // store
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
            'location_id' => ['nullable', 'exists:ticket_locations,id'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'location_id' => $request->location_id,
        ]);

        $user->assignRole($request->role);

        // Log
        logActivity::add(
            'user',
            'created',
            $user,
            'User dibuat',
            [
                'new' => [
                    'role' => $request->role,
                    'name' => $request->name,
                    'email' => $request->email,
                    'location_id' => $request->location_id,
                ]
            ]
        );

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User created successfully.'
            ], 200);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    // update
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'exists:roles,name'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'location_id' => ['nullable', 'exists:ticket_locations,id'],
        ]);

        $input = $request->only(['name', 'email']);
        $input['role'] = $request->role;
        $input['location_id'] = $request->location_id;

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
                'location_id' => $request->location_id,
            ],
            'old' => [
                'role' => $user->role,
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'location_id' => $user->location_id,
            ]
        ]);

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.'
            ], 200);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully');
    }

    // delete
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
                'location_id' => $user->location_id,
            ]
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully');
    }
}
