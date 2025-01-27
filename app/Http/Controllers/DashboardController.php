<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\Schedule;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics.
     */
    public function getStatistics(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        // Statistik jumlah data utama
        $totalStudents = Student::where('tenant_id', $tenantId)->count();
        $totalTeachers = User::where('tenant_id', $tenantId)->where('role', 'teacher')->count();
        $totalClasses = SchoolClass::where('tenant_id', $tenantId)->count();
        $totalSubjects = Subject::where('tenant_id', $tenantId)->count();

        // Statistik kehadiran hari ini
        $today = now()->toDateString();
        $attendanceToday = Attendance::where('tenant_id', $tenantId)
            ->where('date', $today)
            ->get();

        $attendanceStats = [
            'present' => $attendanceToday->where('status', 'present')->count(),
            'absent' => $attendanceToday->where('status', 'absent')->count(),
            'sick' => $attendanceToday->where('status', 'sick')->count(),
            'permission' => $attendanceToday->where('status', 'permission')->count(),
        ];

        // Statistik nilai rata-rata per mata pelajaran
        $averageGrades = Grade::where('tenant_id', $tenantId)
            ->selectRaw('subject_id, AVG(score) as average_score')
            ->groupBy('subject_id')
            ->with('subject')
            ->get();

        // Highlight jadwal hari ini
        $schedulesToday = Schedule::where('tenant_id', $tenantId)
            ->where('day', now()->format('l')) // Nama hari
            ->with(['class', 'subject', 'teacher'])
            ->get();

        return response()->json([
            'total_students' => $totalStudents,
            'total_teachers' => $totalTeachers,
            'total_classes' => $totalClasses,
            'total_subjects' => $totalSubjects,
            'attendance_today' => $attendanceStats,
            'average_grades' => $averageGrades,
            'schedules_today' => $schedulesToday,
        ]);
    }
}
