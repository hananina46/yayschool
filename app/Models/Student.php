<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'class_id',
        'name',
        'email',
        'user_id',
        'nisn',
        'dob',
        'gender',
        'phone',
        'address',
        'profile_photo',
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
     * Relationship to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
