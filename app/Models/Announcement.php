<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'user_announcer',
        'users_announced',
        'user_id',
        'message',
    ];

    // Relasi ke Tenant
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // Relasi ke User yang membuat pengumuman
    public function announcer()
    {
        return $this->belongsTo(User::class, 'user_announcer');
    }

    // Relasi ke User jika `users_announced = others`
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
