<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignedBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'student_id',
        'academic_year_id',
        'bill_type_id',
        'status',
        'discount',
        'note',
        'payment_method',
        'payment_url',
        'payment_proof',
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
     * Relationship to Academic Year
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Relationship to BillType
     */
    public function billType()
    {
        return $this->belongsTo(BillType::class);
    }
}
