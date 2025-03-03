<?php

namespace App\Http\Controllers;

use App\Models\ExtracurricularMember;
use Illuminate\Http\Request;

class ExtracurricularMemberController extends Controller
{
    // Menampilkan semua anggota ekstrakurikuler berdasarkan tenant
    public function index()
    {
        $members = ExtracurricularMember::with(['extracurricular', 'student'])
            ->where('tenant_id', auth()->user()->tenant_id)
            ->get();

        return response()->json($members);
    }

    // Menambahkan siswa sebagai anggota ekstrakurikuler
    public function store(Request $request)
    {
        $validated = $request->validate([
            'extracurricular_id' => 'required|exists:extracurricular_names,id',
            'student_id' => 'required|exists:students,id' // Validasi ke tabel students
        ]);

        // Cek apakah siswa sudah terdaftar di ekstrakurikuler ini
        $exists = ExtracurricularMember::where([
            'tenant_id' => auth()->user()->tenant_id,
            'extracurricular_id' => $validated['extracurricular_id'],
            'student_id' => $validated['student_id'],
        ])->exists();

        if ($exists) {
            return response()->json(['message' => 'Student is already a member of this extracurricular'], 409);
        }

        $member = ExtracurricularMember::create([
            'tenant_id' => auth()->user()->tenant_id, // Tenant ID otomatis dari user yang login
            'extracurricular_id' => $validated['extracurricular_id'],
            'student_id' => $validated['student_id']
        ]);

        return response()->json([
            'message' => 'Student successfully added as a member',
            'data' => $member
        ], 201);
    }

    // Menampilkan detail anggota ekstrakurikuler
    public function show(ExtracurricularMember $extracurricularMember)
    {
        return response()->json($extracurricularMember);
    }

    // Menghapus siswa dari ekstrakurikuler (mengeluarkan siswa)
    public function destroy(ExtracurricularMember $extracurricularMember)
    {
        $extracurricularMember->delete();

        return response()->json(['message' => 'Student removed from extracurricular']);
    }

    public function getMembersByExtracurricular($extracurricular_id)
    {
        // Ambil semua siswa berdasarkan extracurricular_id dengan relasi ke tabel students dan extracurricular_names
        $members = ExtracurricularMember::with(['extracurricular', 'student'])
            ->where('extracurricular_id', $extracurricular_id)
            ->get();

        return response()->json($members);
    }

    public function update(Request $request, ExtracurricularMember $extracurricularMember)
{
    $validated = $request->validate([
        'extracurricular_id' => 'required|exists:extracurricular_names,id',
        'student_id' => 'required|exists:students,id'
    ]);

    // Cek apakah update ini menyebabkan duplikasi data
    $exists = ExtracurricularMember::where([
        'tenant_id' => auth()->user()->tenant_id,
        'extracurricular_id' => $validated['extracurricular_id'],
        'student_id' => $validated['student_id'],
    ])->where('id', '!=', $extracurricularMember->id)->exists();

    if ($exists) {
        return response()->json(['message' => 'This student is already a member of this extracurricular'], 409);
    }

    $extracurricularMember->update([
        'extracurricular_id' => $validated['extracurricular_id'],
        'student_id' => $validated['student_id']
    ]);

    return response()->json([
        'message' => 'Membership updated successfully',
        'data' => $extracurricularMember
    ]);
}


}
