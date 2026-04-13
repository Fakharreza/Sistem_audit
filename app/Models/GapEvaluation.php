<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GapEvaluation extends Model
{
   protected $guarded = [];
    public function auditResponse()
    {
        return $this->belongsTo(AuditResponse::class);
    }
    public function criterion() {
       return $this->belongsTo(Criterion::class)->withTrashed();

    }
    
}
