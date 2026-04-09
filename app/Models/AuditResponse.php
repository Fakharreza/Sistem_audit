<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditResponse extends Model
{
    use HasFactory;

  
    protected $guarded = [];

    protected $casts = [
        'score' => 'float',
    ];

    // Relasi ke Audit
    public function audit()
    {
        return $this->belongsTo(Audit::class);
    }


    public function question()
    {
        return $this->belongsTo(CobitQuestion::class, 'cobit_question_id');
    }
}