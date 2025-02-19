<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeType extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'subject_id',
        'name',
        'description',
        'percentage',
    ];

    /**
     * Relasi ke Subject.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
