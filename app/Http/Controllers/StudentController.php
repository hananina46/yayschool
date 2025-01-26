<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
//validator
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    /**
     * Display a listing of the students.
     */
    public function index()
    {
        $students = Student::where('tenant_id', auth()->user()->tenant_id)->with('class', 'user')->get();

        return response()->json($students);
    }

    //show student by id
    public function show($id)
    {
        $student = Student::where('tenant_id', auth()->user()->tenant_id)->with('class', 'user')->find($id);

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        return response()->json($student);
    }

    /**
     * Store a newly created student.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:students,email|unique:users,email',
            'class_id' => 'nullable|exists:school_classes,id',
            'nisn' => 'nullable|string|unique:students,nisn',
            'dob' => 'nullable|date',
            'gender' => 'nullable|string',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|max:2048', // Validasi foto profil
        ]);

        // Simpan foto profil jika ada
        $profilePhotoPath = null;
        if ($request->hasFile('profile_photo')) {
            $profilePhotoPath = $request->file('profile_photo')->store('profile_photos/students', 'public');
        }

        // Buat akun di tabel users
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make('password'), // Password default
            'role' => 'student', // Role untuk siswa
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        // Buat data siswa
        $student = Student::create([
            'tenant_id' => auth()->user()->tenant_id,
            'class_id' => $validated['class_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nisn' => $validated['nisn'],
            'dob' => $validated['dob'],
            'gender' => $validated['gender'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'profile_photo' => $profilePhotoPath,
            'user_id' => $user->id,
        ]);

        return response()->json($student, 201);
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, Student $student)
    {
        if ($student->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:students,email,' . $student->id . '|unique:users,email,' . $student->user_id,
            'class_id' => 'nullable|exists:school_classes,id',
            'nisn' => 'nullable|string|unique:students,nisn,' . $student->id,
            'dob' => 'nullable|date',
            'gender' => 'nullable|string',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|max:2048', // Validasi foto profil
        ]);

        // Update foto profil jika ada
        if ($request->hasFile('profile_photo')) {
            // Hapus foto lama jika ada
            if ($student->profile_photo) {
                Storage::disk('public')->delete($student->profile_photo);
            }

            // Simpan foto baru
            $student->profile_photo = $request->file('profile_photo')->store('profile_photos/students', 'public');
        }

        // Update akun user
        $student->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Update data siswa
        $student->update([
            'class_id' => $validated['class_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nisn' => $validated['nisn'],
            'dob' => $validated['dob'],
            'gender' => $validated['gender'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
        ]);

        return response()->json($student);
    }

    /**
     * Remove the specified student.
     */
    public function destroy(Student $student)
    {
        if ($student->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Hapus foto profil jika ada
        if ($student->profile_photo) {
            Storage::disk('public')->delete($student->profile_photo);
        }

        // Hapus akun user
        $student->user->delete();

        // Hapus data siswa
        $student->delete();

        return response()->json(['message' => 'Student deleted successfully']);
    }
}
