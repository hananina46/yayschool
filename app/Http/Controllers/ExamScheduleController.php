<?php

namespace App\Http\Controllers;

use App\Models\ExamSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExamScheduleController extends Controller
{
    /**
     * Display a listing of the exam schedules.
     */
    public function index()
    {
        $examSchedules = ExamSchedule::where('tenant_id', auth()->user()->tenant_id)
            ->with(['class', 'subject'])
            ->get();

        return response()->json($examSchedules);
    }

    /**
     * Store a newly created exam schedule.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'exam_type' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $examSchedule = ExamSchedule::create([
            'tenant_id' => auth()->user()->tenant_id,
            'class_id' => $validated['class_id'],
            'subject_id' => $validated['subject_id'],
            'exam_date' => $validated['exam_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'exam_type' => $validated['exam_type'],
            'notes' => $validated['notes'],
        ]);

        return response()->json($examSchedule, 201);
    }

    /**
     * Show the specified exam schedule.
     */
    public function show($id)
    {
        $examSchedule = ExamSchedule::where('tenant_id', auth()->user()->tenant_id)
            ->with(['class', 'subject'])
            ->find($id);

        if (!$examSchedule) {
            return response()->json(['error' => 'Exam schedule not found'], 404);
        }

        return response()->json($examSchedule);
    }

    /**
     * Update the specified exam schedule.
     */
    public function update(Request $request, ExamSchedule $examSchedule)
    {
        if ($examSchedule->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'exam_type' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $examSchedule->update($validated);

        return response()->json($examSchedule);
    }

    /**
     * Remove the specified exam schedule.
     */
    public function destroy(ExamSchedule $examSchedule)
    {
        if ($examSchedule->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $examSchedule->delete();

        return response()->json(['message' => 'Exam schedule deleted successfully']);
    }
}
