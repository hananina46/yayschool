<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{
    /**
     * Display a listing of the subjects.
     */
    public function index()
    {
        $subjects = Subject::where('tenant_id', auth()->user()->tenant_id)->get();

        return response()->json($subjects);
    }

    /**
     * Store a newly created subject.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $subject = Subject::create([
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'],
        ]);

        return response()->json($subject, 201);
    }

    /**
     * Show the specified subject.
     */
    public function show($id)
    {
        $subject = Subject::where('tenant_id', auth()->user()->tenant_id)->find($id);

        if (!$subject) {
            return response()->json(['error' => 'Subject not found'], 404);
        }

        return response()->json($subject);
    }

    /**
     * Update the specified subject.
     */
    public function update(Request $request, Subject $subject)
    {
        if ($subject->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code,' . $subject->id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $subject->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'],
        ]);

        return response()->json($subject);
    }

    /**
     * Remove the specified subject.
     */
    public function destroy(Subject $subject)
    {
        if ($subject->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $subject->delete();

        return response()->json(['message' => 'Subject deleted successfully']);
    }
}
