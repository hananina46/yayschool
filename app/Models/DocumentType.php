<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'note',
        'max_file',
    ];

    // Relasi ke Tenant
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
