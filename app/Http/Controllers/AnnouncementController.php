<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the announcements. based on query role teacher or student or parent, if role is undefined, return all announcements. use %like% for user_announced
     */
    public function index(Request $request)
    {
        $announcements = Announcement::where('tenant_id', auth()->user()->tenant_id)
            ->when($request->role, function ($query, $role) {
                return $query->where('users_announced', 'like', "%$role%");
            })
            ->with('announcer', 'user')
            ->get();

        return response()->json($announcements);
    } 

    /**
     * Store a newly created announcement.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'users_announced' => 'required|in:all,teachers,students,parents,others',
            'message' => 'required|string',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->users_announced === 'others' && !$request->user_id) {
            return response()->json(['error' => 'user_id is required when users_announced is others'], 422);
        }

        $announcement = Announcement::create([
            'tenant_id' => auth()->user()->tenant_id,
            'user_announcer' => auth()->id(),
            'users_announced' => $request->users_announced,
            'user_id' => $request->user_id,
            'message' => $request->message,
        ]);

        return response()->json($announcement, 201);
    }

    /**
     * Display the specified announcement.
     */
    public function show(Announcement $announcement)
    {
        if ($announcement->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($announcement);
    }

    /**
     * Update the specified announcement.
     */
    public function update(Request $request, Announcement $announcement)
    {
        if ($announcement->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'users_announced' => 'required|in:all,teachers,students,parents,others',
            'message' => 'required|string',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->users_announced === 'others' && !$request->user_id) {
            return response()->json(['error' => 'user_id is required when users_announced is others'], 422);
        }

        $announcement->update([
            'users_announced' => $request->users_announced,
            'user_id' => $request->user_id,
            'message' => $request->message,
        ]);

        return response()->json($announcement);
    }

    /**
     * Remove the specified announcement.
     */
    public function destroy(Announcement $announcement)
    {
        if ($announcement->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $announcement->delete();

        return response()->json(['message' => 'Announcement deleted successfully']);
    }
}
