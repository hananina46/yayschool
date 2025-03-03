<?php

namespace App\Http\Controllers;

use App\Models\ExtracurricularGrade;
use Illuminate\Http\Request;

class ExtracurricularGradeController extends Controller
{
    // Menampilkan semua nilai berdasarkan member_id, with member dan extracurricularName
    public function index($member_id)
    {
        $grades = ExtracurricularGrade::where('member_id', $member_id)
            ->with('member.student', 'member.extracurricular')  
            ->get();

        return response()->json($grades);
    }

    // Menambahkan nilai untuk anggota ekstrakurikuler
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:extracurricular_members,id',
            'grade_name' => 'required|string|max:255',
            'grade' => 'required|numeric|min:0|max:100',
            'note' => 'nullable|string'
        ]);

        $grade = ExtracurricularGrade::create($validated);

        return response()->json([
            'message' => 'Grade added successfully',
            'data' => $grade
        ], 201);
    }

    // Menampilkan detail nilai berdasarkan ID
    public function show(ExtracurricularGrade $extracurricularGrade)
    {
        return response()->json($extracurricularGrade);
    }

    // Mengupdate nilai anggota ekstrakurikuler
    public function update(Request $request, ExtracurricularGrade $extracurricularGrade)
    {
        $validated = $request->validate([
            'grade_name' => 'required|string|max:255',
            'grade' => 'required|numeric|min:0|max:100',
            'note' => 'nullable|string'
        ]);

        $extracurricularGrade->update($validated);

        return response()->json([
            'message' => 'Grade updated successfully',
            'data' => $extracurricularGrade
        ]);
    }

    // Menghapus nilai anggota ekstrakurikuler
    public function destroy(ExtracurricularGrade $extracurricularGrade)
    {
        $extracurricularGrade->delete();

        return response()->json(['message' => 'Grade deleted successfully']);
    }
}
