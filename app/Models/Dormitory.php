<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dormitory extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'capacity',
        'students',
        'teachers',
    ];

    protected $casts = [
        'students' => 'array',
        'teachers' => 'array',
    ];

    /**
     * Relationship to Tenant
     */
    // public function tenant()
    // {
    //     return $this->belongsTo(Tenant::class);
    // }

    /**
     * Relationship to Students
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'students', 'id', 'id')->whereIn('id', $this->students);
    }

    /**
     * Relationship to Teachers
     */
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teachers', 'id', 'id')->whereIn('id', $this->teachers);
    }
}
