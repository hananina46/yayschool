<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $users = User::where('tenant_id', auth()->user()->tenant_id)
            ->when($request->role, function ($query, $role) {
                $query->where('role', $role);
            })
            ->get();

        return response()->json($users);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'role' => 'required|in:admin,teacher,student,guardian',
            'password' => 'nullable|string|min:8', // Password default jika tidak disediakan
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'tenant_id' => auth()->user()->tenant_id,
            'password' => Hash::make($validated['password'] ?? 'password123'), // Default password
        ]);

        return response()->json($user, 201);
    }

    /**
     * Show the specified user.
     */
    public function show($id)
    {
        $user = User::where('tenant_id', auth()->user()->tenant_id)->find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        if ($user->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'nullable|in:admin,teacher,student,guardian',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $user->update($validated);

        return response()->json($user);
    }

    /**
     * Reset password for the user.
     */
    public function resetPassword(Request $request, $id)
    {
        $user = User::where('tenant_id', auth()->user()->tenant_id)->find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Password reset successfully']);
    }

    /**
     * Delete the specified user.
     */
    public function destroy(User $user)
    {
        if ($user->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
