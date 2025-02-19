<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    /**
     * Display a listing of the classes.
     */
    public function index()
{
    $schoolClasses = SchoolClass::where('tenant_id', auth()->user()->tenant_id)
        ->with([
            'academicYear', 
            'teacher', 
            'schedules.subject', 
            'schedules.teacher', 
            'students' // Tambahkan relasi 'students' di sini
        ])
        ->get();

    return response()->json($schoolClasses);
}


    /**
     * Store a newly created class.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'teacher_id' => 'nullable|exists:users,id',
        ]);

        $schoolClass = SchoolClass::create([
            'tenant_id' => auth()->user()->tenant_id,
            'academic_year_id' => $validated['academic_year_id'],
            'name' => $validated['name'],
            'teacher_id' => $request->input('teacher_id'),
        ]);

        return response()->json($schoolClass, 201);
    }

    /**
     * Display the specified class.
     */
    public function show(SchoolClass $schoolClass)
    {
        if ($schoolClass->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
        return response()->json($schoolClass->load(['academicYear', 'teacher', 'schedules.subject', 'schedules.teacher', 'students']));
    }    

    /**
     * Update the specified class.
     */
    public function update(Request $request, SchoolClass $schoolClass)
    {
        if ($schoolClass->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'teacher_id' => 'nullable|exists:users,id',
        ]);

        $schoolClass->update([
            'academic_year_id' => $validated['academic_year_id'],
            'name' => $validated['name'],
            'teacher_id' => $request->input('teacher_id'),
        ]);

        return response()->json($schoolClass);
    }

    /**
     * Remove the specified class.
     */
    public function destroy(SchoolClass $schoolClass)
    {
        if ($schoolClass->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $schoolClass->delete();

        return response()->json(['message' => 'Class deleted successfully']);
    }
}
