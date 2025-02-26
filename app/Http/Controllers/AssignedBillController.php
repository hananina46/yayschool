<?php

namespace App\Http\Controllers;

use App\Models\AssignedBill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssignedBillController extends Controller
{
    /**
     * Display a listing of assigned bills.
     */

     /**
 * Display all assigned bills for a specific student.
 */
    public function getStudentBills($studentId)
    {
        $assignedBills = AssignedBill::where('student_id', $studentId)
            ->where('tenant_id', auth()->user()->tenant_id) // Memastikan hanya tenant yang memiliki data bisa melihat
            ->with(['academicYear', 'billType'])
            ->get();

        if ($assignedBills->isEmpty()) {
            return response()->json(['message' => 'No assigned bills found for this student'], 404);
        }

        return response()->json($assignedBills);
    }

    public function index()
    {
        $assignedBills = AssignedBill::where('tenant_id', auth()->user()->tenant_id)
            ->with(['student', 'academicYear', 'billType'])
            ->get();

        return response()->json($assignedBills);
    }

    /**
     * Store a newly created assigned bill.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'bill_type_id' => 'required|exists:bill_types,id',
            'status' => 'required|in:pending,paid,overdue',
            'discount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
            'payment_method' => 'nullable|in:VA,manual_transfer,gift_card,credit_card',
            'payment_url' => 'nullable|string',
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Validasi file
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $paymentProofPath = null;

        // Jika ada file yang diupload, simpan di `public/payment_proof/`
        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $fileName = Carbon::now()->format('dmYHis') . Str::random(5) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('payment_proof/');
            $file->move($destinationPath, $fileName);
            $paymentProofPath = 'payment_proof/' . $fileName;
        }

        $assignedBill = AssignedBill::create([
            'tenant_id' => auth()->user()->tenant_id,
            'student_id' => $request->student_id,
            'academic_year_id' => $request->academic_year_id,
            'bill_type_id' => $request->bill_type_id,
            'status' => $request->status,
            'discount' => $request->discount,
            'note' => $request->note,
            'payment_method' => $request->payment_method,
            'payment_url' => $request->payment_url,
            'payment_proof' => $paymentProofPath,
        ]);

        return response()->json($assignedBill, 201);
    }


    /**
     * Display the specified assigned bill.
     */
    public function show($id)
    {
        $assignedBill = AssignedBill::where('tenant_id', auth()->user()->tenant_id)
            ->with(['student', 'academicYear', 'billType'])
            ->find($id);

        if (!$assignedBill) {
            return response()->json(['error' => 'Assigned bill not found'], 404);
        }

        return response()->json($assignedBill);
    }

    /**
     * Update the specified assigned bill.
     */
    public function update(Request $request, AssignedBill $assignedBill)
    {
        if ($assignedBill->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'bill_type_id' => 'required|exists:bill_types,id',
            'status' => 'required|in:pending,paid,overdue',
            'discount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
            'payment_method' => 'nullable|in:VA,manual_transfer,gift_card,credit_card',
            'payment_url' => 'nullable|string',
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $paymentProofPath = $assignedBill->payment_proof;

        // Jika ada file baru yang diupload, hapus file lama dan simpan yang baru
        if ($request->hasFile('payment_proof')) {
            if ($assignedBill->payment_proof && file_exists(public_path($assignedBill->payment_proof))) {
                unlink(public_path($assignedBill->payment_proof)); // Hapus file lama
            }

            $file = $request->file('payment_proof');
            $fileName = Carbon::now()->format('dmYHis') . Str::random(5) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('payment_proof/');
            $file->move($destinationPath, $fileName);
            $paymentProofPath = 'payment_proof/' . $fileName;
        }

        $assignedBill->update([
            'student_id' => $request->student_id,
            'academic_year_id' => $request->academic_year_id,
            'bill_type_id' => $request->bill_type_id,
            'status' => $request->status,
            'discount' => $request->discount,
            'note' => $request->note,
            'payment_method' => $request->payment_method,
            'payment_url' => $request->payment_url,
            'payment_proof' => $paymentProofPath,
        ]);

        return response()->json($assignedBill);
    }

    /**
     * Remove the specified assigned bill.
     */
    public function destroy(AssignedBill $assignedBill)
    {
        if ($assignedBill->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $assignedBill->delete();

        return response()->json(['message' => 'Assigned bill deleted successfully']);
    }
}
