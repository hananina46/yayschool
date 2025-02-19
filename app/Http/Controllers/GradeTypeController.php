<?php

namespace App\Http\Controllers;

use App\Models\GradeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GradeTypeController extends Controller
{
    /**
     * Display a listing of grade types.
     */
    public function index()
    {
        $gradeTypes = GradeType::where('tenant_id', auth()->user()->tenant_id)
            ->with('subject')
            ->get();

        return response()->json($gradeTypes);
    }

    /**
     * Store a newly created grade type.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'percentage' => 'required|numeric|min:0|max:100', // Validasi persentase
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $gradeType = GradeType::create([
            'tenant_id' => auth()->user()->tenant_id,
            'subject_id' => $validated['subject_id'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'percentage' => $validated['percentage'],
        ]);

        return response()->json($gradeType, 201);
    }

    /**
     * Display the specified grade type.
     */
    public function show($id)
    {
        $gradeType = GradeType::where('tenant_id', auth()->user()->tenant_id)
            ->with('subject')
            ->find($id);

        if (!$gradeType) {
            return response()->json(['error' => 'Grade type not found'], 404);
        }

        return response()->json($gradeType);
    }

    /**
     * Update the specified grade type.
     */
    public function update(Request $request, GradeType $gradeType)
    {
        if ($gradeType->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'percentage' => 'required|numeric|min:0|max:100', // Validasi persentase
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $gradeType->update($validated);

        return response()->json($gradeType);
    }

    /**
     * Remove the specified grade type.
     */
    public function destroy(GradeType $gradeType)
    {
        if ($gradeType->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $gradeType->delete();

        return response()->json(['message' => 'Grade type deleted successfully']);
    }
}
