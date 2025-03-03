<?php

namespace App\Http\Controllers;

use App\Models\ExtracurricularName;
use Illuminate\Http\Request;

class ExtracurricularNameController extends Controller
{
    // Tampilkan semua ekstrakurikuler, with supervisor and with students
    public function index()
    {
        $extracurriculars = ExtracurricularName::with('supervisor')->where('tenant_id', auth()->user()->tenant_id)->get();
        return response()->json($extracurriculars);
    }

    // Tambah ekstrakurikuler baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'supervisor_id' => 'required|exists:teacher,id', // Guru/Pengawas wajib ada
        ]);

        $extracurricular = ExtracurricularName::create([
            'tenant_id' => auth()->user()->tenant_id, // Tenant ID otomatis dari user
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'supervisor_id' => $validated['supervisor_id'],
        ]);

        return response()->json([
            'message' => 'Extracurricular created successfully',
            'data' => $extracurricular
        ], 201);
    }

    // Lihat detail ekstrakurikuler, with supervisor
    public function show(ExtracurricularName $extracurricularName)
    {
        $extracurricularName->load('supervisor');
        return response()->json($extracurricularName);
    }

    // Update ekstrakurikuler
    public function update(Request $request, ExtracurricularName $extracurricularName)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'supervisor_id' => 'required|exists:users,id',
        ]);

        $extracurricularName->update($validated);

        return response()->json([
            'message' => 'Extracurricular updated successfully',
            'data' => $extracurricularName
        ]);
    }

    // Hapus ekstrakurikuler
    public function destroy(ExtracurricularName $extracurricularName)
    {
        $extracurricularName->delete();

        return response()->json(['message' => 'Extracurricular deleted successfully']);
    }
}
