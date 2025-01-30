<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the schedules.
     */
    public function index()
    {
        $schedules = Schedule::where('tenant_id', auth()->user()->tenant_id)
            ->with(['class', 'subject', 'teacher'])
            ->get();

        return response()->json($schedules);
    }

    /**
     * Store a newly created schedule.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'day' => 'required|string|max:20',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $schedule = Schedule::create([
            'tenant_id' => auth()->user()->tenant_id,
            'class_id' => $validated['class_id'],
            'subject_id' => $validated['subject_id'],
            'teacher_id' => $validated['teacher_id'],
            'day' => $validated['day'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],

        ]);

        return response()->json($schedule, 201);
    }

    /**
     * Show the specified schedule.
     */
    public function show($id)
    {
        $schedule = Schedule::where('tenant_id', auth()->user()->tenant_id)
            ->with(['class', 'subject', 'teacher'])
            ->find($id);

        if (!$schedule) {
            return response()->json(['error' => 'Schedule not found'], 404);
        }

        return response()->json($schedule);
    }

    /**
     * Update the specified schedule.
     */
    public function update(Request $request, Schedule $schedule)
    {
        if ($schedule->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'day' => 'required|string|max:20',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $schedule->update($validated);

        return response()->json($schedule);
    }

    /**
     * Remove the specified schedule.
     */
    public function destroy(Schedule $schedule)
    {
        if ($schedule->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $schedule->delete();

        return response()->json(['message' => 'Schedule deleted successfully']);
    }
}
