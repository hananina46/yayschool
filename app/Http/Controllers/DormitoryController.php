<?php

namespace App\Http\Controllers;

use App\Models\Dormitory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DormitoryController extends Controller
{
    public function index()
    {
        $tenantId = Auth::user()->tenant_id;
        return Dormitory::where('tenant_id', $tenantId)->with(['students', 'teachers'])->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer',
            'students' => 'array',
            'teachers' => 'array',
        ]);

        $tenantId = Auth::user()->tenant_id;

        $dormitory = Dormitory::create([
            'tenant_id' => $tenantId,
            'name' => $request->name,
            'capacity' => $request->capacity,
            'students' => $request->students ?? [],
            'teachers' => $request->teachers ?? [],
        ]);

        return response()->json($dormitory, 201);
    }

    public function show(Dormitory $dormitory)
    {
        if ($dormitory->tenant_id !== Auth::user()->tenant_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($dormitory->load(['students', 'teachers']));
    }

    public function update(Request $request, Dormitory $dormitory)
    {
        if ($dormitory->tenant_id !== Auth::user()->tenant_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'string|max:255',
            'capacity' => 'integer',
            'students' => 'array',
            'teachers' => 'array',
        ]);

        $dormitory->update([
            'name' => $request->name ?? $dormitory->name,
            'capacity' => $request->capacity ?? $dormitory->capacity,
            'students' => $request->students ?? $dormitory->students,
            'teachers' => $request->teachers ?? $dormitory->teachers,
        ]);

        return response()->json($dormitory);
    }

    public function destroy(Dormitory $dormitory)
    {
        if ($dormitory->tenant_id !== Auth::user()->tenant_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $dormitory->delete();

        return response()->json(['message' => 'Dormitory deleted successfully']);
    }
}
