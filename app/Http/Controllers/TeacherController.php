<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


class TeacherController extends Controller
{
    /**
     * Display a listing of the teachers.
     */
    public function index()
    {
        $teachers = Teacher::where('tenant_id', auth()->user()->tenant_id)->with('user')->get();

        return response()->json($teachers);
    }

    /**
     * Store a newly created teacher.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:teachers,email|unique:users,email',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|max:2048', // Validasi file foto
        ]);

        // Simpan foto profil jika ada
        $profilePhotoPath = null;
        if ($request->hasFile('profile_photo')) {
            $profilePhotoPath = $request->file('profile_photo')->store('profile_photos', 'public');
        }

        // Buat akun di tabel users
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make('password'), // Password default
            'role' => 'teacher', // Role untuk guru
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        // Buat data guru
        $teacher = Teacher::create([
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'profile_photo' => $profilePhotoPath, // Simpan path foto
            'user_id' => $user->id,
        ]);

        return response()->json($teacher, 201);
    }

    /**
     * Display the specified teacher.
     */
    public function show(Teacher $teacher)
    {
        if ($teacher->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($teacher->load('user'));
    }

    /**
     * Update the specified teacher.
     */
    public function update(Request $request, Teacher $teacher)
    {
        if ($teacher->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:teachers,email,' . $teacher->id . '|unique:users,email,' . $teacher->user_id,
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|max:2048', // Validasi file foto
        ]);

        // Update foto profil jika ada
        if ($request->hasFile('profile_photo')) {
            // Hapus foto lama jika ada
            if ($teacher->profile_photo) {
                Storage::disk('public')->delete($teacher->profile_photo);
            }

            // Simpan foto baru
            $teacher->profile_photo = $request->file('profile_photo')->store('profile_photos', 'public');
        }

        // Update akun user
        $teacher->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Update data guru
        $teacher->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
        ]);

        return response()->json($teacher);
    }

    /**
     * Remove the specified teacher.
     */
    public function destroy(Teacher $teacher)
    {
        if ($teacher->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Hapus foto profil jika ada
        if ($teacher->profile_photo) {
            Storage::disk('public')->delete($teacher->profile_photo);
        }

        // Hapus akun user
        $teacher->user->delete();

        // Hapus data guru
        $teacher->delete();

        return response()->json(['message' => 'Teacher deleted successfully']);
    }
}
