<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DocumentTypeController extends Controller
{
    /**
     * Display a listing of the document types.
     */
    public function index()
    {
        $documentTypes = DocumentType::where('tenant_id', auth()->user()->tenant_id)->get();
        return response()->json($documentTypes);
    }

    /**
     * Store a newly created document type.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'note' => 'nullable|string',
            'max_file' => 'integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $documentType = DocumentType::create([
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $request->name,
            'note' => $request->note,
            'max_file' => $request->max_file ?? 1000,
        ]);

        return response()->json($documentType, 201);
    }

    /**
     * Display the specified document type.
     */
    public function show(DocumentType $documentType)
    {
        if ($documentType->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($documentType);
    }

    /**
     * Update the specified document type.
     */
    public function update(Request $request, DocumentType $documentType)
    {
        if ($documentType->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'note' => 'nullable|string',
            'max_file' => 'integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $documentType->update([
            'name' => $request->name,
            'note' => $request->note,
            'max_file' => $request->max_file ?? 1000,
        ]);

        return response()->json($documentType);
    }

    /**
     * Remove the specified document type.
     */
    public function destroy(DocumentType $documentType)
    {
        if ($documentType->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $documentType->delete();

        return response()->json(['message' => 'Document type deleted successfully']);
    }
}
