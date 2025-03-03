<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\ExamScheduleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Teacher\TeacherScheduleController;
use App\Http\Controllers\Teacher\ProfileController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Parent\AttendancesController;
use App\Http\Controllers\GradeTypeController;
use App\Http\Controllers\BillTypeController;
use App\Http\Controllers\AssignedBillController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ExtracurricularNameController;
use App\Http\Controllers\ExtracurricularMemberController;
use App\Http\Controllers\ExtracurricularGradeController;

Route::middleware('auth:api')->group(function () {
    Route::get('/extracurricular-grades/member/{member_id}', [ExtracurricularGradeController::class, 'index']);
    Route::apiResource('extracurricular-grades', ExtracurricularGradeController::class)->except(['index']);
});


Route::middleware('auth:api')->group(function () {
    Route::apiResource('extracurricular-members', ExtracurricularMemberController::class);
    Route::get('/extracurricular-members/extracurricular/{id}', [ExtracurricularMemberController::class, 'getMembersByExtracurricular']);

});

Route::middleware('auth:api')->group(function () {
    Route::apiResource('extracurricular-names', ExtracurricularNameController::class);
});


Route::middleware('auth:api')->group(function () {
    Route::apiResource('documents', DocumentController::class);
    //documentsByUser
    Route::get('documents/user/{userId}', [DocumentController::class, 'documentsByUser']);
});
  

Route::middleware('auth:api')->group(function () {
    Route::apiResource('document-types', DocumentTypeController::class);
});


Route::post('/chat', [ChatController::class, 'handleHuggingFace']);
Route::post('/chat1', [ChatController::class, 'handleTogether']);
//yay
Route::post('/yay', [ChatController::class, 'handleYay']);
Route::middleware('auth:api')->group(function () {
    Route::apiResource('grade-types', GradeTypeController::class);
});

Route::middleware('auth:api')->group(function () {
    Route::apiResource('bill-types', BillTypeController::class);
});

Route::middleware('auth:api')->group(function () {
    Route::apiResource('assigned-bills', AssignedBillController::class);
    Route::post('assigned-bills/{assignedBill}', [AssignedBillController::class, 'update']);
});
Route::middleware('auth:api')->group(function () {
    Route::get('assigned-bills/student/{studentId}', [AssignedBillController::class, 'getStudentBills']);
});




//parent, prefix attendance
Route::middleware('auth:api')->prefix('parent_area')->group(function () {
    //list anak 
    Route::get('students', [AttendancesController::class, 'students']);
    //presence/id
    Route::get('presence/{studentId}', [AttendancesController::class, 'getStudentAttendance']);
    //student detail
    Route::get('student/{studentId}', [AttendancesController::class, 'getStudentDetail']);
});











/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//parent login 
Route::post('parent-login', [AuthController::class, 'parentLogin']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//prefix auth
Route::prefix('auth')->group(function () {
    Route::post('register-tenant', [AuthController::class, 'registerTenant']);
    Route::post('register-user', [AuthController::class, 'registerUser']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    //me
    Route::get('me', [AuthController::class, 'me']);
});


Route::middleware('auth:api')->group(function () {
    Route::apiResource('academic-years', AcademicYearController::class);
});


Route::middleware('auth:api')->group(function () {
    Route::apiResource('school-classes', SchoolClassController::class);
});


Route::middleware('auth:api')->group(function () {
    Route::apiResource('teachers', TeacherController::class);
    Route::post('teachers/{teacher}', [TeacherController::class, 'update']);
    
});


Route::middleware('auth:api')->group(function () {
    Route::apiResource('students', StudentController::class);
    Route::post('students/{student}', [StudentController::class, 'update']);
});


Route::middleware('auth:api')->group(function () {
    Route::apiResource('guardians', GuardianController::class);
    Route::post('guardians/{guardian}', [GuardianController::class, 'update']);

});


Route::middleware('auth:api')->group(function () {
    Route::apiResource('subjects', SubjectController::class);
});


Route::middleware('auth:api')->group(function () {
    Route::apiResource('schedules', ScheduleController::class);
});

Route::middleware('auth:api')->group(function () {
    Route::apiResource('attendances', AttendanceController::class);
    Route::get('attendance/by-class', [AttendanceController::class, 'attendanceByClass']);
    Route::get('attendance/by-period', [AttendanceController::class, 'attendanceByPeriod']);
    Route::get('attendance/daily-summary', [AttendanceController::class, 'dailyAttendanceSummary']);
    Route::get('attendance/student-summary', [AttendanceController::class, 'studentAttendanceSummary']);
    Route::get('attendance/teacher-summary', [AttendanceController::class, 'teacherAttendanceSummary']);
});

Route::middleware('auth:api')->group(function () {
    Route::apiResource('grades', GradeController::class);
});

Route::middleware('auth:api')->group(function () {
    Route::apiResource('exam-schedules', ExamScheduleController::class);
});

Route::middleware('auth:api')->group(function () {
    Route::get('dashboard/statistics', [DashboardController::class, 'getStatistics']);
});

Route::middleware('auth:api')->group(function () {
    Route::get('users', [UserController::class, 'index']);
    Route::post('users', [UserController::class, 'store']);
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::put('users/{user}', [UserController::class, 'update']);
    Route::post('users/{id}/reset-password', [UserController::class, 'resetPassword']);
    Route::delete('users/{user}', [UserController::class, 'destroy']);
});

Route::middleware('auth:api')->prefix('teacher')->group(function () {
    Route::get('schedules', [TeacherScheduleController::class, 'index']);
    Route::get('my-profile', [ProfileController::class, 'myProfile']);

});







