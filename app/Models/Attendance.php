<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'schedule_id',
        'student_id',
        'teacher_id',
        'date',
        'status',
        'notes',
    ];

    /**
     * Relationship to Tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Relationship to Schedule
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    /**
     * Relationship to Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Relationship to Teacher
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
