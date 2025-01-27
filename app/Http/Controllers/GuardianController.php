<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class GuardianController extends Controller
{
    /**
     * Display a listing of the guardians.
     */
    public function index()
    {
        $guardians = Guardian::where('tenant_id', auth()->user()->tenant_id)
            ->with('students')
            ->get();

        return response()->json($guardians);
    }

    /**
     * Store a newly created guardian.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:guardians,email|unique:users,email',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'student_ids' => 'nullable|array', // Daftar ID siswa
            'student_ids.*' => 'exists:students,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        // Buat akun di tabel users
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make('password'), // Password default
            'role' => 'guardian', // Role untuk orang tua
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        // Buat data guardian
        $guardian = Guardian::create([
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'user_id' => $user->id,
        ]);

        // Hubungkan dengan siswa jika ada
        if (!empty($validated['student_ids'])) {
            $guardian->students()->sync($validated['student_ids']);
        }

        return response()->json($guardian->load('students'), 201);
    }

    /**
     * Update the specified guardian.
     */
    public function update(Request $request, Guardian $guardian)
    {
        if ($guardian->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:guardians,email,' . $guardian->id . '|unique:users,email,' . $guardian->user_id,
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        // Update akun user
        $guardian->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Update data guardian
        $guardian->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
        ]);

        // Update relasi dengan siswa
        if (!empty($validated['student_ids'])) {
            $guardian->students()->sync($validated['student_ids']);
        }

        return response()->json($guardian->load('students'));
    }

    /**
     * Remove the specified guardian.
     */
    public function destroy(Guardian $guardian)
    {
        if ($guardian->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Hapus akun user
        $guardian->user->delete();

        // Hapus data guardian
        $guardian->delete();

        return response()->json(['message' => 'Guardian deleted successfully']);
    }
}
