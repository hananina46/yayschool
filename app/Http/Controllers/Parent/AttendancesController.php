<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\Attendance;
use App\Models\Student;

use Illuminate\Http\Request;

class AttendancesController extends Controller
{
    public function students()
    {
        $guardian = Guardian::where('user_id', auth()->id()) // Ambil data guardian berdasarkan user yang login
            ->with('students') // Ambil relasi dengan siswa
            ->first();

        if (!$guardian) {
            return response()->json(['error' => 'Guardian not found or unauthorized'], 403);
        }

        return response()->json($guardian->students);
    }

    public function getStudentAttendance($studentId)
    {
        $guardian = Guardian::where('user_id', auth()->id())
            ->with('students')
            ->first();

        if (!$guardian) {
            return response()->json(['error' => 'Guardian not found or unauthorized'], 403);
        }

        // Periksa apakah studentId termasuk dalam daftar anak dari guardian
        $isChild = $guardian->students->contains('id', $studentId);
        if (!$isChild) {
            return response()->json(['error' => 'Unauthorized access to student data'], 403);
        }

        // Ambil data kehadiran anak berdasarkan student_id
        $attendances = Attendance::where('student_id', $studentId)
            ->with(['schedule.subject', 'schedule.class', 'teacher'])
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($attendances);
    }

    public function getStudentDetail($studentId)
    {
        $guardian = Guardian::where('user_id', auth()->id())
            ->with('students')
            ->first();

        if (!$guardian) {
            return response()->json(['error' => 'Guardian not found or unauthorized'], 403);
        }

        // Periksa apakah studentId termasuk dalam daftar anak dari guardian, with class
        $student = $guardian->students->where('id', $studentId)->first();

        if (!$student) {
            return response()->json(['error' => 'Unauthorized access to student data'], 403);
        }

        //get student with class, 'class.academicYear','user','guardians','grades.subject','grades.class','grades.academicYear','bills.billType','bills.academicYear'
        $student = Student::where('id', $studentId)
            ->with([
                'class.academicYear',
                'user',
                'guardians',
                'grades.subject',
                'grades.class',
                'grades.academicYear',
                'bills.billType',
                'bills.academicYear'
            ])
            ->first();

        return response()->json($student);
    }
}
