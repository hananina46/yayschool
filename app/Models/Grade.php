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
        'class_id',
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
}
