<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'document_type_id',
        'path',
        'note',
    ];

    // Relasi ke Tenant
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke DocumentType
    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }
}
