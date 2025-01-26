<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'domain',
    ];

    /**
     * Relationship to Users (Admin)
     */
    public function admin()
    {
        return $this->hasOne(User::class, 'tenant_id')->where('role', 'admin');
    }

    /**
     * Relationship to Users
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
