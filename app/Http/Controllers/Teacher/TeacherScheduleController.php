<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller; // Pastikan Controller diimpor
use App\Models\Teacher\TeacherSchedule;
use Illuminate\Http\Request;

class TeacherScheduleController extends Controller
{
    /**
     * Tampilkan jadwal pelajaran untuk guru yang sedang login.
     */
    public function index(Request $request)
    {
        $schedules = TeacherSchedule::where('tenant_id', auth()->user()->tenant_id)
            ->where('teacher_id', auth()->user()->id)
            ->when($request->day, function ($query, $day) {
                $query->where('day', $day);
            })
            ->with(['class', 'subject'])
            ->get();

        return response()->json($schedules);
    }
}
