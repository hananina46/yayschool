<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Student;
use App\Models\User;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the attendance records.
     */
    public function index()
    {
        $attendances = Attendance::where('tenant_id', auth()->user()->tenant_id)
            ->with(['schedule', 'student', 'teacher'])
            ->get();

        return response()->json($attendances);
    }

    /**
     * Store a newly created attendance record.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'schedule_id' => 'required|exists:schedules,id',
            'student_id' => 'nullable|exists:students,id',
            'teacher_id' => 'nullable|exists:users,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,sick,permission',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $attendance = Attendance::create([
            'tenant_id' => auth()->user()->tenant_id,
            'schedule_id' => $validated['schedule_id'],
            'student_id' => $validated['student_id'],
            'teacher_id' => $validated['teacher_id'],
            'date' => $validated['date'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);

        return response()->json($attendance, 201);
    }

    /**
     * Show a specific attendance record.
     */
    public function show($id)
    {
        $attendance = Attendance::where('tenant_id', auth()->user()->tenant_id)
            ->with(['schedule', 'student', 'teacher'])
            ->find($id);

        if (!$attendance) {
            return response()->json(['error' => 'Attendance record not found'], 404);
        }

        return response()->json($attendance);
    }

    /**
     * Update a specific attendance record.
     */
    public function update(Request $request, Attendance $attendance)
    {
        if ($attendance->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'schedule_id' => 'required|exists:schedules,id',
            'student_id' => 'nullable|exists:students,id',
            'teacher_id' => 'nullable|exists:users,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,sick,permission',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $attendance->update($validated);

        return response()->json($attendance);
    }

    /**
     * Remove a specific attendance record.
     */
    public function destroy(Attendance $attendance)
    {
        if ($attendance->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $attendance->delete();

        return response()->json(['message' => 'Attendance deleted successfully']);
    }

    public function attendanceByClass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:school_classes,id',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $attendances = Attendance::where('tenant_id', auth()->user()->tenant_id)
            ->where('date', $validated['date'])
            ->whereHas('schedule', function ($query) use ($validated) {
                $query->where('class_id', $validated['class_id']);
            })
            ->with(['schedule.subject', 'student', 'teacher'])
            ->get();

        return response()->json($attendances);
    }

    public function attendanceByPeriod(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:school_classes,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $attendances = Attendance::where('tenant_id', auth()->user()->tenant_id)
            ->whereHas('schedule', function ($query) use ($validated) {
                $query->where('class_id', $validated['class_id']);
            })
            ->whereBetween('date', [$validated['start_date'], $validated['end_date']])
            ->with(['schedule.subject', 'student'])
            ->get();

        $summary = [
            'class_id' => $validated['class_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'attendance_summary' => [
                'present' => $attendances->where('status', 'present')->count(),
                'absent' => $attendances->where('status', 'absent')->count(),
                'sick' => $attendances->where('status', 'sick')->count(),
                'permission' => $attendances->where('status', 'permission')->count(),
            ],
            'details' => $attendances,
        ];

        return response()->json($summary);
    }

    public function dailyAttendanceSummary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        // Ambil data kehadiran berdasarkan tanggal
        $attendances = Attendance::where('tenant_id', auth()->user()->tenant_id)
            ->where('date', $validated['date'])
            ->with(['schedule.class'])
            ->get();

        // Kelompokkan kehadiran berdasarkan kelas
        $classesSummary = $attendances->groupBy('schedule.class_id')->map(function ($attendanceGroup) {
            $class = $attendanceGroup->first()->schedule->class;

            return [
                'class_name' => $class->name,
                'total_students' => $class->students->count(),
                'present' => $attendanceGroup->where('status', 'present')->count(),
                'absent' => $attendanceGroup->where('status', 'absent')->count(),
                'sick' => $attendanceGroup->where('status', 'sick')->count(),
                'permission' => $attendanceGroup->where('status', 'permission')->count(),
            ];
        })->values();

        // Rangkuman data
        $summary = [
            'date' => $validated['date'],
            'classes' => $classesSummary,
        ];

        return response()->json($summary);
    }

    public function studentAttendanceSummary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        // Ambil data siswa
        $student = Student::with('class')->find($validated['student_id']);

        if (!$student || $student->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized or student not found'], 403);
        }

        // Ambil data kehadiran siswa berdasarkan periode
        $attendances = Attendance::where('tenant_id', auth()->user()->tenant_id)
            ->where('student_id', $validated['student_id'])
            ->whereBetween('date', [$validated['start_date'], $validated['end_date']])
            ->with('schedule.subject')
            ->get();

        // Rangkuman data kehadiran
        $summary = [
            'student_name' => $student->name,
            'class_name' => $student->class->name,
            'attendance_summary' => [
                'present' => $attendances->where('status', 'present')->count(),
                'absent' => $attendances->where('status', 'absent')->count(),
                'sick' => $attendances->where('status', 'sick')->count(),
                'permission' => $attendances->where('status', 'permission')->count(),
            ],
            'daily_attendance' => $attendances->map(function ($attendance) {
                return [
                    'date' => $attendance->date,
                    'status' => $attendance->status,
                    'notes' => $attendance->notes,
                    'subject' => $attendance->schedule->subject->name,
                ];
            }),
        ];

        return response()->json($summary);
    }

    public function teacherAttendanceSummary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        // Ambil data guru
        $teacher = User::where('role', 'teacher')->find($validated['teacher_id']);

        if (!$teacher || $teacher->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized or teacher not found'], 403);
        }

        // Ambil data kehadiran guru berdasarkan periode
        $attendances = Attendance::where('tenant_id', auth()->user()->tenant_id)
            ->where('teacher_id', $validated['teacher_id'])
            ->whereBetween('date', [$validated['start_date'], $validated['end_date']])
            ->with(['schedule.subject', 'schedule.class'])
            ->get();

        // Rangkuman data kehadiran
        $summary = [
            'teacher_name' => $teacher->name,
            'attendance_summary' => [
                'present' => $attendances->where('status', 'present')->count(),
                'absent' => $attendances->where('status', 'absent')->count(),
                'sick' => $attendances->where('status', 'sick')->count(),
                'permission' => $attendances->where('status', 'permission')->count(),
            ],
            'daily_attendance' => $attendances->map(function ($attendance) {
                return [
                    'date' => $attendance->date,
                    'status' => $attendance->status,
                    'notes' => $attendance->notes,
                    'class' => $attendance->schedule->class->name,
                    'subject' => $attendance->schedule->subject->name,
                ];
            }),
        ];

        return response()->json($summary);
    }





}
