<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtracurricularName extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'supervisor_id' // Guru/Pengawas yang bertanggung jawab
    ];

    // Relasi ke Tenant
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // Relasi ke User sebagai Pengawas/Guru
    public function supervisor()
    {
        return $this->belongsTo(Teacher::class);
    }

    // Relasi ke anggota ekstrakurikuler
    public function members()
    {
        return $this->hasMany(ExtracurricularMember::class);
    }
}
