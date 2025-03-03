<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtracurricularMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'extracurricular_id',
        'student_id'
    ];

    // Relasi ke Tenant
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // Relasi ke Extracurricular
    public function extracurricular()
    {
        return $this->belongsTo(ExtracurricularName::class);
    }

    // Relasi ke Student dari tabel students
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
