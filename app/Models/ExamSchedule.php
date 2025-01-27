<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'class_id',
        'subject_id',
        'exam_date',
        'start_time',
        'end_time',
        'exam_type',
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
     * Relationship to Class
     */
    public function class()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    /**
     * Relationship to Subject
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
