<?php

namespace App\Http\Controllers;

use App\Models\BillType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BillTypeController extends Controller
{
    /**
     * Display a listing of bill types.
     */
    public function index()
    {
        $billTypes = BillType::where('tenant_id', auth()->user()->tenant_id)->get();

        return response()->json($billTypes);
    }

    /**
     * Store a newly created bill type.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $billType = BillType::create([
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $validated['name'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
        ]);

        return response()->json($billType, 201);
    }

    /**
     * Display the specified bill type.
     */
    public function show($id)
    {
        $billType = BillType::where('tenant_id', auth()->user()->tenant_id)->find($id);

        if (!$billType) {
            return response()->json(['error' => 'Bill type not found'], 404);
        }

        return response()->json($billType);
    }

    /**
     * Update the specified bill type.
     */
    public function update(Request $request, BillType $billType)
    {
        if ($billType->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $billType->update($validated);

        return response()->json($billType);
    }

    /**
     * Remove the specified bill type.
     */
    public function destroy(BillType $billType)
    {
        if ($billType->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $billType->delete();

        return response()->json(['message' => 'Bill type deleted successfully']);
    }
}
