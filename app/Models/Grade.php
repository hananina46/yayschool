<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'student_id',
        'subject_id',
        'grade_type_id',
        'class_id',
        'academic_year_id',
        'type',
        'score',
        'remarks',
    ];

    /**
     * Relationship to Tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Relationship to Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relationship to Subject
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Relationship to Class
     */
    public function class()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function gradeType()
    {
        return $this->belongsTo(GradeType::class);
    }

    //relationship to AcademicYear
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
