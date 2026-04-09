<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CobitQuestion extends Model
{
    use HasFactory;
   protected $guarded = [];

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function responses()
    {
        return $this->hasMany(AuditResponse::class, 'cobit_question_id');
    }
}
