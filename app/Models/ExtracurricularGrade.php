<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtracurricularGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'grade_name',
        'grade',
        'note'
    ];

    // Relasi ke anggota ekstrakurikuler (ExtracurricularMember)
    public function member()
    {
        return $this->belongsTo(ExtracurricularMember::class);
    }

    // Relasi ke nilai ekstrakurikuler (ExtracurricularName)
    public function extracurricularName()
    {
        return $this->belongsTo(ExtracurricularName::class);
    }
}
