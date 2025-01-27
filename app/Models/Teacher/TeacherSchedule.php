<?php

namespace App\Models\Teacher;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherSchedule extends Model
{
    use HasFactory;

    protected $table = 'schedules';

    protected $fillable = [
        'tenant_id',
        'class_id',
        'subject_id',
        'teacher_id',
        'day',
        'start_time',
        'end_time',
        'description',
    ];

    /**
     * Relasi ke kelas
     */
    public function class()
    {
        return $this->belongsTo(\App\Models\SchoolClass::class, 'class_id');
    }

    /**
     * Relasi ke mata pelajaran
     */
    public function subject()
    {
        return $this->belongsTo(\App\Models\Subject::class, 'subject_id');
    }

    /**
     * Relasi ke guru
     */
    public function teacher()
    {
        return $this->belongsTo(\App\Models\User::class, 'teacher_id');
    }
}
