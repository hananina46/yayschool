<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // for generating random strings

class DocumentController extends Controller
{
    /**
     * Store a newly created document.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document_type_id' => 'required|exists:document_types,id',
            'file' => 'required|file|max:10240', // Maksimal file 10MB
            'note' => 'nullable|string',
            'user_id' => 'required|exists:users,id', // Pastikan user_id ada di body request
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Simpan file
        $file = $request->file('file');
        $date = now()->format('dmY'); // Current date in ddmmyyyy format
        $randomString = Str::random(5); // Generate 5 random characters
        $filename = $date . $randomString . '.' . $file->getClientOriginalExtension(); // Create the new file name

        // Move the file to the documents directory with the new name
        $path = $file->move(public_path('documents'), $filename);

        // Create the document entry in the database
        $document = Document::create([
            'tenant_id' => auth()->user()->tenant_id,
            'user_id' => $request->user_id,  // Dapatkan user_id dari request body
            'document_type_id' => $request->document_type_id,
            'path' => 'documents/' . $filename, // Store relative path
            'note' => $request->note,
        ]);

        return response()->json($document, 201);
    }

    /**
     * Update the specified document.
     */
    public function update(Request $request, Document $document)
    {
        if ($document->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'document_type_id' => 'required|exists:document_types,id',
            'note' => 'nullable|string',
            'user_id' => 'required|exists:users,id',  // Pastikan user_id ada di body request
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('file')) {
            // Simpan file baru
            $file = $request->file('file');
            $date = now()->format('dmY'); // Current date in ddmmyyyy format
            $randomString = Str::random(5); // Generate 5 random characters
            $filename = $date . $randomString . '.' . $file->getClientOriginalExtension(); // Create the new file name

            // Hapus file lama jika ada
            if ($document->path) {
                // Delete the old file if it exists
                Storage::delete($document->path);
            }

            // Move the new file to the documents directory with the new name
            $path = $file->move(public_path('documents'), $filename);

            $document->path = 'documents/' . $filename; // Update the file path
        }

        $document->update([
            'document_type_id' => $request->document_type_id,
            'note' => $request->note,
            'user_id' => $request->user_id,  // Update user_id dengan data dari request body
        ]);

        return response()->json($document);
    }

    /**
     * Remove the specified document.
     */
    public function destroy(Document $document)
    {
        if ($document->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Hapus file
        Storage::delete($document->path);

        $document->delete();

        return response()->json(['message' => 'Document deleted successfully']);
    }

    // documents by user_id
    public function documentsByUser($user_id)
    {
        //with user and document type
        $documents = Document::where('user_id', $user_id)->with('user', 'documentType')->get();
        return response()->json($documents);
    }
}
