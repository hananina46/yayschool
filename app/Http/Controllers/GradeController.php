<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\GradeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GradeController extends Controller
{
    /**
     * Display a listing of the grades.
     */
    public function index()
    {
        $grades = Grade::where('tenant_id', auth()->user()->tenant_id)
            ->with(['student', 'subject', 'class', 'gradeType', 'academicYear'])
            ->get();

        return response()->json($grades);
    }

    /**
     * Store a newly created grade.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:school_classes,id',
            'grade_type_id' => 'nullable|exists:grade_types,id',
            'academic_years' => 'required|exists:academic_years,id',
            'score' => 'required|numeric|min:0|max:100',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $type_name = GradeType::where('id', $request->grade_type_id)->first();
        $type = $type_name ? $type_name->name : null;

        $validated = $validator->validated();

        $grade = Grade::create([
            'tenant_id' => auth()->user()->tenant_id,
            'student_id' => $validated['student_id'],
            'subject_id' => $validated['subject_id'],
            'class_id' => $validated['class_id'],
            'grade_type_id' => $validated['grade_type_id'],
            'academic_year_id' => $validated['academic_years'],
            'type' => $type,
            'score' => $validated['score'],
            'remarks' => $validated['remarks'],
        ]);

        return response()->json($grade, 201);
    }

    /**
     * Show the specified grade.
     */
    public function show($id) 
    {
        $grade = Grade::where('tenant_id', auth()->user()->tenant_id)
            ->with(['student', 'subject', 'class', 'gradeType', 'academicYear'])
            ->find($id);

        if (!$grade) {
            return response()->json(['error' => 'Grade not found'], 404);
        }

        return response()->json($grade);
    }

    /**
     * Update the specified grade.
     */
    public function update(Request $request, Grade $grade)
    {
        if ($grade->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:school_classes,id',
            'grade_type_id' => 'nullable|exists:grade_types,id',
            'academic_years' => 'required|exists:academic_years,id',
            'score' => 'required|numeric|min:0|max:100',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $type_name = GradeType::where('id', $request->grade_type_id)->first();
        $type = $type_name ? $type_name->name : null;

        $validated = $validator->validated();

        $grade->update([
            'student_id' => $validated['student_id'],
            'subject_id' => $validated['subject_id'],
            'class_id' => $validated['class_id'],
            'grade_type_id' => $validated['grade_type_id'],
            'academic_year_id' => $validated['academic_years'],
            'type' => $type,
            'score' => $validated['score'],
            'remarks' => $validated['remarks'],
        ]);

        return response()->json($grade);
    }

    /**
     * Remove the specified grade.
     */
    public function destroy(Grade $grade)
    {
        if ($grade->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $grade->delete();

        return response()->json(['message' => 'Grade deleted successfully']);
    }
}
