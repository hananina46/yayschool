<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Teacher\TeacherSchedule;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Menampilkan profil dan data terkait guru yang sedang login.
     */
    public function myProfile(Request $request)
    {
        $user = auth()->user();

        // Pastikan user adalah teacher
        if ($user->role !== 'teacher') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Ambil kelas yang diajar
        $classes = SchoolClass::where('tenant_id', $user->tenant_id)
            ->where('teacher_id', $user->id)
            ->get();

        // Ambil jadwal pelajaran
        $schedules = TeacherSchedule::where('tenant_id', $user->tenant_id)
            ->where('teacher_id', $user->id)
            ->with(['class', 'subject'])
            ->get();

        return response()->json([
            'profile' => $user,
            'classes' => $classes,
            'schedules' => $schedules,
        ]);
    }
}
